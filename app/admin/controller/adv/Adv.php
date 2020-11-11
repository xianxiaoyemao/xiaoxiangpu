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
//    protected $advmodel =null;

    public function initialize(){
        parent::initialize();
        $this -> model = new \app\admin\model\Adv();
        $this -> advmodel = new \app\admin\model\Advposition();
        $this -> advpostion= $this -> advmodel -> where('status','normal') -> order('id asc') -> select();
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
                -> with('advposition')
                ->where($where)
                ->order($sort, $order)
                ->count();
            $list = $this->model
                -> with('advposition')
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select() ->toArray();
            foreach ($list as $key => $value) {
                $list[$key]['posation'] = $value['advposition']['title'];
                unset($list[$key]['advposition']);
            }
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
                $this ->model -> save($params);
                $this->success();
            }
            $this->error();
        }
        return $this -> fetch();
    }
    public function edit($ids=null){
        $row = $this -> model ->find($ids);
        if (! $row) {
            $this->error(__('No Results were found'));
        }
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            if($params){
                $params['update_time'] = time();
                $result = $row->save($params);
                if ($result === false) {
                    $this->error($row->getError());
                }
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
            $this->model -> destroy($ids);
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
