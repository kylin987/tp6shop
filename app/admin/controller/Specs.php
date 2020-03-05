<?php
namespace app\admin\controller;

use app\BaseController;
/**
 * 规格名称
 */
class Specs extends BaseController
{
    
    public function dialog(){
        return view("",[
            "specs" => json_encode(config('specs')),
        ]);
    }
}