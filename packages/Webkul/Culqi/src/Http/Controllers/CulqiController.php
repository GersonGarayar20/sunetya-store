<?php

namespace Webkul\Culqi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Webkul\Checkout\Facades\Cart;
use Webkul\Checkout\Repositories\CartRepository;
use Webkul\Culqi\Payment\Culqi;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Repositories\OrderTransactionRepository;
use Webkul\Sales\Transformers\OrderResource;

class CulqiController extends Controller
{
    public function __construct(
        protected CartRepository $cartRepository,
        protected OrderRepository $orderRepository,
        protected OrderTransactionRepository $orderTransactionRepository,
        protected InvoiceRepository $invoiceRepository,
        protected Culqi $culqi,
    ) {}

    /**
     * Show the Culqi checkout page that loads Culqi.js and tokenizes the card.
     */
    public function redirect()
    {
        if (! $this->culqi->hasValidCredentials()) {
            session()->flash('error', trans('culqi::app.response.provide-credentials'));

            return redirect()->route('shop.checkout.cart.index');
        }

        $cart = Cart::getCart();

        if (! $cart) {
            session()->flash('error', trans('culqi::app.response.cart-not-found'));

            return redirect()->route('shop.checkout.cart.index');
        }

        return view('culqi::checkout', [
            'cart'      => $cart,
            'publicKey' => $this->culqi->getPublicKey(),
        ]);
    }

