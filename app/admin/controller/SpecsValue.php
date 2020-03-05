<?php
namespace app\admin\controller;

use app\BaseController;
use app\common\lib\Show;
use app\common\business\SpecsValue as SpecsValueBis;
/**
 * 规格属性
 */
class SpecsValue extends BaseController
{
    /**
     * 新增规格属性
     * @return \think\response\Json
     */
    public function save(){
        $specsId = input("param.specs_id", 0, "intval");
        $name = input("param.name", "", "trim");

        $data = [
            'specs_id' => $specsId,
            'name'      => $name,
        ];

        $validate = new \app\admin\validate\SpecsValue();
        if (!$validate->check($data)) {
            return Show::error($validate->getError());
        }
        try {
           $id = (new SpecsValueBis())->add($data);
        } catch(\Exception $e){
            return Show::error($e->getMessage());
        }        

        if (!$id) {
            return Show::error("新增失败");
        }

        return Show::success(['id'=>$id]);
    }

    /**
     * 获取规格id里的所有规格属性
     * @return \think\response\Json
     */
    public function getBySpecsId()
    {
        $specs_id = input("param.specs_id", 0, "intval");
        if (!$specs_id) {
            return Show::success("","没有数据哦");
        }
        $result = (new SpecsValueBis())->getBySpecsId($specs_id);
        return Show::success($result);
    }

    /**
     * 删除指定的规格属性99
     * @return \think\response\Json
     */
    public function del()
    {
        $specs_value_id = input("param.specs_value_id", 0 , "intval");
        if (!$specs_value_id) {
            return Show::error("不存在该规格属性");
        }
        try {
            $result = (new SpecsValueBis())->DelById($specs_value_id);
        } catch(\Exception $e) {
            return Show::error($e->getMessage());
        }
        if (!$result) {
            return Show::error("删除失败");
        }
        return Show::success();
    }
}