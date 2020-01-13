<?php

namespace app\admin\controller;

use app\BaseController;

/**
 * 退出
 */
class Logout extends BaseController
{
    
    public function index()
    {
        session(config('admin.session_admin'), null);
        return redirect(url('/admin/login/index'));
    }
}