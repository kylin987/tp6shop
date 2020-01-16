<?php
declare(strict_type=1);
namespace app\common\lib\sms;

/**
 * 短信统一接口约束
 */
interface SmsBase {
    public static function sendCode(string $phone, int $code);
}