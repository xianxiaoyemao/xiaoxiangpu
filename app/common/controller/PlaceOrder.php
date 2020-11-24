<?php
namespace app\common\controller;
use app\common\controller\Pay;
use app\common\model\Order;
//use app\common\model\PreSell;
use app\common\util\TpshopException;
use think\facade\Cache;
class PlaceOrder extends Common {

//    public function __construct(Pay $pay){
//        $this->pay = $pay;
//        $this->order = new Order();
//    }

    public function addNormalOrder($cartList,$areaid,$remark=''){
//        $this->check($cartList['product']['shop_id']);
        $this->queueInc();
        $this->addOrder($cartList['user_id'],$areaid,$remark);//$uid,$shop_id,$addressid,$remark
        $this->addOrderGoods();
//        $this->addShopOrder();
//        $this->addOrderBespeak();
    }

    public function check($shopid){

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
    private function addOrder($userid,$areaid,$remark){
        $orderData = [
            'order_sn' => $this ->get_order_sn(), // 订单编号
            'user_id' => $userid, // 用户id
            'addressid' => $areaid,
            'goods_price' => 0,
            'shipping_price' => 0,
            'payment_price'=>0,
            'status' => 1,
            'pay_status'=>2,
            'add_time' => time(), // 下单时间
            'remark' => $remark
            //'invoice_title' => ($this->invoiceDesc != '不开发票') ?  $invoice_title : '', //'发票抬头',
            //'invoice_desc' => $this->invoiceDesc, //'发票内容',
            //'goods_price' => $this->pay->getGoodsPrice(),//'商品价格',
            //'shipping_price' => $this->pay->getShippingPrice(),//'物流价格',
            //'real_shipping_price' => $this->pay->getRealShippingPrice(),//'真实物流价格'
            //'user_money' => $this->pay->getUserMoney(),//'使用余额',
            //'coupon_price' => $this->pay->getCouponPrice(),//'使用优惠券',
            //'card_price'=>$this->pay->getCardPrice(),//使用购物卡
            //'integral' => $this->pay->getPayPoints(), //'使用积分',
            //'integral_money' => $this->pay->getIntegralMoney(),//'使用积分抵多少钱',
            //'total_amount' => $this->pay->getTotalAmount(),// 订单总额
            //'order_amount' => $this->pay->getOrderAmount(),//'应付款金额',
            //'add_time' => time(), // 下单时间
            //'from_terminal' => $this->from_terminal, //'下单的终端设备',
        ];
        dump($orderData);die;


    }


    /**
     * 插入订单商品表
     */
    private function addOrderGoods(){

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
