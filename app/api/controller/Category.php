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
        $result = [];
        $categoryInfo = $this->CategoryBis->getCategoryRedisInfo();
        if (!empty($categoryInfo)){
            foreach ($categoryInfo as $value){
                $arr = json_decode($value,true);
                if ($arr['pid'] == 0){
                    $result[] = json_decode($value,true);
                }
            }
        }else{
            try {
                $categorys = $this->CategoryBis->getNormalCategorys();
            } catch(\Exception $e){
                Log::error("getNormalCategorys-error".$e->getMessage());
                return Show::success("","内部异常");
            }
            if (empty($categorys)) {
                return Show::success("","数据为空");
            }

            $result = Arr::getTree($categorys);
        }

        $result = Arr::sliceTreeArr($result);
        if (!empty($result)){
            array_multisort(array_column($result,'listorder'), SORT_DESC, $result);

        }
        $result = Arr::del_key($result,['pid','path','listorder']);
        return Show::success($result);
    }

    /**
     * 按分类检索Api
     * @return \think\response\Json
     */
    public function search(){
        $id = input("param.id", "", "intval");
        if (empty($id)){
            return Show::success();
        }
        try {
            $result = $this->CategoryBis->getUpDownCategoryList($id);
        }catch (\Exception $e){
            Log::error("search-error".$e->getMessage());
            return Show::success("","内部异常");
        }

        return Show::success($result);
    }

    public function sub(){
        $id = input("param.id", "", "intval");
        if (empty($id)){
            return Show::success();
        }

        try {
            $result = $this->CategoryBis->getDownCategoryList($id);
        }catch (\Exception $e){
            Log::error("search-error".$e->getMessage());
            return Show::success("","内部异常");
        }

        return Show::success($result);
    }
}