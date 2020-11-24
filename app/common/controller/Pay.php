<?php
/**
 * tpshop
 * ============================================================================
 * 版权所有 2015-2027 深圳搜豹网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.tp-shop.cn
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和使用 .
 * 不允许对程序代码以任何形式任何目的的再发布。
 * 采用最新Thinkphp6
 * ============================================================================
 * Author: dyr
 * Date: 2017-12-04
 */

namespace app\common\controller;
use app\common\model\Cart;
//use app\common\model\CouponList;
use app\common\model\Shops;
use app\common\model\User;
use app\common\util\TpshopException;
use think\Model;
use think\facade\Db;
/**
 * 计算价格类
 * Class CatsLogic
 * @package Home\Logic
 */
class Pay
{
    public $message;
    protected $payList;
    protected $userId;
    protected $user;

    private $totalAmount = 0;//订单总价
    private $orderAmount = 0;//应付金额
    private $shippingPrice = 0;//物流费
	private $realShippingPrice = 0;//实际物流费，不免邮时=$shippingPrice,免邮时$shippingPrice为零，而$realShippingPrice记录不免邮时的运费
    private $goodsPrice = 0;//商品总价
    private $cutFee = 0;//共节约多少钱
    private $totalNum = 0;// 商品总共数量
    private $integralMoney = 0;//积分抵消金额
    private $userMoney = 0;//使用余额
    private $payPoints = 0;//使用积分
    private $couponPrice = 0;//优惠券抵消金额
    private $cardPrice = 0;//购物卡抵消金额

    private $orderPromId;//订单优惠ID
    private $orderPromAmount = 0;//订单优惠金额
    private $couponId;
    private $shop;//自提点
    private $shoppingCardBalance=[];//购物卡金额

    /**
     * 计算订单表的普通订单商品
     * @param $order_goods
     * @return $this
     * @throws TpshopException
     */
    public function payOrder($order_goods){
        $this->payList = $order_goods;
        $order = Db::name('order')->where('order_id',  $this->payList[0]['order_id'])->find();
        if(empty($order)){
            throw new TpshopException('计算订单价格', 0, ['status' => -9, 'msg' => '找不到订单数据', 'result' => '']);
        }
        $reduce = tpCache('shopping.reduce');
        if($order['pay_status'] == 0 && $reduce == 2){
            $goodsListCount = count($this->payList);
            for ($payCursor = 0; $payCursor < $goodsListCount; $payCursor++) {
                $goods_stock = getGoodNum($this->payList[$payCursor]['goods_id'], $this->payList[$payCursor]['spec_key']); // 最多可购买的库存数量
                if($goods_stock <= 0 && $this->payList[$payCursor]['goods_num'] > $goods_stock){
                    throw new TpshopException('计算订单价格', 0, ['status' => -9, 'msg' => $this->payList[$payCursor]['goods_name'].','.$this->payList[$payCursor]['spec_key_name'] . "库存不足,请重新下单", 'result' => '']);
                }
            }
        }
        $this->Calculation();
        return $this;
    }

    /**
     * 计算购买购物车的商品
     * @param $cart_list
     * @return $this
     * @throws TpshopException
     */
    public function payCart($cart_list){
        $this->payList = $cart_list;
        $goodsListCount = count($this->payList);
        if ($goodsListCount == 0) {
            throw new TpshopException('计算订单价格', 0, ['status' => -9, 'msg' => '你的购物车没有选中商品', 'result' => '']);
        }
        $this->Calculation();
        return $this;
    }

    /**
     * 计算购买商品表的商品
     * @param $goods_list
     * @return $this
     * @throws TpshopException
     */
    public function payGoodsList($goods_list){
        $goodsListCount = count($goods_list);
        if ($goodsListCount == 0) {
            throw new TpshopException('计算订单价格', 0, ['status' => -9, 'msg' => '你的购物车没有选中商品', 'result' => '']);
        }
        $discount = $this->getDiscount();
        for ($goodsCursor = 0; $goodsCursor < $goodsListCount; $goodsCursor++) {
            //优先使用member_goods_price，没有member_goods_price使用goods_price
            if(empty($goods_list[$goodsCursor]['member_goods_price'])){
                //积分商品不打折。因为是全积分商品打会员折扣，结算会出现负数
                if($goods_list[$goodsCursor]['exchange_integral'] > 0){
                    $goods_list[$goodsCursor]['member_goods_price'] = $goods_list[$goodsCursor]['goods_price'];
                }else{
                    $goods_list[$goodsCursor]['member_goods_price'] = $discount * $goods_list[$goodsCursor]['goods_price'];
                }

            }
        }
        $this->payList = $goods_list;
//        dump($this->payList);die;
//        $this->Calculation();
        return $this;
    }

