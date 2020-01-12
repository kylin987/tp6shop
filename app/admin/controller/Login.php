<?php

namespace app\admin\controller;

use app\BaseController;
use think\facade\View;

class Login extends BaseController
{
    public function index() {
    	return View::fetch();
    }

    public function check() {
        return show(config('status.error'), "登录失败");
    }
}
