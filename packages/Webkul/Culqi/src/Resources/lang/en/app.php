<?php

return [
    'title'       => 'Culqi',
    'description' => 'Pay securely with your credit/debit card via Culqi.',

    'fields' => [
        'public-key'      => 'Public Key',
        'secret-key'      => 'Secret Key',
        'test-public-key' => 'Test Public Key',
        'test-secret-key' => 'Test Secret Key',
    ],

    'response' => [
        'cart-not-found'      => 'Cart not found or invalid.',
        'cart-processed'      => 'This cart has already been processed.',
        'invalid-token'       => 'Invalid Culqi token.',
        'payment-cancelled'   => 'Payment was cancelled.',
        'payment-failed'      => 'Payment failed.',
        'payment-success'     => 'Payment completed successfully.',
        'provide-credentials' => 'Please provide valid Culqi credentials.',
        'webhook-invalid'     => 'Culqi webhook rejected: signature verification failed.',
    ],

    'checkout' => [
        'heading'    => 'Pay with Culqi',
        'pay-button' => 'Pay :amount',
        'cancel'     => 'Cancel',
        'processing' => 'Processing payment...',
    ],
];