    /**
     * 初始化计算
     */
    private function Calculation()
    {
        //查出搭配购的商品
        if($this->payList){
            $Cart = new Cart();
            foreach ($this->payList as $cartKey => $cartVal) {
                if ($cartVal['prom_type'] == 7) {
                    $arr = $Cart->where(['combination_group_id' => $cartVal['id'], ['id' , '<>', $cartVal['id']]])->select();
                    //$this->payList = array_merge($this->payList, $arr);
                    $this->payList =$this->payList->merge($arr);
                }
            }
        }

        $goodsListCount = count($this->payList);

        for ($payCursor = 0; $payCursor < $goodsListCount; $payCursor++) {
            $this->payList[$payCursor]['goods_fee'] = $this->payList[$payCursor]['goods_num'] * $this->payList[$payCursor]['member_goods_price'];    // 小计
            $this->goodsPrice += $this->payList[$payCursor]['goods_fee']; // 商品总价
            if(array_key_exists('market_price',$this->payList[$payCursor])){
                $this->cutFee += $this->payList[$payCursor]['goods_num'] * ($this->payList[$payCursor]['market_price'] - $this->payList[$payCursor]['member_goods_price']);// 共节约
            }
            $this->totalNum += $this->payList[$payCursor]['goods_num'];
        }
        $this->orderAmount = $this->goodsPrice;
        $this->totalAmount = $this->goodsPrice;
    }


    /**
     * 用于余额支付-设置订单金额总额
     */
    public function setOrderAmount($orderAmount = 0)
    {
        $this->orderAmount = $orderAmount;
        $this->totalAmount = $orderAmount;
        return $this;
    }


    /**
     * 设置用户ID
     * @param $user_id
     * @return $this
     * @throws TpshopException
     */
    public function setUserId($user_id)
    {
        $this->userId = $user_id;
        $this->user = (new User())->where(['id' => $this->userId])->find();
        if(empty($this->user)){
            throw new TpshopException("计算订单价格",0,['status' => -9, 'msg' => '未找到用户', 'result' => '']);
        }
        return $this;
    }

    public function setShopById($shop_id){
        if($shop_id){
            $this->shop = Shops::find($shop_id);
        }
        return $this;
    }

