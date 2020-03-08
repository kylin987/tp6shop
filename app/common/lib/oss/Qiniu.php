<?php
/**
 * User: Xunm
 * Date: 2018/10/6
 */

namespace app\common\lib\oss;

// 引入七牛鉴权类
use Qiniu\Auth;
// 引入七牛上传类
use Qiniu\Storage\UploadManager;

class Qiniu
{
    /**
     * 上传图片
     * @return string|null
     * @throws \Exception
     */
    public static function image()
    {
        if (empty($_FILES['file']['tmp_name'])) {
            explode('图片不合法', 404);
        }

        // 要上传文件的临时文件
        $file = $_FILES['file']['tmp_name'];

        $pathinfo = pathinfo($_FILES['file']['name']);

        // 通过pathinfo函数获取图片后缀名
        $ext = $pathinfo['extension'];

        $conf = config('qiniu');

        // 构建鉴权对象
        $auth = new Auth($conf['ak'], $conf['sk']);

        // 生成上传需要的token
        $token = $auth->uploadToken($conf['bucket']);

        // 上传到七牛后保存的文件名
        $filename = date('Y') . '/' . date('m') . '/' . substr(md5($file), 8, 5) . date('Ymd') . rand(0, 9999) . '.' . $ext;


        // 初始化UploadManager类
        $uploadMgr = new UploadManager();
        list($res, $err) = $uploadMgr->putFile($token, $filename, $file);

        if ($err !== null) {
            return null;
        } else {
            return $conf['image_url']."/".$filename;
        }
    }

    /**
     * 删除图片
     * @param $delFileName 要删除的图片文件，与七牛云空间存在的文件名称相同
     * @return bool
     */
    public static function delimage($delFileName)
    {
        // 判断是否是图片  目前测试，简单判断
        $isImage = preg_match('/.*(\.png|\.jpg|\.jpeg|\.gif)$/', $delFileName);
        if (!$isImage) {
            return false;
        }
        $conf = config('qiniu');

        // 构建鉴权对象
        $auth = new Auth($conf['ak'], $conf['sk']);

        // 配置
        $config = new \Qiniu\Config();

        // 管理资源
        $bucketManager = new \Qiniu\Storage\BucketManager($auth, $config);

        // 删除文件操作
        $res = $bucketManager->delete($conf['bucket'], $delFileName);

        if (is_null($res)) {
            // 为null成功
            return true;
        }

        return false;

    }
}