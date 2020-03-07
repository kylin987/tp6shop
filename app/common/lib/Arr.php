<?php
namespace app\common\lib;

/**
 * 对数组进行组装处理
 */
class Arr
{

    /**
     * 对数组进行树形组装
     * @param $data
     * @return array
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
     * 从树形结构中取出对应数目的分叉
     * @param $data
     * @param int $firstCount
     * @param int $secondCount
     * @param int $threeCount
     * @return array
     */
    public static function sliceTreeArr($data, $firstCount = 5, $secondCount = 3, $threeCount = 5){
        $data = array_slice($data, 0, $firstCount);
        foreach ($data as $k => $v) {
            if (!empty($v['list'])) {
                $data[$k]['list'] = array_slice($v['list'], 0 , $secondCount);
                foreach ($data[$k]['list'] as $kk => $vv) {
                     if (!empty($vv['list'])) {
                         $data[$k]['list'][$kk]['list'] = array_slice($vv['list'], 0, $threeCount);
                     }
                 } 
            }
        }
        return $data;
    }

    /**
     * 分页默认返回的数据
     * @return array
     */
    public static function getPaginateDefaultData($num) {
        $result = [
            'total' => 0,
            'per_page'  => $num,
            'current_page'  => 1,
            'last_page' => 0,
            'data'  => [],
        ];
        return $result;
    }
}