    /**
     * 使用积分
     * @throws TpshopException
     * @param $pay_points
     * @param $is_exchange|是否有使用积分兑换商品流程
     * @param $port
     * @return $this
     */
    public function usePayPoints($pay_points, $is_exchange = false, $port = "pc")
    {
        if($pay_points > 0 && $this->orderAmount > 0){
            //积分规则修改后的逻辑
            $isUseIntegral = tpCache('integral.is_use_integral');
            $isPointMinLimit = tpCache('integral.is_point_min_limit');
            $isPointRate = tpCache('integral.is_point_rate');
            $isPointUsePercent = tpCache('integral.is_point_use_percent');
            $point_rate = tpCache('integral.point_rate');
            if($is_exchange == false){
                if($isUseIntegral==1 && $isPointUsePercent==1) {
                    $use_percent_point = tpCache('integral.point_use_percent')/100;
                }else{
                    $use_percent_point = 1;
                }
                if($isUseIntegral==1 && $isPointMinLimit==1) {
                    $min_use_limit_point = tpCache('integral.point_min_limit');
                }else{
                    $min_use_limit_point = 0;
                }
                if($isUseIntegral == 0 || $isPointRate != 1){
                    throw new TpshopException("计算订单价格",0,['status' => -1, 'msg' => '该笔订单不能使用积分', 'result' => '']);
                }
                if($use_percent_point > 0 && $use_percent_point < 1){
                    //计算订单最多使用多少积分,默认只用最大值
                    $pay_points = intval($this->totalAmount * $point_rate * $use_percent_point);
                    if($pay_points > $this->user['pay_points']){
                        //如果我的积分比最大值小，那么就直接使用我的全部积分
                        $pay_points = $this->user['pay_points'];
                    }

                }
                //计算订单最多使用多少积分(没勾选比例的情况)
                $next_point_limit = intval($this->totalAmount * $point_rate * $use_percent_point);
                if($port!="mobile" && $pay_points > $next_point_limit){
                    throw new TpshopException("计算订单价格", 0, ['status' => -1, 'msg' => "该笔订单, 您使用的积分不能大于" . $next_point_limit, 'result' => '']);
                }
                if($pay_points > $this->user['pay_points']){
                    throw new TpshopException("计算订单价格",0,['status' => -5, 'msg' => "你的账户可用积分为:" . $this->user['pay_points'], 'result' => '']);
                }
                if ($min_use_limit_point > 0 && $this->user['pay_points'] < $min_use_limit_point) {
                    throw new TpshopException("计算订单价格",0,['status' => -1, 'msg' => "积分小于".$min_use_limit_point."时 ，不能使用积分", 'result' => '']);
                }
                $order_amount_pay_point = round($this->orderAmount * $point_rate,2);
                //$order_amount_pay_point = $this->orderAmount * $point_rate;
                if($pay_points > $order_amount_pay_point){
                    $this->payPoints = $order_amount_pay_point;
                }else{
                    $this->payPoints = $pay_points;
                }
                $this->integralMoney = $this->payPoints / $point_rate;
                $this->orderAmount = $this->orderAmount - $this->integralMoney;
            }else{
                //积分兑换流程
                if($pay_points <= $this->user['pay_points']){
                    $this->payPoints = $pay_points;
                    $this->integralMoney = $pay_points / $point_rate;//总积分兑换成的金额
                }else{
                    $this->payPoints = 0;//需要兑换的总积分
                    $this->integralMoney = 0;//总积分兑换成的金额
                }
                $this->orderAmount = $this->orderAmount - $this->integralMoney;
            }

        }
        return $this;
    }

    /**
     * 使用余额
     * @throws TpshopException
     * @param $user_money
     * @return $this
     */
    public function useUserMoney($user_money)
    {
        if($user_money > 0){
            $this->checkUserWithdrawals();
            if($user_money > $this->user['user_money']){
//                throw new TpshopException("计算订单价格",0,['status' => -6, 'msg' =>  "你的账户可用余额为:" . $this->user['user_money'], 'result' => '']);
            }
            if($this->orderAmount > 0){
                if($user_money > $this->orderAmount){
                    $this->userMoney = $this->orderAmount;
                    $this->orderAmount = 0;
                }else{
                    $this->userMoney = $user_money;
                    $this->orderAmount = $this->orderAmount - $this->userMoney;
                }
            }
        }
        return $this;
    }
    /*
     * 使用购物卡
     */
    public function useShoppingCard($shopping_card_ids)
    {
        if(count($this->payList)==1){
            $now = time();
            $ids=explode('_',$shopping_card_ids);

            $cat_info = Db::name('goods_category')->where(array('id'=>$this->payList[0]['goods']['cat_id']))->find();
            $cat_path = explode('_', $cat_info['parent_id_path']);

            $shopping_card_goods=Db::name('shopping_card_goods')->where(['goods_id'=>$this->payList[0]['goods_id']])
                ->whereOr(['goods_category_id'=>['in',$cat_path]])
                ->select();

            $card_ids='-1';
            foreach($shopping_card_goods as $good){
                $card_ids.=','.$good['card_id'];
            }

            $where['l.id']=['in',$ids];
            $where['l.uid']=$this->user['user_id'];
            $where['l.balance']=['>',0];
            $where['l.status']=0;

            $condition="(l.add_time+c.validity*86400 > $now OR c.validity = 0) AND (c.use_type=0 OR c.id IN ($card_ids))";

            $cards = Db::name('shopping_card_list')
                ->alias('l')
                ->join('shopping_card c','c.id=l.cid')
                ->where($where)
                ->where($condition)
                ->order('balance','DESC')
                ->field('l.*')
                ->select();


            foreach($cards as $card){
                $start_balance=$card['balance'];
                if($this->orderAmount>0){
                    if($card['balance']>$this->orderAmount){
                        $card['balance']-=$this->orderAmount;
                        $this->cardPrice+=$this->orderAmount;
                        $this->orderAmount=0;
                    }else{
                        $this->cardPrice+=$card['balance'];
                        $this->orderAmount-=$card['balance'];
                        $card['balance']=0;
                    }
                }else{
                    break;
                }
                $variation=$start_balance-$card['balance'];
                $this->shoppingCardBalance[]=['id'=>$card['id'],'balance'=>$card['balance'],'variation'=>$variation];
            }
        }
        return $this;
    }

