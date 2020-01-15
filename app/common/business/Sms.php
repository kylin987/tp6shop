<?php

declare(strict_types=1);
namespace app\common\business;

use app\common\lib\sms\AliSms;

/**
 * 发送短信业务逻辑
 */
class Sms
{
    
    public static function sendCode(string $phoneNumber) :bool{
        //生成短信验证码
        $code = rand(100000,999999);
        $sms = AliSms::sendCode($phoneNumber, $code);
        if ($sms) {
            //需要把短信验证码记录到Redis，并且给出一个失效时间
        }

        return $sms;
    }
}