<?php
namespace app\admin\controller;

use app\BaseController;
use app\common\lib\Show;
use app\common\business\Goods as GoodsBis;
/**
 * 后台产品控制器
 */
class Goods extends BaseController
{
    public function index(){
        return view();
    }

    public function add(){
        return view();
    }

    public function save() {
        if (!$this->request->isPost()){
            return Show::error("请求方式异常");
        }
//        if (!$this->request->checkToken()){
//            return Show::error("Token不合法");
//        }
        $data = [
            'title' =>input("param.title", "", "trim"),
            'category_path_id' =>input("param.category_id", 0, "trim"),
            'sub_title' =>input("param.sub_title", "", "trim"),
            'promotion_title' =>input("param.promotion_title", "", "trim"),
            'keywords' =>input("param.keywords", "", "trim"),
            'goods_unit' =>input("param.goods_unit", "", "trim"),
            'is_show_stock' =>input("param.is_show_stock", 0, "intval"),
            'stock' =>input("param.stock", 0, "intval"),
            'production_time' =>input("param.production_time", "", "trim"),
            'goods_specs_type' =>input("param.goods_specs_type", 0, "intval"),
            'big_image' =>input("param.big_image", "", "trim"),
            'carousel_image' =>input("param.carousel_image", "", "trim"),
            'recommend_image' =>input("param.recommend_image", "", "trim"),
            'description' =>input("param.description", "", "trim"),
            'market_price' => input("param.market_price", 0, "floatval"),
            'sell_price' => input("param.sell_price", 0, "floatval"),
            'skus'      => input("param.skus", []),
        ];

        //验证器
        $validate = new \app\admin\validate\Goods();
        //基础信息验证
        if (!$validate->scene('base')->check($data)) {
            return Show::error($validate->getError());
        }
        if ($data['goods_specs_type'] == 1) {
            //统一规格信息验证
            if (!$validate->scene('no_sku')->check($data)) {
                return Show::error($validate->getError());
            }
        }
        //暂不做sku详细验证
        if ($data['goods_specs_type'] == 2) {
            if (!$validate->scene('skus')->check($data)) {
                return Show::error($validate->getError());
            }
        }

        //数据处理
        $arr = explode(",",$data['category_path_id']);
        $data['category_id'] = end($arr);

        try {
            $result = (new GoodsBis())->insertData($data);
        } catch (\Exception $e) {
            return Show::error($e->getMessage());
        }

        if (!$result) {
            return Show::error("新增商品失败");
        }
        return Show::success("","新增商品成功");
    }
}