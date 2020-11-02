<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2020/8/23
 * Time: 17:03
 */
namespace app\admin\controller\general;
use fast\Random;
use think\facade\Session;
use app\admin\model\Admin;
use app\common\controller\Backend;
use think\facade\Db;
use app\admin\model\AdminLog;
class Profile extends Backend{
    /**
     * 查看.
     */
    public function index(){
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            $model =new AdminLog();
            [$where, $sort, $order, $offset, $limit] = $this->buildparams();
            $total = $model
                ->where($where)
                ->where('admin_id', $this->auth->id)
                ->order($sort, $order)
                ->count();
            $list = $model
                ->where($where)
                ->where('admin_id', $this->auth->id)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            $result = ['total' => $total, 'rows' => $list];

            return json($result);
        }

        return $this->fetch();
    }

    /**
     * 更新个人信息.
     */
    public function update()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            $params = array_filter(array_intersect_key($params,
                array_flip(['email', 'nickname', 'password', 'avatar'])));
            unset($v);
            if (isset($params['password'])) {
                $params['salt'] = Random::alnum();
                $params['password'] = md5(md5($params['password']).$params['salt']);
            }
            if ($params) {
                $admin = Admin::find($this->auth->id);
                $admin->save($params);
                //因为个人资料面板读取的Session显示，修改自己资料后同时更新Session
                Session::set('admin', $admin->toArray());
                $this->success();
            }
            $this->error();
        }
    }
}