    /**
     * Receive the Culqi token from the browser and create the charge + order.
     */
    public function charge(): JsonResponse
    {
        $token = request()->input('token');

        if (! $token) {
            return response()->json([
                'success' => false,
                'message' => trans('culqi::app.response.invalid-token'),
            ], 422);
        }

        $cart = Cart::getCart();

        if (! $cart) {
            return response()->json([
                'success' => false,
                'message' => trans('culqi::app.response.cart-not-found'),
            ], 422);
        }

        $charge = $this->culqi->createCharge($token, $cart);

        if (! $charge || ($charge['outcome']['type'] ?? null) !== 'venta_exitosa') {
            return response()->json([
                'success' => false,
                'message' => $charge['user_message']
                    ?? $charge['merchant_message']
                    ?? trans('culqi::app.response.payment-failed'),
            ], 422);
        }

        try {
            Cart::collectTotals();

            $data = (new OrderResource($cart))->jsonSerialize();

            $data['payment']['additional'] = [
                'culqi_charge_id' => $charge['id'] ?? null,
                'culqi_outcome'   => $charge['outcome']['type'] ?? null,
            ];

            /** @var \Webkul\Sales\Models\Order $order */
            $order = $this->orderRepository->create($data);

            $this->orderRepository->update(['status' => 'processing'], $order->id);

            if ($order->canInvoice()) {
                $invoiceData = ['order_id' => $order->id];

                foreach ($order->items as $item) {
                    $invoiceData['invoice']['items'][$item->id] = $item->qty_to_invoice;
                }

                $invoice = $this->invoiceRepository->create($invoiceData);

                $this->orderTransactionRepository->create([
                    'transaction_id' => $charge['id'] ?? null,
                    'status'         => 'paid',
                    'type'           => $order->payment->method,
                    'payment_method' => $order->payment->method,
                    'order_id'       => $order->id,
                    'invoice_id'     => $invoice->id,
                    'amount'         => $order->base_grand_total,
                    'data'           => json_encode($charge),
                ]);
            }

            Cart::deActivateCart();

            session()->flash('order_id', $order->id);

            return response()->json([
                'success'      => true,
                'redirect_url' => route('shop.checkout.onepage.success'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => trans('culqi::app.response.payment-failed').': '.$e->getMessage(),
            ], 500);
        }
    }

    public function cancel(): RedirectResponse
    {
        session()->flash('error', trans('culqi::app.response.payment-cancelled'));

        return redirect()->route('shop.checkout.cart.index');
    }

    /**
     * Receive a server-to-server webhook from Culqi. Verifies the payload
     * via HMAC signature with fallback to an event re-fetch against the API.
     */
    public function webhook(Request $request): JsonResponse
    {
        $rawBody   = $request->getContent();
        $signature = $request->header('X-Culqi-Signature');
        $payload   = json_decode($rawBody, true);

        if (! is_array($payload)) {
            return response()->json(['error' => 'invalid payload'], 400);
        }

        // Culqi test environment does not send X-Culqi-Signature — skip verification in sandbox.
        // In production, verify HMAC signature with API fallback.
        if (! $this->culqi->isSandbox()) {
            $verified = $this->culqi->verifyWebhookSignature($rawBody, $signature);

            if (! $verified) {
                $eventId = $payload['id'] ?? null;

                if (! $eventId || ! $this->culqi->fetchEvent($eventId)) {
                    Log::warning(trans('culqi::app.response.webhook-invalid'), [
                        'signature_header' => $signature,
                        'event_id'         => $eventId,
                    ]);

                    return response()->json(['error' => 'invalid signature'], 401);
                }
            }
        }

        $type = $payload['type'] ?? null;

        // Culqi puts the object directly under 'data' (not 'data.object' like Stripe).
        // Handle the rare case where Culqi sends 'data' as a JSON string.
        $raw = $payload['data'] ?? [];
        $data = is_string($raw) ? (json_decode($raw, true) ?? []) : $raw;

        Log::info('Culqi webhook received', ['type' => $type, 'data_id' => $data['id'] ?? null]);

        return match ($type) {
            'charge.creation.succeeded' => $this->handleChargeSucceeded($data),
            'charge.creation.failed'    => $this->handleChargeFailed($data),
            'refund.creation.succeeded' => $this->handleRefund($data),
            default                     => response()->json(['ignored' => $type], 200),
        };
    }

    protected function handleChargeSucceeded(array $charge): JsonResponse
    {
        $chargeId = $charge['id'] ?? null;

        if (! $chargeId) {
            return response()->json(['error' => 'missing charge id'], 400);
        }

        $tx = DB::table('order_transactions')->where('transaction_id', $chargeId)->first();

        if (! $tx) {
            // Webhook arrived before the synchronous charge handler created the order.
            return response()->json(['status' => 'no_order_yet'], 200);
        }

        // Idempotency: already paid or refunded — do not overwrite.
        if (in_array($tx->status, ['paid', 'refunded'], true)) {
            return response()->json(['status' => 'already_processed'], 200);
        }

        // Skip if the charge is authorized but not yet captured.
        if (! ($charge['capture'] ?? true)) {
            Log::info('Culqi webhook: charge not yet captured, skipping', ['charge_id' => $chargeId]);

            return response()->json(['status' => 'not_captured'], 200);
        }

        $existing = json_decode($tx->data, true) ?: [];

        DB::table('order_transactions')->where('id', $tx->id)->update([
            'status'     => 'paid',
            'data'       => json_encode(array_merge($existing, ['webhook' => $charge])),
            'updated_at' => now(),
        ]);

        // Ensure the order is in processing state (covers edge cases where sync flow failed).
        if ($tx->order_id) {
            $order = $this->orderRepository->find($tx->order_id);

            if ($order && $order->status === 'pending') {
                $this->orderRepository->update(['status' => 'processing'], $tx->order_id);
            }
        }

        return response()->json(['status' => 'ok'], 200);
    }

    protected function handleChargeFailed(array $charge): JsonResponse
    {
        $chargeId = $charge['id'] ?? null;

        if (! $chargeId) {
            return response()->json(['error' => 'missing charge id'], 400);
        }

        $tx = DB::table('order_transactions')->where('transaction_id', $chargeId)->first();

        if (! $tx) {
            return response()->json(['status' => 'no_order'], 200);
        }

        // Idempotency: already failed or refunded — do not overwrite.
        if (in_array($tx->status, ['failed', 'refunded'], true)) {
            return response()->json(['status' => 'already_processed'], 200);
        }

        // Do not downgrade a successfully paid transaction.
        if ($tx->status === 'paid') {
            Log::warning('Culqi webhook: received charge.failed for already-paid transaction', [
                'transaction_id' => $chargeId,
            ]);

            return response()->json(['status' => 'conflict_ignored'], 200);
        }

        $existing = json_decode($tx->data, true) ?: [];

        DB::table('order_transactions')->where('id', $tx->id)->update([
            'status'     => 'failed',
            'data'       => json_encode(array_merge($existing, ['webhook' => $charge])),
            'updated_at' => now(),
        ]);

        if ($tx->order_id) {
            $this->orderRepository->update(['status' => 'canceled'], $tx->order_id);
        }

        return response()->json(['status' => 'ok'], 200);
    }

    protected function handleRefund(array $refund): JsonResponse
    {
        // Culqi refund payloads include the parent charge id in `charge_id`.
        $chargeId = $refund['charge_id'] ?? ($refund['charge']['id'] ?? null);

        if (! $chargeId) {
            return response()->json(['error' => 'missing charge id'], 400);
        }

        $tx = DB::table('order_transactions')->where('transaction_id', $chargeId)->first();

        if (! $tx) {
            return response()->json(['status' => 'no_order'], 200);
        }

        // Idempotency: already refunded.
        if ($tx->status === 'refunded') {
            return response()->json(['status' => 'already_processed'], 200);
        }

        $existing = json_decode($tx->data, true) ?: [];

        DB::table('order_transactions')->where('id', $tx->id)->update([
            'status'     => 'refunded',
            'data'       => json_encode(array_merge($existing, ['refund_webhook' => $refund])),
            'updated_at' => now(),
        ]);

        return response()->json(['status' => 'ok'], 200);
    }
}