    /**
     * 检查用户账户是否存在正在提现的订单,存在则可用余额 = 可用余额 - 提现总金额
     * status提现状态为 0表示为已发起提现申请 1审核成功待转账的
     */
    private function checkUserWithdrawals()
    {
        $check = new \app\common\logic\UsersLogic();
        $check->checkUserWithdrawals($this->user);
    }

    /**
     * 减去应付金额
     * @param $cut_money
     * @return $this
     */
    public function cutOrderAmount($cut_money){
        $this->orderAmount = $this->orderAmount - $cut_money;
        return $this;
    }

    /**
     * 使用优惠券
     * @param $coupon_id
     * @return $this
     */
    public function useCouponById($coupon_id){
        if($coupon_id > 0){
            $couponList = new CouponList();
            $userCoupon = $couponList->where(['uid'=>$this->user['user_id'],'id'=>$coupon_id])->find();
            if($userCoupon){
                $coupon = Db::name('coupon')->where(['id'=>$userCoupon['cid'],'status'=>1])->find(); // 获取有效优惠券类型表
                if($coupon){
                    $this->couponId = $coupon_id;
                    if ($this->orderAmount > 0) {
                        if ($coupon['money'] > $this->orderAmount) {
                            $this->couponPrice = $this->orderAmount;
                            $this->orderAmount = 0;
                        } else {
                            $this->couponPrice = $coupon['money'];
                            $this->orderAmount = $this->orderAmount - $this->couponPrice;
                        }
                    }
                }
            }
        }
        return $this;
    }

    /**
     * 配送
     * @param $district_id
	 * @param $is_free
     * @throws TpshopException
     * @return $this
     */
    public function delivery($district_id, $is_free = false){
        if (array_key_exists('is_virtual', $this->payList[0]) && $this->payList[0]['is_virtual'] == 0) {
            if (empty($this->shop) && empty($district_id)) {
                throw new TpshopException("计算订单价格", 0, ['status' => -1, 'msg' => '请填写收货信息', 'result' => ['']]);
            }
        }
        $GoodsLogic = new GoodsLogic();
        $checkGoodsShipping = $GoodsLogic->checkGoodsListShipping($this->payList, $district_id);
        foreach($checkGoodsShipping as $shippingKey => $shippingVal){
            if($shippingVal['shipping_able'] != true){
                throw new TpshopException("计算订单价格",0,['status'=>-1, 'code' => 301,
                    'msg'=>'订单中部分商品【 '.$shippingVal['goods_name'].' 】不支持对当前地址的配送请返回购物车修改',
                    'result'=>['goods_shipping'=>$checkGoodsShipping]]);
            }
        }
        //使用自提点不计算运费
        if(!empty($this->shop)){
            return $this;
        }
        //预售活动暂不计算运费
        if ($this->payList[0]['prom_type'] == 4) {
            return $this;
        }
        $freight_free = tpCache('shopping.freight_free'); // 全场满多少免运费
		$this->realShippingPrice = $GoodsLogic->getFreight($this->payList, $district_id); //实际需要的运费，当免邮时，会员显示为0，而supplier_shipping_price为原本需要付的运费
        if(($this->goodsPrice < $freight_free || $freight_free == 0) && !$is_free){
            $this->shippingPrice = $this->realShippingPrice;
            $this->orderAmount = $this->orderAmount + $this->shippingPrice;
            $this->totalAmount = $this->totalAmount + $this->shippingPrice;
        }else{
            $this->shippingPrice = 0;
        }
        return $this;
    }

