<?php
declare(strict_types=1);
namespace app\api\controller;

use app\BaseController;
use app\common\business\Sms as SmsBus;
use think\Response\Json;

/**
 * 发送短信
 */
class Sms extends BaseController
{
    
    public function code() : Json {
        $phoneNumber = input('param.phone_number', '', 'trim');
        $data = [
            'phone_number'  => $phoneNumber,
        ];
        try {
            validate(\app\api\validate\User::class)->scene('send_code')->check($data);
        }catch (\think\exception\ValidateException $e) {
            return show(config('status.error'), $e->getError());
        }

        //分流
        if (rand(1,10) <= 8) {
            $type = "ali";
        }else {
            $type = "yuntz";
        }

        //调用business层的逻辑
        if (SmsBus::sendCode($phoneNumber,6,$type)) {
            return show(config('status.success'), "发送验证码成功");
        }
        return show(config('status.error'), "发送验证码失败");
    }
}