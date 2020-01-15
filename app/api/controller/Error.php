<?php

namespace app\api\controller;

/**
 * 控制器不存在
 */
class Error
{
    
    public function __call($name, $arguments) {
        return show(config('status.error'), "该控制器不存在", null, 400);
    }
}