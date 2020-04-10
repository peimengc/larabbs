<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\VerificationCodeRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;

class VerificationCodesController extends Controller
{
    public function store(VerificationCodeRequest $request, EasySms $easySms)
    {
        $captchaData = Cache::get($request->captcha_key);

        if (!$captchaData) {
            abort('验证码已失效');
        }

        if (!hash_equals($request->captcha_code, $captchaData['code'])) {
            //清除缓存
            Cache::forget($request->captch_key);
            throw new AuthenticationException('验证码错误');
        }

        $phone = $captchaData['phone'];

        $code = $this->getCode($easySms, $phone);
        //缓存key
        $key = 'verificationCode_' . Str::random(15);
        $expiredAt = now()->addMinutes(5);
        //缓存验证码五分钟
        Cache::put($key, compact('phone', 'code'), $expiredAt);
        Cache::forget($request->captch_key);

        return response()->json([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString()
        ], 201);
    }

    /**
     * @param EasySms $easySms
     * @param $phone
     * @return string
     * @throws \Exception
     */
    protected function getCode($easySms, $phone)
    {
        if (!app()->environment('production')) {
            return '1234';
        }

        //随机4位数字,左侧补零
        $code = str_pad(random_int(1, 9999), 4, 0, STR_PAD_LEFT);

        try {
            $easySms->send($phone, [
                'template' => config('easysms.gateways.aliyun.templates.register'),
                'data' => [
                    'code' => $code
                ]
            ]);
        } catch (NoGatewayAvailableException $exception) {
            $message = $exception->getException('aliyun')->getMessage();
            abort(500, $message ?: '短信发送异常');
        }

        return $code;

    }
}
