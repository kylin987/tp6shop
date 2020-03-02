<?php

namespace app\common\lib;

/**
* 时间相关lib
*/
class Time {
    
    /**
     *  用户登录的token有效时间
     *  @param int $type  默认2=30天，1=7天
     *  @return int
    */
    public static function userLoginExpiresTime($type = 2) {
        $type = !in_array($type, ['1','2']) ? 2 : $type;
        if ($type == 1) {
            $day = 7;
        } else {
            $day = 30;
        }
        return $day * 24 * 3600;
    }
}