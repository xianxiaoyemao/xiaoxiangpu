<?php


namespace app\common\model;


class Order extends BaseModel{
    /**
     * 生成订单
     * @param $uid
     * @param $order_no
     * @param $addressId
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function cacheKeyCreateOrder($uid,$order_no,$addressId,$pid,$skuid,$price,$specvalue,$quantity = 1){
        self::beginTrans();
        try {
            $count=app('redis')->lpop('goods_store'.$pid.$skuid);
            if(!$count){
                insertLog('已经抢光了哦');
                return  0;
            }
            //查询订单是否存在
            $orderinfo = $this -> find($order_no);
            if($orderinfo){
                return '订单已存在';
            }
            //减库存
//            (new ProductSku)::where('id',$skuid) -> setInc('store');//默认加1 setInc('num',2); // 字段原值加2
            $store = (new ProductSku)::where('id',$skuid) -> setDec('store',$quantity);//默认减1 setDec('num',2); // 字段原值减2
            if($store){
                (new Product) -> where('id',$pid) -> setDec('inventory',$quantity);
                insertLog('库存减少成功');
            }else{
                insertLog('库存减少失败');
            }
            $orderdata =[
                'user_id' => $uid,
                'order_no' => $order_no,
                'createtime' => time(),
                'payment_price' => $price * $quantity,
                'status' => 1,
                'pay_status' =>1,
                'addressid' => $addressId
            ];
            $orderid = Order::create($orderdata);
            $oid = $orderid -> id;
            $deleiedata = [
                'order_id'=> $oid,
                'product_id' =>$pid,
                'skuid' => $skuid,
                'price' => $price,
                'total_price' =>$quantity * $price,
                'specvalue' => $specvalue,
                'number' => $quantity,
                'createtime' =>time(),
            ];
            $result = (new OrderDetail)::save($deleiedata);
            if($result){
                return 1;
            }else{
                return 2;
            }
        } catch (\PDOException $e) {
            self::rollbackTrans();
            return '生成订单时SQL执行错误错误原因：' . $e->getMessage();
        }
    }

}
