<?php


namespace app\common\model;


class Order extends BaseModel
{
    /**
     * 生成订单
     * @param $uid
     * @param $order_no
     * @param $addressId
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function cacheKeyCreateOrder($uid,$order_no,$addressId,$pid,$skuid,$price,$quantity = 1){
        self::beginTrans();
        try {
            $count=app('redis')->lpop('goods_store'.$pid.$skuid);
            if(!$count){
                insertLog('已经抢光了哦');
                return '已经抢光了哦';
            }
            //查询订单是否存在
            $orderinfo = $this -> find($order_no);
            if($orderinfo){}
            $orderdata =[
                'user_id' => $uid,
                'order_no' => $order_no,
                'createtime' => time(),
                'payment_price' => $price,
                'status' => 1,
                'pay_status' =>1,
                'addressid' => $addressId
            ];
            $res = $this -> save($orderdata);
            if($res){//库存减少
                $number=1;
//                (new ProductSku)::where('id',$skuid) -> setInc('store');//默认加1 setInc('num',2); // 字段原值加2
               $store = (new ProductSku)::where('id',$skuid) -> setDec('store',$quantity);//默认减1 setDec('num',2); // 字段原值减2
                if($store){
                    insertLog('库存减少成功');
                }else{
                    insertLog('库存减少失败');
                }
            }
            $this->id;
            $deleiedata = [
                'order_id'=>$this->id,
                'product_id' =>$pid,
                'skuid' =>$skuid,
                'price' =>'price',
                'total_price' =>$quantity * $price,
                'specvalue' =>'specvalue',
                'number' => $quantity,
                'createtime' =>time(),
            ];
            (new OrderDetail)::save($deleiedata);

        } catch (\PDOException $e) {
            self::rollbackTrans();
            return self::setErrorInfo('生成订单时SQL执行错误错误原因：' . $e->getMessage());
        }
    }
    public function cacheKeyCreateOrder1($uid, $key,$addressId, $payType,  $useIntegral = false,
                                         $couponId = 0, $mark = '', $combinationId = 0, $pinkId = 0, $seckill_id = 0,
                                         $bargain_id = 0, $test = false, $isChannel = 0, $shipping_type = 1, $real_name = '', $phone = '', $storeId = 0)
    {}
}
