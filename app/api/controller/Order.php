<?php
namespace app\api\controller;
use app\BaseController;
use app\Request;
use app\common\model\Address;
use app\service\OrderService;
class Order extends BaseController
{
    //生成订单
    public function addorder(Request $request,OrderService $orderService){
        $uid = $this -> uid;
        $address_id = $request->post('address_id');
        $remark = $request->post('remark');
        $items = $request->post('items');
        $shop_id = $request->post('shop_id');
        $address = Address::find($address_id);
        return $orderService -> addOrder($uid,$address,$remark,$items,$shop_id);
    }







}
