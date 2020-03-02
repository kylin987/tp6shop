<?php
namespace app\api\controller;

use app\common\business\Category as CategoryBis;
use app\common\lib\Show;
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
       $categorys = $this->CategoryBis->getNormalCategorys();
       $result = \app\common\lib\Arr::getTree($categorys);
       return Show::success($result);
    }
}