<?php

namespace app\admin\controller;

use app\BaseController;
use think\facade\View;
use app\common\lib\Str;

class Login extends BaseController
{
    public function index() {
    	return View::fetch();
    }

    public function md5() {
        $salt = Str::createRandomStr(10);
        dump($salt);
        echo kMd5("admin",$salt);
    }

    //登录验证
    public function check() {
        if (!$this->request->isPost()) {
            return show(config('status.error'), "请求方式异常");
        }

        $data = [
            'username'  => $this->request->param("username", "", "trim"),
            'password'  => $this->request->param("password", "", "trim"),
            'captcha'   => $this->request->param("captcha", "", "trim"),
        ];

        //验证器
        $validate = new \app\admin\validate\AdminUser();
        if (!$validate->check($data)) {
            return show(config('status.error'), $validate->getError());
        }

        try {
            $result = \app\admin\business\AdminUser::login($data); 
        }catch (\Exception $e) {
            return show(config('status.error'), $e->getMessage());
        }

        if ($result) {
            return show(config('status.success'), "登录成功");
        } else {
            return show(config('status.error'), "登录失败");
        }
        
    }
}
