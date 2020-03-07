<?php

namespace app\common\business;
use app\common\model\mysql\Goods as GoodsModel;
use app\common\business\GoodsSku as GoodsSkuBis;
use think\facade\Log;

class Goods extends BaseBis {

    public $model = null;

    public function __construct() {
        parent::initialize();
        $this->model = new GoodsModel();
    }

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
}
