<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CaptchaRequest;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CaptchasController extends Controller
{
    public function store(CaptchaRequest $request, CaptchaBuilder $captchaBuilder)
    {
        $key = 'captcha-' . Str::random(15);
        $phone = $request->phone;

        $captch = $captchaBuilder->build();
        $expiredAt = now()->addMinutes(2);
        Cache::put($key, [
            'phone' => $phone,
            'code' => $captch->getPhrase()
        ], $expiredAt);

        $result = [
            'captcha_key' => $key,
            'expired_at'=> $expiredAt,
            'captcha_image_content' =>$captch->inline(),
        ];

        return response()->json($result,201);
    }
}
