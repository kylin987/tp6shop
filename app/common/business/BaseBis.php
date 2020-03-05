<?php

namespace app\common\business;
use think\facade\Session;
/**
* 
*/
class BaseBis {

    public $adminUser = '';
    public function __construct()
    {
        $adminUser = Session::get(config('admin.session_admin'));
        if ($adminUser && isset($adminUser['username'])) {
            $this->adminUser = $adminUser['username'];
        }
    }
}