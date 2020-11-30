<?php


namespace app\admin\controller\orders;
use app\common\controller\Backend;
use app\Request;
use think\facade\Db;
class Orders extends Backend{
    public function index(Request $request){
        if ($this->request->isAjax()) {
            if ($request->request('keyField')) {
                return $this->selectpage();
            }
            [$where, $sort, $order, $offset, $limit] = $this->buildparams();
            $total = Db::name('orders') -> alias('o') -> join('user u','u.id=o.user_id')
                ->where($where)
//                ->where('o.status', 1)
                ->count();
            $list =  Db::name('orders') -> alias('o') -> join('user u','u.id=o.user_id')
                ->where($where)
//                ->where('o.status', 1)
//                ->order($sort, $order)
//                ->limit($offset, $limit)
                ->select();
            echo Db::name('orders') -> getLastSql();
            dump($list);die;
            $result = ['total' => $total, 'rows' => $list];
            return json($result);
        }
        return $this-> fetch();
    }
}
