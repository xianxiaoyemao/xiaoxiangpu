<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/8/14
 * Time: 16:08
 */
namespace app\admin\controller\adv;

use app\common\controller\Backend;
//use app\common\model\Config as ConfigModel;
use think\facade\Db;
class Advpostion extends Backend{
    protected $model = null;
    protected $noNeedRight = ['check', 'rulelist'];
    public function initialize(){
        parent::initialize();
        $this ->model = Db::name('advposition');
    }
    public function index(){
        if ($this->request->isAjax()) {
            [$where, $sort, $order, $offset, $limit] = $this->buildparams();

            $total = $this -> model
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this -> model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $result = ['total' => $total, 'rows' => $list];

            return json($result);
        }
        return $this -> fetch();
    }
    public function add(){
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            if($params){
                $params['create_time'] =time();
                $this ->model -> insert($params);
                $this->success();
            }
            $this->error();
        }
        return $this-> fetch();
    }
    public function edit($ids=null){
        $row = Db::name('advposition') ->find($ids);
        if (! $row) {
            $this->error(__('No Results were found'));
        }
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            if($params){
                $data['title'] = $params['title'];
                $data['desc'] = $params['desc'];
                $data['status'] = $params['status'];
                $data['sort'] = $params['sort'];
                $data['update_time'] = time();
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
            Db::name('adv') -> where('pid',$ids)-> delete();
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
