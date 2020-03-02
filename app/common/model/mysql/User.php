<?php

namespace app\common\model\mysql;

use think\Model;

/**
 * 
 */
class User extends Model
{
    //开启自动添加时间戳
    protected $autoWriteTimestamp = true;
    //根据手机号获取用户表的数据
    public function getUserByPhoneNumber($phone_number)
    {
        if (empty($phone_number)) {
            return false;
        }

        $where = [
            'phone_number'  => $phone_number,
        ];

        $result = $this->where($where)->find();
        return $result;
    }

    /**
     * 根据id获取用户信息
     * @param $id
     * @return array|bool|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserById($id) {
        $id = intval($id);
        if (!$id) {
            return false;
        }
        return $this->find($id);
    }

    /**
     * 根据用户名获取用户信息
     * @param $username
     * @return array|bool|Model|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getUserByUsername($username) {
        if (empty($username)) {
            return false;
        }
        $where = [
            'username'  => $username,
        ];
        return $this->where($where)->find();
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