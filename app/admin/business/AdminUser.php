<?php

namespace app\admin\business;

use app\common\model\mysql\AdminUser as AdminUserModel;
use think\Exception;

/**
 * 后台用户业务逻辑
 */
class AdminUser
{
    
    public static function login($data) {
                    
        $adminUser = self::getAdminUserByUsername($data['username']);
        if (!$adminUser) {
            throw new Exception("不存在该用户");
        }
        //判断密码是否正确
        if ($adminUser['password'] != kMd5($data['password'],$adminUser['salt'])) {
            throw new Exception("密码错误");
        }
        

        //记录信息到mysql表中
        $updateDate = [
            'last_login_time'   => time(),
            'last_login_ip'     => request()->ip(),
        ];
        $adminUserObj = new AdminUserModel();
        $res = $adminUserObj->updateById($adminUser['id'], $updateDate);
        if (empty($res)) {
            throw new Exception("登录失败");
        }
        
        //记录session
        session(config('admin.session_admin'), $adminUser);
        return true; 
    }

    //通过用户名获取后台用户数据
    public static function getAdminUserByUsername($username) {
        $adminUserObj = new AdminUserModel();
        $adminUser = $adminUserObj->getAdminUserByUsername($username);
        
        if (empty($adminUser) || $adminUser->status != config('status.mysql.table_normal')) {
            return false;
        }
        $adminUser = $adminUser->toArray();
        return $adminUser;
    }
}