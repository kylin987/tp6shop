<?php

namespace app\admin\validate;

use think\Validate;

/**
 * 产品新增编辑验证
 */
class Goods extends Validate
{
    
    protected $rule = [
        'title'  => 'require',
        'category_path_id'  => 'require',
        'sub_title'  => 'require',
        'goods_unit'  => 'require',
        'is_show_stock'  => 'require|in:0,1',
        'goods_specs_type'  => 'require|in:1,2',
        'carousel_image'  => 'require',
        'recommend_image'  => 'require',
        'description'  => 'require',
        'market_price' => 'require|float|gt:0',
        'sell_price' => 'require|float|gt:0',
        'stock'     => 'require|number',
        'skus'      => 'require',
    ];

    protected $message = [
        'title'  => '标题必须填写',
        'category_path_id'  => '栏目必须选择',
        'sub_title'   => '副标题必须填写',
        'goods_unit'   => '商品单位必须填写',
        'is_show_stock.require'   => '库存显示必须选择',
        'is_show_stock.in'   => '库存显示选择异常',
        'goods_specs_type.require'   => '商品规格必须选择',
        'goods_specs_type.in'   => '商品规格选择异常',
        'carousel_image'   => '轮播图不能为空',
        'recommend_image'   => '展示图不能为空',
        'description'   => '商品详情不能为空',
        'market_price.require'   => '市场价格不能为空',
        'market_price.float'   => '市场价格异常',
        'market_price.gt'   => '市场价格必须大于0',
        'sell_price.require'   => '销售价格不能为空',
        'sell_price.float'   => '销售价格异常',
        'sell_price.gt'   => '市场价格必须大于0',
        'stock.require'   => '总库存不能为空',
        'stock.number'   => '总库存只能为整数',
        'skus.require'   => 'SKU数据不能为空',
    ];

    protected $scene = [
        'base'   => ['title','category_path_id','sub_title','goods_unit','is_show_stock','goods_specs_type','description'],
        'no_sku' => ['market_price','sell_price','stock'],
        'skus' => ['skus'],
    ];
}