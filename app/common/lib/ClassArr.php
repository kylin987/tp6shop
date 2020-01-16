<?php

namespace app\common\lib;

/**
 * lib类的初始化
 */
class ClassArr
{
    
    public static function smsClassStat() {
        return [
            'ali'   => "app\common\lib\sms\AliSms",
            'baidu' => "app\common\lib\sms\BaiduSms",
            'jd'    => "app\common\lib\sms\JdSms",
            'yuntz' => "app\common\lib\sms\YuntzSms",
        ];
    }

    //示例上传类
    public static function uploadClassStat() {
        return [
            'text'  => "xxx",
            'image' => "xxx",
        ];
    }

    public static function initClass($type, $class, $params = [], $needInstance = false) {
        //如果我们的工厂模式调用的方法是静态的，那么我们这里返回类库即可  AliSms
        //如果不是静态的，那么需要返回 实例化的对象

        if (!array_key_exists($type, $class)) {
            return false;
        }

        $className = $class[$type];

        if ($needInstance) {
            //如果需要实例化对象，使用 new ReflectionClass('A'),建立A反射类 
            // ->newInstanceArgs($params) 此句相当于实例化A对象
            return (new \ReflectionClass($className))->newInstanceArgs($params);
        } else {
            //如果是静态的，那么不需要实例化，直接返回类库
            return $className;
        }
    }
}