<?php

namespace app\admin\controller;

use think\captcha\facade\Captcha;

/**
 * 验证码
 */
class Verify
{
    
    public function index($lenth = 4)
    {
        return Captcha::create("len".$lenth);
    }
}