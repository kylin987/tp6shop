<?php

namespace app\admin\controller;

use app\BaseController;
use think\facade\View;
use app\common\model\mysql\AdminUser;

class Login extends BaseController
{
    public function index() {
    	return View::fetch();
    }

    public function md5() {
        halt(session(config('admin.session_admin')));
        $salt = createNonceStr(10);
        dump($salt);
        echo kMd5("admin",$salt);
    }

    public function check() {
        if (!$this->request->isPost()) {
            return show(config('status.error'), "请求方式异常");
        }

        $username = $this->request->param("username", "", "trim");
        $password = $this->request->param("password", "", "trim");
        $captcha = $this->request->param("captcha", "", "trim");

        if (empty($username) || empty($password) || empty($captcha)) {
            return show(config('status.error'), "参数不能为空");
        }

        //验证码校验
        if (!captcha_check($captcha)) {
            return show(config('status.error'), "验证码不正确");
        }

        try {
            $adminUserObj = new AdminUser();
            $adminUser = $adminUserObj->getAdminUserByUsername($username);
            
            if (empty($adminUser) || $adminUser->status != config('status.mysql.table_normal')) {
                return show(config('status.error'), "不存在该用户"); 
            }
            $adminUser = $adminUser->toArray();
            //判断密码是否正确
            if ($adminUser['password'] != kMd5($password,$adminUser['salt'])) {
                return show(config('status.error'), "密码错误"); 
            }
            

            //记录信息到mysql表中
            $updateDate = [
                'last_login_time'   => time(),
                'last_login_ip'     => request()->ip(),
            ];
            $res = $adminUserObj->updateById($adminUser['id'], $updateDate);
            if (empty($res)) {
                return show(config('status.error'), "登录失败"); 
            }
        } catch (\Exception $e) {
            // todo 记录日志  $e->getMessage();
            return show(config('status.error'), "内部异常，登录失败"); 
        }
        //记录session
        session(config('admin.session_admin'), $adminUser);


        return show(config('status.success'), "登录成功"); 
    }
}
