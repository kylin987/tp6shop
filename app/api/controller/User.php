<?php

namespace app\api\controller;

use app\common\business\User as UserBis;


class User extends ApiBase {

    public $middleware = ['auth'];

    public function index() {
        $userInfo = (new UserBis())->getNormalUserById($this->userId);
        if (!$userInfo) {
            return show(config('status.error'),"用户数据异常");
        }
        $resDate = [
            'username'  => $userInfo['username'],
            'id'    => $userInfo['id'],
            'sex'       => $userInfo['sex'],
        ];
        return show(config('status.success'), "ok", $resDate);
    }

    /**
     * 更新用户信息
     * @return \think\response\Json
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function update() {
        $username = input('param.username', "", "trim");
        $sex = input('param.sex', 0, "intval");
        $data = [
            'username'  => $username,
            'sex'       => $sex,
        ];

        $validate = (new \app\api\validate\User())->scene('update_user');
        if (!$validate->check($data)) {
            return show(config('status.error'), $validate->getError());
        }
        try {
            $result = (new UserBis())->update($this->userId, $data);
        } catch (\Exception $e) {
            return show($e->getCode(), $e->getMessage());
        }

        if (!$result) {
            return show(config('status.error'), "更新失败");

        }
        //修改redis里面的用户名
        (new UserBis())->updateRedisDate($this->accessToken, $data['username']);
        return show(config('status.success'), "更新成功");


    }



}