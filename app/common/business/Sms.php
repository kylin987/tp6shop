<?php

declare(strict_types=1);
namespace app\common\business;

use app\common\lib\sms\AliSms;
use app\common\lib\Num;
use app\common\lib\ClassArr;

/**
 * 发送短信业务逻辑
 */
class Sms
{
    
    public static function sendCode(string $phoneNumber, int $len, string $type = "ali") :bool{
        //生成短信验证码
        $code = Num::getCode($len);
        //$sms = AliSms::sendCode($phoneNumber, $code);

        //工厂模式
        // $type = ucfirst($type);
        // $class = "app\common\lib\sms\\".$type."Sms";
        // $sms = $class::sendCode($phoneNumber, $code);

        $classStats = ClassArr::smsClassStat();
        $classObj = ClassArr::initClass($type, $classStats);
        $sms = $classObj::sendCode($phoneNumber, $code);
        if ($sms) {
            //需要把短信验证码记录到Redis，并且给出一个失效时间
            cache(config('redis.code_pre').$phoneNumber, $code, config('redis.code_expire'));
        }

        return $sms;
    }
}