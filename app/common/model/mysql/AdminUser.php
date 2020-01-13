<?php

namespace app\common\model\mysql;

use think\Model;

/**
 * 
 */
class AdminUser extends Model
{
    
    //根据用户名获取后端表的数据
    public function getAdminUserByUsername($username)
    {
        if (empty($username)) {
            return false;
        }

        $where = [
            'username'  => $username,
        ];

        $result = $this->where($where)->find();
        return $result;
    }

    //根据主键id更新数据表中的数据
    public function updateById($id, $data) {
        $id = intval($id);
        if (empty($id) || empty($data) || !is_array($data)) {
            return false;
        }

        $where = [
            'id'    => $id,
        ];

        return $this->where($where)->save($data);
    }
}