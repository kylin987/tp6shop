<?php

namespace app\common\business;
use app\common\model\mysql\Goods as GoodsModel;
use app\common\business\GoodsSku as GoodsSkuBis;
use app\common\business\Category;
use think\facade\Log;

class Goods extends BaseBis {

    public $model = null;

    public function __construct() {
        parent::initialize();
        $this->model = new GoodsModel();
    }

    /**
     * 新增插入商品
     * @param $data
     * @return bool
     */
    public function insertData($data){
        //开启一个事务
        $this->model->startTrans();
        try {
            $data['operate_user'] = $this->adminUser;
            $goods_id = $this->add($data);
            if (!$goods_id) {
                return $goods_id;
            }

            $goodsSkuBisObj = new GoodsSkuBis();

            //如果插入的产品是统一规格
            if ($data['goods_specs_type'] == 1) {
                $skuData = [
                    'goods_id' => $goods_id,
                    'price'     => $data['price'],
                    'cost_price'=> $data['cost_price'],
                    'stock'     => $data['stock'],
                ];
                //写入sku表
                $res = $goodsSkuBisObj->add($skuData);
                if (!empty($res)){
                    $goodsSkuData = [
                        'sku_id'    => $res,
                    ];
                    //回写主表goods表
                    $goodsRes = $this->model->updateById($goods_id, $goodsSkuData);
                    if (!$goodsRes) {
                        throw new \think\Exception("insertData:goods主表回写失败");
                    }
                }else {
                    throw new \think\Exception("insertData:goods主表回写失败");
                }

            }elseif ($data['goods_specs_type'] == 2) {
                $data['goods_id'] = $goods_id;
                //写入sku表
                $res = $goodsSkuBisObj->saveAll($data);
                if (!empty($res)){
                    $stock = array_sum(array_column($res,'stock'));
                    $goodsSkuData = [
                        'sku_id'    => $res[0]['id'],
                        'price'     => $res[0]['price'],
                        'cost_price'=> $res[0]['cost_price'],
                        'stock'     => $stock,
                    ];
                    //回写主表goods表
                    $goodsRes = $this->model->updateById($goods_id, $goodsSkuData);
                    if (!$goodsRes) {
                        throw new \think\Exception("insertData:goods主表回写失败");
                    }
                }else {
                    throw new \think\Exception("insertData:goods主表回写失败");
                }
            }else {
                throw new \think\Exception("商品规格异常");
            }
            //事务提交
            $this->model->commit();
            return true;
        }catch (\Exception $e){
            Log::error("goods-insertData-".$e->getMessage());
            //事务回滚
            $this->model->rollback();
            return false;
        }

    }

    /**
     * 获取首页推荐商品大图
     * @return array
     */
    public function getRotationChart() {
        $data = [
            'is_index_recommend' => 1,
        ];
        $num = 5;
        $field = "sku_id as id,title,big_image as image";
        try {
            $res = $this->model->getRotationChart($data, $field, $num);
        }catch (\Exception $e){
            return [];
        }
        return $res->toArray();
    }

    /**
     * 获取首页分类和产品
     * @param $categoryIds
     * @return array
     */
    public function categoryGoodsRecommend($categoryIds) {
        if (!$categoryIds){
            return [];
        }

        $categoryTree = (new Category())->getCategoryTreeByPids($categoryIds);
        if (!$categoryTree){
            return [];
        }
        $result = [];
        foreach ($categoryTree as $k=>$v){
            if (isset($v['pid'])){
                unset($v['pid']);
            }
            $result[$k]['categorys'] = $v;
        }
        //防止产品和栏目对应不上，直接用$result来foreach
        foreach($result as $key => $value) {
            $result[$key]['goods'] = $this->getNormalGoodsFindInSetCategoryId($value['categorys']['category_id']);
        }

        return $result;
    }

    /**
     * 根据栏目id获得10个商品
     * @param $categoryId
     * @return array
     */
    public function getNormalGoodsFindInSetCategoryId($categoryId) {
        $field = "sku_id as id, title, price, recommend_image as image";
        $limit = 10;

        try {
            $res = $this->model->getNormalGoodsFindInSetCategoryId($categoryId, $field,$limit);
        }catch (\Exception $e){
            return [];
        }
        return $res->toArray();
    }


    public function getNormalLists($data, $pageSize, $order){
        try {
            $field = "sku_id as id,title,recommend_image as image,price,sales_count";
            $list = $this->model->getNormalLists($data, $pageSize, $field, $order);
            $res = $list->toArray();

            $result = [
                'total_page_num'    => isset($res['last_page']) ? $res['last_page'] : 0,
                'count'             => isset($res['total']) ? $res['total'] : 0,
                'page'              => isset($res['current_page']) ? $res['current_page'] : 0,
                'page_size'         => $pageSize,
                'list'              => isset($res['data']) ? $res['data'] : [],
            ];

        }catch (\Exception $e){
            Log::error("getNormalLists-".$e->getMessage());
            return [];
        }

        return $result;
    }
}