    /**
     * 获取折扣
     * @return int
     */
    private function getDiscount()
    {
        if(empty($this->user['discount'])){
            return 1;
        }else{
            return $this->user['discount'];
        }
    }

    /**
     * 使用订单优惠
     */
    public function orderPromotion()
    {
        //砍价不参与优惠
        if($this->payList[0]['prom_type'] == 8){
            return $this;
        }
        $time = time();
        $order_prom_where = [['type','<',2],['end_time','>',$time],['start_time','<',$time],['money','<=',$this->goodsPrice],'is_close'=>0];
        $orderProm = Db::name('prom_order')->where($order_prom_where)->order('money desc')->find();
        if ($orderProm) {
            if ($orderProm['type'] == 0) {
                $expressionAmount = round($this->goodsPrice * $orderProm['expression'] / 100, 2);//满额打折
                $this->orderPromAmount = round($this->goodsPrice - $expressionAmount, 2);
                $this->orderPromId = $orderProm['id'];
            } elseif ($orderProm['type'] == 1) {
                $this->orderPromAmount = $orderProm['expression'];
                $this->orderPromId = $orderProm['id'];
            }
        }
        $this->orderAmount = $this->orderAmount - $this->orderPromAmount;
        return $this;
    }

    /**
     * 获取实际上使用的余额
     * @return int
     */
    public function getUserMoney()
    {
        return $this->userMoney;
    }

    /*
     * 获取购物卡金额
     */
    public function getShoppingCardBalance()
    {
        return $this->shoppingCardBalance;
    }

    /**
     * 获取订单总价
     * @return int
     */
    public function getTotalAmount()
    {
        return number_format($this->totalAmount, 2, '.', '');
    }

    /**
     * 获取订单应付金额
     * @return int
     */
    public function getOrderAmount()
    {
        return number_format($this->orderAmount, 2, '.', '');
    }

    /**
     * 获取实际上使用的积分抵扣金额
     * @return float
     */
    public function getIntegralMoney(){
        return $this->integralMoney;
    }

    /**
     * 获取实际上使用的积分
     * @return float|int
     */
    public function getPayPoints()
    {
        return $this->payPoints;
    }

    /**
     * 获取物流费
     * @return int
     */
    public function getShippingPrice()
    {
        return $this->shippingPrice;
    }

	/**
     * 获取真实物流费
     * @return int
     */
    public function getRealShippingPrice()
    {
        return $this->realShippingPrice;
    }

    /**
     *  获取优惠券费
     * @return int
     */
    public function getCouponPrice()
    {
        return $this->couponPrice;
    }

    public function getCardPrice(){
        return $this->cardPrice;
    }
    /**
     * 商品总价
     * @return int
     */
    public function getGoodsPrice()
    {
        return $this->goodsPrice;
    }

    /**
     * 获取用户
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    public function getPayList()
    {
        return $this->payList;
    }

    public function getCouponId()
    {
        return $this->couponId;
    }

    public function getOrderPromAmount()
    {
        return $this->orderPromAmount;
    }
    public function getOrderPromId()
    {
        return $this->orderPromId;
    }

    public function getShop()
    {
        return $this->shop;
    }

    public function getToTalNum()
    {
        return $this->totalNum;
    }

    public function toArray()
    {
        return [
            'shipping_price' => round($this->shippingPrice, 2),
			'real_shipping_price' => round($this->realShippingPrice, 2),
            'coupon_price' => round($this->couponPrice, 2),
            'card_price'=> round($this->cardPrice,2),
            'user_money' => round($this->userMoney, 2),
            'integral_money' => $this->integralMoney,
            'pay_points' => $this->payPoints,
            'order_amount' => round($this->orderAmount, 2),
            'total_amount' => round($this->totalAmount, 2),
            'goods_price' => round($this->goodsPrice, 2),
            'total_num' => $this->totalNum,
            'order_prom_amount' => round($this->orderPromAmount, 2),
            'integral_msg' => $this->message ? $this->message : '',
        ];
    }
}
