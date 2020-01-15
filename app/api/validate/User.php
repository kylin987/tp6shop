<?php

namespace app\api\validate;

use think\Validate;

/**
 * 数据校验
 */
class User extends Validate
{
    
    protected $rule = [
        'username'  => 'require',
        'phone_number'  => 'require|mobile',
    ];

    protected $message = [
        'username'  => '用户名必须',
        'phone_number.require'  => '手机号必须',
        'phone_number.mobile'   => '请填写正确的手机号'
    ];

    protected $scene = [
        'send_code' => ['phone_number'],
    ];
}