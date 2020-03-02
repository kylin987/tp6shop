<?php

namespace app\api\controller;


class Logout extends ApiBase
{
    public $middleware = ['auth'];

    /**
     * 退出登录
     * @return \think\response\Json
     */
    public function index() {
        //删除redis token的缓存
        $res = cache(config('redis.token_pre').$this->accessToken, null);

        $result = cache(config('redis.id_pre').$this->userId, null);

        if ($res) {
            return show(config('status.success'), "退出登录成功");
        }

        return show(config('status.error'), "退出登录失败");

    }
}