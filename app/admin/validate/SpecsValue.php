<?php

namespace app\admin\validate;

use think\Validate;

/**
 * 规格属性验证器
 */
class SpecsValue extends Validate
{
    
    protected $rule = [
        'specs_id'  => 'require|number',
        'name'  => 'require',
    ];

    protected $message = [
        'specs_id'  => '规格id必须填写',
        'name'  => '必须填写规格属性名',
    ];
}