<?php

namespace app\api\controller;

use app\BaseController;

/**
 * 发送短信
 */
class Sms extends BaseController
{
    
    public function demo(){        
        return show(config('status.success'), 'is ok');
    }
}