<?php
namespace app\admin\controller;

use app\BaseController;
use app\common\lib\Show;
use think\facade\Filesystem;

class Image extends BaseController{

    public function upload(){
        if (!$this->request->isPost()) {
            return Show::error("请求不合法");
        }
        $file = $this->request->file('file');
        //校验
        try {
            $data = [
                'image'=>'fileSize:'.config('upload.image.max_size').'|fileExt:'.config('upload.image.allow_type')
            ];
            validate($data)->check(["file"=>$file]);
            //保存图片
            $filename = Filesystem::disk('public')->putFile('image', $file);
        } catch (\think\exception\ValidateException $e) {
            echo $e->getMessage();
        }

        if (!$filename) {
            return Show::error("上传图片失败");
        }
        $imageUrl = [
            'image' => "/upload/".$filename,
        ];
        return Show::success($imageUrl);
    }
}