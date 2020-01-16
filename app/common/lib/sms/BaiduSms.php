<?php
declare(strict_type=1);
namespace app\common\lib\sms;

/**
 * 百度云发送短信
 */
class BaiduSms implements SmsBase
{
    
    public static function sendCode(string $phone, int $code)  : bool{
        return true;
    } 
}