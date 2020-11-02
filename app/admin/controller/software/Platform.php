<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/8/22
 * Time: 16:43
 */
namespace app\admin\controller\software;
use app\common\controller\Backend;
use think\facade\Db;
class Platform extends Backend{
    protected $model = null;
    protected $noNeedRight = ['check', 'rulelist'];
    public function initialize(){
        parent::initialize();
        $this -> model = Db::name('business_application_platform');
    }
    public function index(){
        if ($this->request->isAjax()) {
            [$where, $sort, $order, $offset, $limit] = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->order($sort, $order)
                ->count();
//            $sort ='sort';$order='desc';
            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select() ->toArray();
//            echo $this -> model -> getLastSql();
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
        $row = $this ->model ->find($ids);
        if (! $row) {
            $this->error(__('No Results were found'));
        }
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            if($params){
                $data['name'] = $params['name'];
                $data['title'] = $params['title'];
                $data['appkey'] = $params['appkey'];
                $data['appsecret'] = $params['appsecret'];
                $data['redirect_uri'] = $params['redirect_uri'];
                $data['sort'] = $params['sort'];
                $data['status'] = $params['status'];
                $data['istype'] = $params['istype'];
                $data['loginurl'] = $params['loginurl'];
                $data['updatetime'] = time();
//                epre($data);exit;
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