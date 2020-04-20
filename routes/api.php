<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->namespace('Api')
    ->name('api.v1.')
    ->group(function () {

        Route::middleware('throttle:' . config('api.rate_limit.sign'))->group(function () {
            Route::post('verificationCodes', 'VerificationCodesController@store')->name('verificationCodes.store');
            Route::post('users', 'UsersController@store')->name('users.store');
            Route::post('captchas', 'CaptchasController@store')->name('captchas.store');
            //第三方登录
            Route::post('socials/{social_type}/authorizations', 'AuthorizationsController@socialStore')
                ->where('social_type', 'weixin')
                ->name('socials.authorization,store');
            // 登录
            Route::post('authorizations', 'AuthorizationsController@store')
                ->name('authorizations.store');

            Route::middleware('auth:api')->group(function () {
                // 刷新token
                Route::put('authorizations/current', 'AuthorizationsController@update')
                    ->name('authorizations.update');
                // 删除token
                Route::delete('authorizations/current', 'AuthorizationsController@destroy')
                    ->name('authorizations.destroy');
            });
        });

        Route::middleware('throttle:' . config('api.rate_limit.access'))->group(function () {
            Route::get('users/{user}', 'UsersController@show')->name('users.show');
            Route::get('categories', 'CategoriesController@index')->name('categories.index');
            Route::middleware('auth:api')->group(function () {
                //当前登录用户信息
                Route::get('user', 'UsersController@me')->name('user.show');
                //编辑个人信息
                Route::patch('user', 'UsersController@update')->name('user.update');
                //上传图片
                Route::post('/images', 'ImagesController@store')->name('images.store');
            });
        });
    });
