<?php
namespace app\api\controller;
use app\BaseController;
use app\Request;
use app\common\model\Address;
use app\common\model\Order as OrderModel;
//use app\service\OrderService;
class Order extends BaseController
{
    //创建订单
    public function ceeateorder(Request $request){
        $param = $request -> post();
        $res = (new OrderModel) -> save($param);
    }

    //查看订单
    public function showorder(){

    }
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
    /**
     * 得到新订单号
     * @return  string
     */
    public function build_order_no(){
        /* 选择一个随机的方案 */
        mt_srand((double) microtime() * 1000000);
        return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }






}
