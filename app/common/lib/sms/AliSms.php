<?php
declare(strict_type=1);
namespace app\common\lib\sms;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

/**
 * 阿里云短信
 */
class AliSms
{
    
    public static function sendCode(string $phone, int $code)  : bool{
        if (empty($phone) || empty($code)) {
            return false;
        }

        AlibabaCloud::accessKeyClient('<accessKeyId>', '<accessSecret>')
                        ->regionId('cn-hangzhou')
                        ->asDefaultClient();

        try {
            $result = AlibabaCloud::rpc()
                                  ->product('Dysmsapi')
                                  // ->scheme('https') // https | http
                                  ->version('2017-05-25')
                                  ->action('SendSms')
                                  ->method('POST')
                                  ->host('dysmsapi.aliyuncs.com')
                                  ->options([
                                                'query' => [
                                                  'RegionId' => "cn-hangzhou",
                                                  'PhoneNumbers' => "15890054823",
                                                  'SignName' => "言致商城",
                                                  'TemplateCode' => "SMS_182680347",
                                                  'TemplateParam' => "{\"code\":\"457165\"}",
                                                ],
                                            ])
                                  ->request();
            print_r($result->toArray());
        } catch (ClientException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        }
    }
}