<?php

namespace app\common\business;

use app\common\model\mysql\User as UserModel;
use app\common\lib\Str;
use app\common\lib\Time;
use think\facade\Request;

/**
* 
*/
class User {
    
    public $UserObj = null;

    public function __construct() {
        $this->UserObj = new UserModel();
    }

    public function login($data) {
        $redisCode = cache(config('redis.code_pre').$data['phone_number']);
        if (empty($redisCode) || $redisCode != $data['code']) {
            throw new \think\Exception("不存在该验证码", config('status.captcha_error'));         
        }

        $user = $this->UserObj->getUserByPhoneNumber($data['phone_number']);

        if (!$user) {
            // 不存在该用户，则插入用户数据
            $username = "yz-".$data['phone_number'];
            $userData = [
                'username'  => $username,
                'phone_number'  => $data['phone_number'],
                'type'          => $data['type'],
                'status'        => config('status.mysql.table_normal'),
                'last_login_time'   => time(),
                'last_login_ip'     => request()->ip(),
            ];
            try {
                $this->UserObj->save($userData);
                $userId = $this->UserObj->id;
                return $userId;
            } catch (\Exception $e) {
                throwE($e,config('status.datebase_error'),"数据库内部异常");                 
            }
            
        } else {
            //存在该用户，则更新用户数据
            $updateData = [
                'type'  => $data['type'],
                'last_login_time'   => time(),
                'last_login_ip'     => request()->ip(),
            ];
            try {
                $this->UserObj->updateById($user->id, $updateData);
            } catch (\Exception $e) {
                throwE($e,config('status.update_error'),"数据库更新异常"); 
            }
            $userId = $user->id;
            $username = $user->username;
        }       


        $token = Str::getLoginToken($data['phone_number']);
        $redisData = [
            'id'    => $userId,
            'username'  => $username,
            'type'      => $data['type'],
        ];

        $res = cache(config('redis.token_pre').$token, $redisData, Time::userLoginExpiresTime($data['type']));

        //获取设备的ua，理论上，单设备（浏览器）只给一个token
        //$agent = md5(Request::header('user-agent'));
        //$uaPre = config('redis.id_pre').$agent.'_';
        $uaPre = config('redis.id_pre');

        //检测之前的登录token是否还在，如果还在，删除之前的token
        $redisIDToken = cache($uaPre.$userId);
        if ($redisIDToken) {
            cache(config('redis.token_pre').$redisIDToken, null);
        }

        //生成一个检测redis,用来防止生成一个用户多个token，值为token
        if ($res) {
            cache($uaPre.$userId, $token, Time::userLoginExpiresTime($data['type']));
        }

        return $res ? ['token' => $token, 'username' => $username] : false;
    }

    /**
     * 根据id返回正常用户的数据
     * @param $id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getNormalUserById($id) {
        $user = $this->UserObj->getUserById($id);
        if (!$user || $user->status != config('status.mysql.table_normal')) {
            return [];
        }
        return $user->toArray();
    }

    /**
     * 根据用户名返回正常用户信息
     * @param $username
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getNormalUserByUsername($username) {
        $user = $this->UserObj->getUserByUsername($username);
        if (!$user || $user->status != config('status.mysql.table_normal')) {
            return [];
        }
        return $user->toArray();
    }

    /**
     * 更新用户信息
     * @param $id
     * @param $data
     * @return bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function update($id, $data) {
        $user = $this->getNormalUserById($id);
        if (!$user) {
            throw new \think\Exception("不存在该用户");
        }
        if ($user['username'] == $data['username'] && $user['sex'] == $data['sex']) {
            return true;
        }

        //如果已经修改了用户名
        if($user['is_edit_username']) {
            if ($data['username'] != $user['username']){
                throw new \think\Exception("用户名不可更改");
            }
        } else {
            if ($user['username'] != $data['username']) {
                $res = $this->getNormalUserByUsername($data['username']);
                if ($res) {
                    throw new \think\Exception("该用户名已存在");
                }
                $data['is_edit_username'] = 1;
            }
        }

        return $this->UserObj->updateById($id, $data);
    }

    /**
     * 修改token里的数据
     * @param $token
     * @param $username
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function updateRedisDate($token, $username) {
        $userInfo = cache(config('redis.token_pre').$token);
        if ($userInfo['username'] !== $username) {
            $user = $this->getNormalUserById($userInfo['id']);
            $redisData = [
                'id'    => $userInfo['id'],
                'username'  => $user['username'],
                'type'      => $userInfo['type'],
            ];
            //计算redis缓存剩余时间
            $surplusTime =Time::userLoginExpiresTime($userInfo['type']) - (intval(time()) - intval($user['last_login_time']));

            //修改缓存的数据
            return cache(config('redis.token_pre').$token, $redisData, $surplusTime);
        }
    }
}