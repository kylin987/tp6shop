<?php
declare(strict_type=1);
namespace app\common\lib\sms;

use app\common\lib\Curl;
use think\facade\Log;
/**
 * www.sms.cn云通知发送短信
 */
class YuntzSms implements SmsBase
{
    
    public static function sendCode(string $phone, int $code)  : bool{
        $data = [
            'code'  => $code,
        ];
        $url = "http://api.sms.cn/sms/?ac=send&uid=kylin87&pwd=bf7eecb2107cb37f2b5ce8a07eab67a0&template=100006&mobile=".$phone."&content=".json_encode($data);
        $result = Curl::get($url);
        Log::info("Yuntzsms".$result);
        $result = json_decode($result);
        if (isset($result) && $result->stat == "100") {
            return true;
        }
        return false;
    } 
}