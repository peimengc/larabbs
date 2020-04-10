<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->namespace('Api')->name('api.v1.')->group(function () {
    Route::post('verificationCodes','VerificationCodesController@store')->name('verificationCodes.store');
});
