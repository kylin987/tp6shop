<?php
declare(strict_type=1);
namespace app\common\lib\sms;

/**
 * 京东云发送短信
 */
class JdSms implements SmsBase
{
    
    public static function sendCode(string $phone, int $code)  : bool{
        return true;
    } 
}