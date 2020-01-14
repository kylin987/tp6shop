<?php

namespace app\admin\validate;

use think\Validate;

/**
 * 管理员登录验证器
 */
class AdminUser extends Validate
{
    
    protected $rule = [
        'username'  => 'require',
        'password'  => 'require',
        'captcha'  => 'require|checkCaptcha',
    ];

    protected $message = [
        'username'  => '用户名必须填写',
        'password'  => '必须填写密码',
        'captcha'   => '必须填写验证码',
    ];

    protected function checkCaptcha($value, $rule, $data = []) {
        //验证码校验
        if (!captcha_check($value)) {
            return "验证码不正确";
        }

        return true;
    }
}