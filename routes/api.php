<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->namespace('Api')
    ->name('api.v1.')
    ->group(function () {

        Route::middleware('throttle:' . config('api.rate_limit.sign'))->group(function () {
            Route::post('verificationCodes', 'VerificationCodesController@store')->name('verificationCodes.store');
            Route::post('users', 'UsersController@store')->name('users.store');
        });

        Route::middleware('throttle:' . config('api.rate_limit.access'))->group(function () {

        });
    });
