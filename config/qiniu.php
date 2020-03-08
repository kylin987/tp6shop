<?php
// +----------------------------------------------------------------------
// | 七牛云设置
// +----------------------------------------------------------------------
use think\facade\Env;

return [
    'power' => true,
    'ak'    => Env::get('qiniu.accesskey', ''),
    'sk'    => Env::get('qiniu.secretkey', ''),
    'bucket'=> Env::get('qiniu.bucket', ''),
    'image_url' => 'http://q6vjva4ik.bkt.clouddn.com',
];
