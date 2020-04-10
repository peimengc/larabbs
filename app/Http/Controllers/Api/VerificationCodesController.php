<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\VerificationCodeRequest;
use Illuminate\Support\Str;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;

class VerificationCodesController extends Controller
{
    public function store(VerificationCodeRequest $request, EasySms $easySms)
    {
        $phone = $request->phone;
        $code = $this->getCode($easySms, $phone);
        //缓存key
        $key = 'verificationCode_' . Str::random(15);
        $expiredAt = now()->addMinutes(5);
        //缓存验证码五分钟
        cache()->put($key, compact('phone', 'code'), $expiredAt);

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
