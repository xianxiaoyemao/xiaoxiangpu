<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/8/14
 * Time: 16:22
 */
namespace app\admin\controller\adv;

use app\common\controller\Backend;
//use app\common\model\Config as ConfigModel;
use think\facade\Db;
class Adv extends Backend{
    protected $model = null;
    protected $noNeedRight = ['check', 'rulelist'];
    protected $advpostion;
    public function initialize(){
        parent::initialize();
        $this -> model = Db::name('adv');
        $this -> advpostion= Db::name('advposition') -> where('status','normal') -> order('id asc') -> select();
        $advgroup =[];
        foreach ( $this -> advpostion as $k => $v) {
            $advgroup[$v['id']] = $v['title'];
        }
        $this -> assign('advgroup',$advgroup);
    }
    public function index(){
        if ($this->request->isAjax()) {
            [$where, $sort, $order, $offset, $limit] = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->order($sort, $order)
                ->count();
            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select() ->toArray();
            foreach ( $this -> advpostion as $kk => $vv){
                foreach ($list as $k => &$v) {
                    if($v['pid'] == $vv['id']){
                        $list[$k]['posation'] = $vv['title'];
                    }
                }
            }
            unset($v);
            $result = ['total' => $total, 'rows' => $list];
            return json($result);
        }
        return $this -> fetch();
    }

    public function add(){
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            if($params){
                $params['createtime'] =time();
                $this ->model -> insert($params);
                $this->success();
            }
            $this->error();
        }
        return $this -> fetch();
    }
    public function edit($ids=null){
        $row = Db::name('adv') ->find($ids);
        if (! $row) {
            $this->error(__('No Results were found'));
        }
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            if($params){
                $data['title'] = $params['title'];
                $data['status'] = $params['status'];
                $data['sort'] = $params['sort'];
                $data['status'] = $params['status'];
                $data['updatetime'] = time();
                $this ->model -> where('id',$params['id']) ->  update($params);
                $this->success();
            }
            $this->error();

        }
        $this->assign('row', $row);
        return $this-> fetch();
    }
    /**
     * 删除.
     */
    public function del($ids = ''){
        if ($ids) {
            $this -> model -> where('id',$ids) -> delete();
            $this->success();
        }
        $this->error();
    }
    /**
     * 批量更新.
     *
     * @internal
     */
    public function multi($ids = '')
    {
        // 管理员禁止批量操作
        $this->error();
    }

    public function selectpage()
    {
        return parent::selectpage();
    }
}