<?php

namespace app\common\business;
use app\common\model\mysql\Category as CategoryModel;
use think\facade\Log;
use app\common\lib\Arr;
use think\facade\Cache;

class Category extends BaseBis {

    public $model = null;

    public function __construct() {
        parent::initialize();
        $this->model = new CategoryModel();
    }

    /**
     * 增加分类
     * @param $data
     * @return mixed
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function add($data) {
        $data['status'] = config('status.mysql.table_normal');

        $res = $this->model->getCategoryByName($data['name']);
        if ($res) {
            throw new \think\Exception("分类名已存在");
        }
        try {
            $this->model->save($data);
        } catch (\Exception $e) {
            throwE($e, config('status.error'), "服务内部异常");
        }

        return $this->model->id;
    }


    /**
     * 修改栏目信息
     * @param $data
     * @return bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function edit($data) {
        if (isset($data['name']) && !empty($data['name'])){
            $res = $this->model->getCategoryByName($data['name']);
            /**
            1、不修改名字，那么获得的是本栏目信息，允许修改
            2、修改为一个未用的名字，那么res为空，允许修改
            3、修改为一个已存在的，并且为别的栏目的名字，那么获得的是别的栏目信息，不允许修改
             */
            //如果为3
            if ($res && $res['id'] != $data['id']) {
                throw new \think\Exception("分类名已存在");
            }
        }

        try {
            $result = $this->updateById($data);
        } catch (\Exception $e) {
            throwE($e, config('status.error'), "服务内部异常");
        }

        return $result;
    }

    /**
     * 获取所有普通栏目信息
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getNormalCategorys() {
        $field = "id, name, pid, path, listorder";
        $categorys = $this->model->getNormalCategorys($field);
        if (!$categorys) {
            return [];
        } else {
            return $categorys->toArray();
        }
    }

    /**
     * 获取栏目列表信息
     * @param $data
     * @param $num
     * @return array
     * @throws \think\db\exception\DbException
     */
    public function getListsAndChildCount($data, $num) {
        $result = $this->getLists($data, $num);
        if (!$result['data']) {
            return $result;
        }
        /**
         * 获取每个栏目下级栏目的数量的思路
         * 1、拿到当前获取的列表中的id，也就是他们下级栏目的pid
         * 2、in mysql 求count
         * 3、把count填充到列表中
         */
        $pids = array_column($result['data'], "id");
        $idCountResult = ($this->model->getChildCountInPids($pids))->toArray();
        if ($idCountResult) {
            $idCounts = [];
            foreach ($idCountResult as $countResult) {
                $idCounts[$countResult['pid']] = $countResult['count'];
            }

            foreach ($result['data'] as $k=>$v){
                $result['data'][$k]['childCount'] = $idCounts[$v['id']] ?? 0;
            }
        } else {
            foreach ($result['data'] as $k=>$v){
                $result['data'][$k]['childCount'] = 0;
            }
        }

        return $result;
    }

    /**
     * 获取面包屑导航
     * @param $pid
     * @return array|bool
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getBreadNav($pid) {
        $breadTree = [];
        $field = "id,name, pid";
        while ($pid != 0) {
            $info = $this->model->getFieldById($pid,$field,true);
            if ($info) {
                $info = $info->toArray();
                $breadTree[] = $info;
                $pid = $info['pid'];
            }
        }

        $breadTree[] = ['id'=>0,'pid'=>0, 'name'=>"栏目首页"];

        return array_reverse($breadTree);
    }

    /**
     * 根据上级id获取下级所有栏目
     * @param  integer $pid   [description]
     * @param  string  $field [description]
     * @return [type]         [description]
     */
    public function getNormalByPid($pid = 0, $field = "id,name,pid"){
        try {
            $res = $this->model->getNormalByPid($pid, $field);
        } catch(\Exception $e) {
            Log::error("getNormalByPid".$e->getMessage());
            return [];
        }
        
        return $res->toArray();
    }

    /**
     * 根据上级id获取下级所有栏目并和上级拼成树状结构
     * @param array $categoryIds
     * @return array
     */
    public function getCategoryTreeByPids($categoryIds = []){
        if (!is_array($categoryIds)){
            return [];
        }
        $categoryInfo = $this->model->getCategoryTreeByPids($categoryIds);
        $categoryInfo = $categoryInfo->toArray();
        if (empty($categoryInfo)){
            return [];
        }
        return Arr::getTree($categoryInfo);
    }

    /**
     * 更新栏目的redis内容
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function updateCategoryRedisInfo(){
        $arr = $this->getNormalCategorys();

        Cache::store('redis')->del(config('redis.category_pre'));
        foreach ($arr as $v){
            Cache::store('redis')->hSet(config('redis.category_pre'), $v['id'], json_encode($v));
        }
        $tree = Arr::getTree($arr);
        Cache::store('redis')->del(config('redis.category_tree'));
        foreach ($tree as $v) {
            Cache::store('redis')->hSet(config('redis.category_tree_pre'), $v['category_id'], json_encode($v));
            if (isset($v['list'])){
                foreach ($v['list'] as $value){
                    Cache::store('redis')->hSet(config('redis.category_tree_pre'), $value['category_id'], json_encode($value));
                }
            }
        }
    }

    /**
     * 获取redis里的栏目信息
     * @param bool $is_tree，获取树状结构
     * @param bool $is_all，获取所有
     * @param array $ids
     * @return array
     */
    public function getCategoryRedisInfo($is_tree = true, $is_all = true, $ids = ""){
        if ($is_tree){
            $key = config('redis.category_tree_pre');
        }else{
            $key = config('redis.category_pre');
        }

        if ($is_all){
            return Cache::store('redis')->hGetAll($key);
        }

        if (empty($ids)){
            return [];
        }

        $result =[];
        if (is_array($ids)){
            foreach ($ids as $id){
                $result[] = Cache::store('redis')->hGet($key, $id);
            }
        }else{
            $result = Cache::store('redis')->hGet($key, $ids);
        }

        return $result;
    }

    /**
     * 获取上下级分类的信息
     * @param $categoryId
     * @return array
     */
    public function getUpDownCategoryList($categoryId){
        $categoryInfo = $this->getCategoryRedisInfo(0,0,$categoryId);
        if (empty($categoryInfo)){
            return [];
        }
        $categoryInfo = json_decode($categoryInfo,true);
        //dump($categoryInfo);

        /*
        1、确定是几级分类
        2、如果是一级分类，那么查询所有二级，和第一个二级的所有3级
        3、如果是2级分类，那么查询上级分类，和同级分类，和它的所有下级（3级）
        4、如果是3级分类，那么查询path的一级分类，和该一级分类的所有下级（2级）和它上级的同级分类
        */

        //如果是一级分类
        if($categoryInfo['pid'] == 0) {
            $tree = json_decode($this->getCategoryRedisInfo(1,0,$categoryInfo['id']),true);
            $result = Arr::doSearchCategoryList($tree);
            return $result;
        }

        $path = explode(",", $categoryInfo['path']);
        $len = count($path);
        $tree = json_decode($this->getCategoryRedisInfo(1,0,$path[0]),true);
        if ($len == 2){
            //如果是二级分类
            $result = Arr::doSearchCategoryList($tree,false, $categoryInfo['id']);
        }elseif($len == 3){
            //如果是三级分类
            $result = Arr::doSearchCategoryList($tree,false, $path[1],$categoryInfo['id']);
        }


        //dump($result);exit;
        return $result;
    }

    /**
     * 获取下级分类名称
     * @param $categoryId
     * @return array
     */
    public function getDownCategoryList($categoryId){
        $tree = json_decode($this->getCategoryRedisInfo(1,0,$categoryId),true);
        $result = [];

        if (isset($tree['list'])){
            //排序
            array_multisort(array_column($tree['list'],'listorder'), SORT_DESC, $tree['list']);
            foreach ($tree['list'] as $k=>$v){
                $result[$k]['id'] = $v['category_id'];
                $result[$k]['name'] = $v['name'];
            }
        }
        return $result;
    }
}
