<?php
namespace app\api\controller;

use app\common\business\Category as CategoryBis;
use app\common\lib\Show;
use app\common\lib\Arr;
use think\facade\Log;
/**
 * 
 */
class Category extends ApiBase
{
    public $CategoryBis = '';

    public function __construct(){
        $this->CategoryBis = new CategoryBis();
    }

    /**
     * 获取分类树
     * @return [type] [description]
     */
    public function index(){
        try {
            $categorys = $this->CategoryBis->getNormalCategorys();
        } catch(\Exception $e){
            log::error("getNormalCategorys-error".$e->getMessage());
            return Show::success("","内部异常");
        }
        if (empty($categorys)) {
            return Show::success("","数据为空");
        }
        
        $result = Arr::getTree($categorys);
        $result = Arr::sliceTreeArr($result);
        return Show::success($result);
    }
}