<?php

namespace app\common\lib;

/**
* 字符串相关lib
*/
class Str {



    /**
     * 生成随机字符串
     * @param int $length
     * @return string
    */
    public static function createRandomStr($length = 16) {
        $chars = "abcdefghjkmnpqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ23456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
          $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 生成所需的token
     * @param string $string
     * @return string
    */
    public static function getLoginToken($string) {
        $str = md5(uniqid(md5(microtime(true)),true));  //生成一个不会重复的字符串
        $token = sha1($str.$string); //加密
        return $token;
    }

}