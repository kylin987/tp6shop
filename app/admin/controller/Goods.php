<?php
namespace app\admin\controller;

use app\BaseController;
use app\common\lib\Show;
use app\common\business\Goods as GoodsBis;
use think\facade\View;
/**
 * 后台产品控制器
 */
class Goods extends BaseController
{

    public function index(){
        $data = [];
        $filter = [];
        $title = input("param.title", "", "trim");
        $time = input("param.time", "", "trim");
        if (!empty($title)) {
            $data['title'] = $title;
            $filter['title'] = $title;
        }
        if (!empty($time)) {
            $data['create_time'] = explode(" - ", $time);
            $filter['time'] = $time;
        }

        $num = 5;

        try {
            $goods = (new GoodsBis())->getLists($data, $num);
        } catch (\Exception $e) {
            $goods = \app\common\lib\Arr::getPaginateDefaultData($num);
        }

        $filter['query'] = \app\common\lib\Filter::getFilter($this->request->query());

        View::assign('filter',$filter);
        View::assign('goods',$goods);
        return View::fetch();
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
            'cost_price' => input("param.market_price", 0, "floatval"),
            'price' => input("param.sell_price", 0, "floatval"),
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

    /**
     * 修改商品状态，包括删除99
     * @return \think\response\Json
     */
    public function changeStatus(){
        $id = input("param.id", 0, "intval");
        $status = input("param.status", 0, "intval");

        $data = [
            'id'    => $id,
            'status' => $status,
        ];

        $validate = (new \app\admin\validate\Goods())->scene('changeStatus');
        if (!$validate->check($data)) {
            return Show::error($validate->getError());
        }

        if (!in_array($status, \app\common\lib\Status::getTableStatus())) {
            return Show::error("参数错误");
        }

        try {
            $resule = (new GoodsBis())->updateById($data);
        } catch (\Exception $e) {
            return Show::error($e->getMessage());
        }

        if ($resule) {
            return Show::success($resule, "更新成功");
        }
        return Show::error("更新失败");
    }

    /**
     * 更新商品排序
     * @return \think\response\Json
     */
    public function listorder() {
        $id = input("param.id", 0, "intval");
        $listorder = input("param.listorder", 0, "intval");

        $data = [
            'id'    => $id,
            'listorder' => $listorder,
        ];

        $validate = (new \app\admin\validate\Goods())->scene('changeListOrder');
        if (!$validate->check($data)) {
            return Show::error($validate->getError());
        }
        try {
            $resule = (new GoodsBis())->updateById($data);
        } catch (\Exception $e) {
            return Show::error($e->getError());
        }

        if ($resule) {
            return Show::success($resule,"排序成功");
        }
        return Show::error("排序失败");
    }

    /**
     * 更新商品推荐
     * @return \think\response\Json
     */
    public function changeRecommend(){
        $id = input("param.id", 0, "intval");
        $is_index_recommend = input("param.is_index_recommend", 0, "intval");

        $data = [
            'id'    => $id,
            'is_index_recommend' => $is_index_recommend,
        ];

        $validate = (new \app\admin\validate\Goods())->scene('changeRecommend');
        if (!$validate->check($data)) {
            return Show::error($validate->getError());
        }
        $GoodsBisOjb = new GoodsBis();
        $good_info = $GoodsBisOjb->getInfoById($data['id']);
        if (empty($good_info['big_image']) && $is_index_recommend == 1) {
            return Show::error("该产品没有大图");
        }

        try {
            $resule = $GoodsBisOjb->updateById($data);
        } catch (\Exception $e) {
            return Show::error($e->getMessage());
        }

        if ($resule) {
            return Show::success($resule, "更新成功");
        }
        return Show::error("更新失败");
    }
}