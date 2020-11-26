<?php


namespace app\common\controller;
use app\common\model\Orders;
use app\common\model\OrdersDetail;
use app\common\model\OrdersShop;
use app\common\model\Product;
use app\common\model\ProductSku;
use app\common\util\TpshopException;
use think\facade\Cache;
use think\facade\Db;
class PlaceOrder extends CommonController{
    protected $orderid = null;
    public function addNormalOrder($uid,$cartList,$areaid,$uf=0,$remark='',$dining=0,$take_time=0){
        self::beginTrans();
        try {
            $this->queueInc();
            $order_no = [];
            foreach ($cartList as $key => $val){
                $order_sn =  $this->addOrder($uid,$areaid,$val['cartlist'],$uf,$remark);//$uid,$shop_id,$addressid,$remark
                if($dining == 0){
                    $this->addOrderGoods($order_sn['orderid'],$val['cartlist']);
                }else{
                    $this->addShopOrder($order_sn['orderid'],$val['cartlist'],$take_time,$val['shopid']);
                }
                if($order_sn){
                    array_push($order_no,$order_sn['order_sn']);
                }else{
                    $order_no = $order_no;
                }
            }
            $this-> queueDec();
            self::commitTrans();
            return $order_no;
        }catch  (\PDOException $e) {
            self::rollbackTrans();
            throw new TpshopException("订单入库", 0,  '生成订单时SQL执行错误错误原因：' . $e->getMessage());
        }
    }
    private function queueInc(){
        $queue = Cache::get('queue');
        if($queue >= 100){
            throw new TpshopException('提交订单', 0, "当前人数过多请耐心排队!");
        }
        Cache::inc('queue');
    }
    /**
     * 订单提交结束
     */
    private function queueDec()
    {
        Cache::dec('queue');
    }

    /**
     * 插入订单表
     * @throws TpshopException
     */
    private function addOrder($userid,$areaid,$cartList,$uf,$remark){
        foreach ($cartList as $key => $val) {
            //查询库存
            $gstoke = (new ProductSku())::where('id', $val['skuid'])->value('stock');
            if ($gstoke == 0) {
                return;
            }
            $store = (new ProductSku)::where('id', $val['skuid'])
                ->update(['stock' => $gstoke - (int)$val['quantity']]);
            //默认减1 setDec('num',2); setDec('stock',1)// 字段原值减2
            $product = (new Product())::where('id', $val['pid'])->field('name,inventory,sales')->find();
            if ($store) {//Db::name('product_sku')
                //查询商品名称
                (new Product)::where('id', $val['pid'])->
                update(['inventory' => (int)$product['inventory'] - (int)$val['quantity'],'sales'=>(int) $product['sales'] + (int)$val['quantity']]);
                self::insertLog($product['name'] . '下单成功');
            } else {
                self::insertLog($product['name'] . '下单失败');
            }
        }
        //获取总金额
        $totle_price = $this ->  getCartPriceInfo($cartList)['total_price'];
        $order_sn = (new CommonController()) -> get_order_sn();
        $orderData = [
            'order_sn' => $order_sn, // 订单编号
            'user_id' => $userid, // 用户id
            'addressid' => $areaid,
            'goods_price' => $totle_price,
            'shipping_price' => $uf,
            'payment_price'=> floatval($totle_price) - floatval($uf),
            'status' => 1,
            'pay_status'=>2,
            'createtime' => time(), // 下单时间
            'remark' => $remark
        ];
        if($orderData["payment_price"] < 0){
            throw new TpshopException("订单入库", 0,  '订单金额不能小于0');
        }
        $res = (new Orders)::create($orderData);
        $data = ['orderid'=> $res -> id,'order_sn' => $order_sn];
        return $data;
    }
    /**
     * 获取购物车的价格详情
     * @param $cartList |购物车列表
     * @return array
     */
    public function getCartPriceInfo($cartList = null){
        $total_price = $goods_fee = $goods_num = 0;//初始化数据。商品总额/节约金额/商品总共数量
        if ($cartList) {
            foreach ($cartList as $cartKey => $val) {
                $total_price += $val['price'] * $val['quantity'];
                $goods_fee += 0;
                $goods_num += $val['quantity'];
            }
        }
        $total_price = round($total_price,2);
        $goods_fee = round($goods_fee,2);
        return compact('total_price', 'goods_fee', 'goods_num');
    }
    /**
     * 插入订单商品表
     */
    private function addOrderGoods($orderid,$cartList){
        $orderGoodsAllData = [];
        foreach ($cartList as $key => $val) {
            $ordergooddata = [
                'order_id' => $orderid,
                'product_id' => $val['pid'],
                'skuid' => $val['skuid'],
                'price' => $val['price'],
                'speckey' => $val['speckey'],
                'specvalue' => $val['specvalue'],
                'number' => $val['quantity'],
                'total_price' => $val['price'] * (int)$val['quantity'],
                'createtime' => time()
            ];
            array_push($orderGoodsAllData, $ordergooddata);
        }
        (new OrdersDetail)-> saveAll($orderGoodsAllData);
    }

    private function addShopOrder($orderid,$cartList,$task_time,$shop_id){
        $orderGoodsAllData = [];
        foreach ($cartList as $key => $val) {
            $ordergooddata = [
                'order_id' => $orderid,
                'product_id' => $val['pid'],
                'skuid' => $val['skuid'],
                'price' => $val['price'],
                'speckey' => $val['speckey'],
                'specvalue' => $val['specvalue'],
                'number' => $val['quantity'],
                'task_time' => $task_time,
                'shop_id' => $shop_id,
                'createtime' => time()
            ];
            array_push($orderGoodsAllData, $ordergooddata);
        }
//        (new UserLog()) -> saveAll($orderlog);
//        echo  (new UserLog()) -> getLastSql();die;
//        epre($orderlog);
        (new OrdersShop()) -> insertAll($orderGoodsAllData);
    }

}
