<?php
namespace app\common\lib;

/**
 * 对数组进行组装处理
 */
class Arr
{
 
    /**
     * 对数组进行树形组装
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
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

    /**
     * 从树形结构中去除对应数目的分叉
     * @param  [type]  $data        [description]
     * @param  integer $firstCount  [description]
     * @param  integer $secondCount [description]
     * @param  integer $threeCount  [description]
     * @return [type]               [description]
     */
    public static function sliceTreeArr($data, $firstCount = 5, $secondCount = 3, $threeCount = 5){
        $data = array_slice($data, 0, $firstCount);
        foreach ($data as $k => $v) {
            if (!empty($v['list'])) {
                $data[$k]['list'] = array_slice($v['list'], 0 , $secondCount);
                foreach ($v['list'] as $kk => $vv) {
                     if (!empty($vv['list'])) {
                         $data[$k]['list'][$kk]['list'] = array_slice($vv['list'], 0, $threeCount);
                     }
                 } 
            }
        }
        return $data;
    }
}