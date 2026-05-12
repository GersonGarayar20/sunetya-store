<?php

use Illuminate\Support\Facades\Route;
use Webkul\Culqi\Http\Controllers\CulqiController;

Route::controller(CulqiController::class)
    ->middleware('web')
    ->prefix('culqi')
    ->group(function () {
        Route::get('redirect', 'redirect')->name('culqi.standard.redirect');

        Route::post('charge', 'charge')->name('culqi.charge');

        Route::post('webhook', 'webhook')->name('culqi.webhook');

        Route::get('cancel', 'cancel')->name('culqi.payment.cancel');
    });
