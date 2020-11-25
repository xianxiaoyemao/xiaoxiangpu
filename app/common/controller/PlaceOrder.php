<?php
namespace app\common\controller;
use app\common\controller\Pay;
use app\common\model\Order;
//use app\common\model\PreSell;
use app\common\model\Product;
use app\common\model\ProductSku;
use app\common\util\TpshopException;
use think\facade\Cache;
class PlaceOrder extends Common {

//    public function __construct(Pay $pay){
//        $this->pay = $pay;
//        $this->order = new Order();
//    }

    protected $orderid = null;
    public function addNormalOrder($cartList,$areaid,$totle_price,$uf=0,$remark=''){
//        $this->check();
        self::beginTrans();
        $this->queueInc();
        $this->addOrder($cartList['user_id'],$areaid,$totle_price,$uf,$remark);//$uid,$shop_id,$addressid,$remark
        $this->addOrderGoods($cartList['product_id'],$cartList['sku_id'],$cartList['speckey'],$cartList['specvalue'],$cartList['quantity']);
//        $this->addShopOrder();
//        $this->addOrderBespeak();
    }

//    public function check($shopid){
//
//    }

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
    private function addOrder($userid,$areaid,$totle_price,$uf,$remark){
        $orderData = [
            'order_sn' => $this ->get_order_sn(), // 订单编号
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
//        $res = (new Order)::create($orderData);
//        $this -> orderid = $res -> id;
        $this -> orderid = 8;
    }


    /**
     * 插入订单商品表
     */
    private function addOrderGoods($pid,$skuid,$speckey,$spcevalue,$number){
        $store = (new ProductSku)::where('id',$skuid) -> setDec('store',$number);//默认减1 setDec('num',2); // 字段原值减2
        if($store){
            (new Product)::where('id',$pid) -> setDec('inventory',$number);
            insertLog('库存减少成功');
        }else{
            insertLog('库存减少失败');
        }
        $ordergooddata = [
            'order_id' => $this -> orderid,
            'product_id' => $pid,
            'skuid' => $skuid,
            'price' => (new ProductSku())::where('id',$skuid)-> value('price'),
            'speckey' => $speckey,
            'specvalue' => $spcevalue,
            'number' => $number,
            'createtime' => time()
        ];
        dump($ordergooddata);die;
    }

    private function addShopOrder(){

    }

    /**
     * 预售订单下单
     * @param PreSell $preSell
     */
    public function addPreSellOrder(PreSell $preSell)
    {
        $this->preSell = $preSell;
        $this->setPromType(4);
        $this->setPromId($preSell['pre_sell_id']);
        $this->check();
        $this->queueInc();
        $this->addOrder();
        $this->addOrderGoods();
        $reduce = tpCache('shopping.reduce');
        $this->userAddOrder($this->order);
        //Hook::listen('user_add_order', $this->order);//下单行为
        if($reduce == 1 || empty($reduce)){
            minus_stock($this->order);//下单减库存
        }
        //预售暂不至此积分余额优惠券支付
        // 如果应付金额为0  可能是余额支付 + 积分 + 优惠券 这里订单支付状态直接变成已支付
//            if ($this->order['order_amount'] == 0) {
//                update_pay_status($this->order['order_sn']);
//            }
//        $this->changUserPointMoney();//扣除用户积分余额
        $this->queueDec();
    }
}
