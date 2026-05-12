<?php

namespace Webkul\Culqi\Payment;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Webkul\Payment\Payment\Payment;

class Culqi extends Payment
{
    /**
     * Payment method code.
     *
     * @var string
     */
    protected $code = 'culqi';

    /**
     * Culqi charges API endpoint (unified for sandbox & production —
     * the key prefix pk_test/sk_test vs pk_live/sk_live determines mode).
     */
    const CHARGES_URL = 'https://api.culqi.com/v2/charges';

    const EVENTS_URL = 'https://api.culqi.com/v2/events';

    public function getRedirectUrl()
    {
        return route('culqi.standard.redirect');
    }

    public function isAvailable()
    {
        return parent::isAvailable() && $this->hasValidCredentials();
    }

    public function getTitle()
    {
        return $this->getConfigData('title') ?? trans('culqi::app.title');
    }

    public function getDescription()
    {
        return $this->getConfigData('description') ?? trans('culqi::app.description');
    }

    public function getImage()
    {
        $url = $this->getConfigData('image');

        return $url ? Storage::url($url) : bagisto_asset('images/cash-on-delivery.png', 'shop');
    }

    public function getPublicKey(): ?string
    {
        return $this->getConfigData('sandbox')
            ? $this->getConfigData('api_test_public_key')
            : $this->getConfigData('api_public_key');
    }

    public function getSecretKey(): ?string
    {
        return $this->getConfigData('sandbox')
            ? $this->getConfigData('api_test_secret_key')
            : $this->getConfigData('api_secret_key');
    }

    public function hasValidCredentials(): bool
    {
        return ! empty($this->getPublicKey()) && ! empty($this->getSecretKey());
    }

    /**
     * Create a charge against the Culqi API using a token produced
     * by Culqi.js in the customer's browser.
     *
     * Returns the parsed Culqi response on success, or false on failure.
     *
     * @return array|false
     */
    public function createCharge(string $token, $cart)
    {
        // Culqi expects amounts in the smallest currency unit (cents).
        $amountInCents = (int) round($cart->base_grand_total * 100);

        $currency = strtoupper(core()->getBaseCurrencyCode());

        // Culqi supports PEN and USD. Fall back to PEN if neither matches.
        if (! in_array($currency, ['PEN', 'USD'], true)) {
            $currency = 'PEN';
        }

        $response = Http::withToken($this->getSecretKey())
            ->acceptJson()
            ->asJson()
            ->post(self::CHARGES_URL, [
                'amount'        => $amountInCents,
                'currency_code' => $currency,
                'email'         => $cart->customer_email,
                'source_id'     => $token,
                'description'   => 'Order from '.config('app.name'),
                'metadata'      => [
                    'cart_id' => (string) $cart->id,
                ],
            ]);

        if ($response->successful()) {
            return $response->json();
        }

        return false;
    }

    /**
     * Verify the HMAC-SHA256 signature on a Culqi webhook payload.
     * Culqi computes the signature over the raw request body using the secret key.
     */
    public function verifyWebhookSignature(string $rawBody, ?string $signature): bool
    {
        if (! $signature || ! $this->getSecretKey()) {
            return false;
        }

        $expected = hash_hmac('sha256', $rawBody, $this->getSecretKey());

        return hash_equals($expected, $signature);
    }

    /**
     * Fetch an event from the Culqi API by ID. Used as fallback verification when
     * the HMAC signature is missing or unverifiable.
     *
     * @return array|false
     */
    public function fetchEvent(string $eventId)
    {
        $response = Http::withToken($this->getSecretKey())
            ->acceptJson()
            ->get(self::EVENTS_URL.'/'.$eventId);

        return $response->successful() ? $response->json() : false;
    }
}
