<?php

namespace app\common\business;
use app\common\model\mysql\GoodsSku as GoodsSkuModel;
use think\facade\Log;

class GoodsSku extends BaseBis {

    public $model = null;

    public function __construct() {
        parent::initialize();
        $this->model = new GoodsSkuModel();
    }

    public function saveAll($data){
        if (!$data['skus']){
            return false;
        }
        $insertData = [];
        foreach ($data['skus'] as $v){
            $insertData[] = [
                'goods_id'  => $data['goods_id'],
                'specs_value_ids' => $v['propvalnames']['propvalids'],
                'price' => $v['propvalnames']['skuSellPrice'],
                'cost_price' => $v['propvalnames']['skuMarketPrice'],
                'stock' => $v['propvalnames']['skuStock'],
            ];
        }
        try {
            $result = $this->model->saveAll($insertData);
            return $result->toArray();
        } catch (\Exception $e) {
            Log::error("sku-saveAll-error_".$e->getMessage());
            return false;
        }
    }
}
