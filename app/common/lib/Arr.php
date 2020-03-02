<?php
namespace app\common\lib;

/**
 * 对数组进行组装处理
 */
class Arr
{
    
    public static function getTree($data){
        $items = [];
        foreach ($data as $k=>$v) {
            $v['category_id'] = $v['id'];
            unset($v['id']);
            $items[$v['category_id']] = $v;            
        }
        $tree = [];
        foreach ($items as $id => $item) {
            if (isset($items[$item['pid']])) {
                $items[$item['pid']]['list'][] = &$items[$id];
            }else {
                $tree[] = &$items[$id];
            }
        }
        return $tree;
    }
}