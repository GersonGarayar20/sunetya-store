<?php

return [
    'title'       => 'Culqi',
    'description' => 'Pague de forma segura con su tarjeta de crédito/débito vía Culqi.',

    'fields' => [
        'public-key'      => 'Llave Pública',
        'secret-key'      => 'Llave Secreta',
        'test-public-key' => 'Llave Pública de Prueba',
        'test-secret-key' => 'Llave Secreta de Prueba',
    ],

    'response' => [
        'cart-not-found'      => 'Carrito no encontrado o inválido.',
        'cart-processed'      => 'Este carrito ya ha sido procesado.',
        'invalid-token'       => 'Token de Culqi inválido.',
        'payment-cancelled'   => 'El pago fue cancelado.',
        'payment-failed'      => 'Error en el pago.',
        'payment-success'     => 'Pago completado exitosamente.',
        'provide-credentials' => 'Por favor proporcione credenciales de Culqi válidas.',
        'webhook-invalid'     => 'Webhook de Culqi rechazado: la verificación de firma falló.',
    ],

    'checkout' => [
        'heading'    => 'Pagar con Culqi',
        'pay-button' => 'Pagar :amount',
        'cancel'     => 'Cancelar',
        'processing' => 'Procesando pago...',
    ],
];
