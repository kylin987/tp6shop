<?php

/*
*   阿里云相关的配置
*/
use think\facade\Env;

return [
    'host'  =>'dysmsapi.aliyuncs.com',
    'access_key_id' => Env::get('aliyun.access_key_id', ''),
    'access_key_secret' => Env::get('aliyun.access_key_secret', ''),
    'region_id'     => 'cn-hangzhou',
    'sign_name'     => '言致商城',
    'template_code' => 'SMS_182680347',
];