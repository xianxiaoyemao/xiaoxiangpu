<?php


namespace app\admin\controller\orders;
use app\common\controller\Backend;
use app\common\model\Address;
use app\common\model\OrdersDetail;
use app\Request;
use think\facade\Db;
use app\common\model\Orders as OrdersModel;
class Orders extends Backend{
    public function index(Request $request){
        if ($this->request->isAjax()) {
            if ($request->request('keyField')) {
                return $this->selectpage();
            }
            [$where, $sort, $order, $offset, $limit] = $this->buildparams();
            $total = (new OrdersModel())
                ->where($where)
                ->order('createtime desc')
                ->count();
            $list =  (new OrdersModel())
                ->where($where)
                ->order('createtime desc')
                ->limit($offset, $limit)
                ->select();
            $result = ['total' => $total, 'rows' => $list];
            return json($result);
        }
        $statusList = [
            '1' => '未付款',
            '2' => '已付款',
            '3' => '已发货',
            '4' => '已签收',
        ];
        $this -> assign('statusList',$statusList);
        return $this-> fetch();
    }


    /**
     * 详情.
     */
    public function detail($ids){
        $row =  (new OrdersModel())::where('id',$ids) -> find() -> toArray();
        $addressinfo = (new Address())::where('id',$row['addressid']) -> find();
        $detail= (new OrdersDetail())-> where('order_id',$ids) -> select() -> toArray();

//        dump($row);die;
//        $row['createtime'] = date('Y-m-d H:i:s',$row['createtime']);
        $this->assign('row', $row);
        return $this->fetch();
    }
}
