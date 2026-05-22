<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ trans('culqi::app.checkout.heading') }}</title>
    <style>
        body { font-family: system-ui, -apple-system, sans-serif; background: #f7f7f7; margin: 0; padding: 40px 20px; }
        .wrap { max-width: 480px; margin: 0 auto; background: white; padding: 32px; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        h1 { margin: 0 0 8px; font-size: 22px; }
        p.amount { color: #555; margin: 0 0 24px; }
        .total { font-size: 28px; font-weight: 700; color: #111; }
        button { width: 100%; padding: 14px 20px; font-size: 16px; border: 0; border-radius: 8px; cursor: pointer; font-weight: 600; }
        .pay { background: #1976d2; color: white; }
        .pay:hover { background: #1565c0; }
        .cancel { background: transparent; color: #666; margin-top: 12px; }
        .error { background: #fee; color: #c33; padding: 12px; border-radius: 6px; margin-top: 16px; display: none; }
        .processing { display: none; text-align: center; color: #666; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="wrap">
        <h1>{{ trans('culqi::app.checkout.heading') }}</h1>
        <p class="amount">{{ trans('culqi::app.checkout.total-label') }}</p>
        <div class="total">{{ core()->formatBasePrice($cart->base_grand_total) }}</div>

        <p class="amount" style="margin-top: 24px;">Email: <strong>{{ $cart->customer_email }}</strong></p>

        <button class="pay" id="pay-btn" onclick="openCulqi()">
            {{ trans('culqi::app.checkout.pay-button', ['amount' => core()->formatBasePrice($cart->base_grand_total)]) }}
        </button>

        <a href="{{ route('culqi.payment.cancel') }}">
            <button type="button" class="cancel">{{ trans('culqi::app.checkout.cancel') }}</button>
        </a>

        <div class="error" id="error-box"></div>
        <div class="processing" id="processing">{{ trans('culqi::app.checkout.processing') }}</div>
    </div>

    <script src="https://checkout.culqi.com/js/v4"></script>
    <script>
        Culqi.publicKey = @json($publicKey);

        function openCulqi() {
            Culqi.settings({
                title:    @json(config('app.name')),
                currency: @json(in_array(strtoupper(core()->getBaseCurrencyCode()), ['PEN', 'USD']) ? strtoupper(core()->getBaseCurrencyCode()) : 'PEN'),
                amount:   {{ (int) round($cart->base_grand_total * 100) }},
                order:    @json((string) $cart->id),
            });

            Culqi.options({
                lang: @json(app()->getLocale() === 'es' ? 'es' : 'auto'),
                installments: false,
                paymentMethods: { tarjeta: true, yape: false, bancaMovil: false, agente: false, billetera: false, cuotealo: false },
            });

            Culqi.open();
        }

        function culqi() {
            if (Culqi.token) {
                document.getElementById('pay-btn').disabled = true;
                document.getElementById('processing').style.display = 'block';
                document.getElementById('error-box').style.display = 'none';

                fetch(@json(route('culqi.charge')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ token: Culqi.token.id }),
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = data.redirect_url;
                    } else {
                        showError(data.message || 'Error');
                    }
                })
                .catch(err => showError(err.message));
            } else {
                showError(Culqi.error.user_message || Culqi.error.merchant_message || 'Error');
            }
        }

        function showError(msg) {
            document.getElementById('pay-btn').disabled = false;
            document.getElementById('processing').style.display = 'none';
            const box = document.getElementById('error-box');
            box.textContent = msg;
            box.style.display = 'block';
        }
    </script>
</body>
</html>
