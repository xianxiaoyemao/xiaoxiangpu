<?php


namespace app\admin\controller\product;
use app\common\controller\Backend;
use app\common\model\ProductSku;
use app\Request;

class Skulist extends Backend{
    public function index(Request $request){
        if ($this->request->isAjax()) {
            if ($request->request('keyField')) {
                return $this->selectpage();
            }
            [$where, $sort, $order, $offset, $limit] = $this->buildparams();
            $total = (new ProductSku())::with('product')
                ->where($where)
//                ->order('')
                ->count();
            $list =  (new ProductSku())::with('product')
                ->where($where)

                ->limit($offset, $limit)
                ->select();
            $result = ['total' => $total, 'rows' => $list];
            return json($result);
        }
        return $this -> fetch();
    }

}
