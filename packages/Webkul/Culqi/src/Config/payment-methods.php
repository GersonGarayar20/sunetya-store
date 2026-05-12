<?php

use Webkul\Culqi\Payment\Culqi;

return [
    'culqi' => [
        'class'       => Culqi::class,
        'code'        => 'culqi',
        'title'       => 'Culqi',
        'description' => 'Pago con tarjeta vía Culqi',
        'active'      => true,
        'sandbox'     => true,
        'sort'        => 3,
    ],
];
