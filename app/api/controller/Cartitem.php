<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2020/11/17
 * Time: 21:38
 */

namespace app\api\controller;
use app\BaseController;
use app\common\controller\CartLogic;
use app\common\controller\Pay;
use app\common\model\Cart;
use app\common\model\ProductSku;
use app\common\model\Product;
use app\Request;
use app\common\util\TpshopException;
class Cartitem extends BaseController{
    //购物车列表
    public function cartlist(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $uid = $request -> post('uid');
        $category_id = $request -> post('cid') ?? 0;
        $cartlist1 = new CartLogic();
        $cartlist1 -> setUserId($uid);
        $cartlist1 -> setCartcartory($category_id);
        $cartlist = $cartlist1->getCartList();//用户购物车
        $cartPriceInfo = $cartlist1->getCartPriceInfo($cartlist);  //初始化数据。商品总额/节约金额/商品总共数量
        $data = [
            'total' => $cartPriceInfo['goods_num'],
            'data' => $cartlist
        ];
        return apiBack('success', '获取成功', '10000',$data);
    }

    //添加商品到购物车
    public function cartsave(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $uid = $request -> post('uid');
        $type = $request -> post('type');
        $pid = $request -> post('product_id');
        $skuid = $request -> post('skuid');
        $price = $request -> post('price');
        $specvalue = $request -> post('specvalue');
        $quantity = $request -> post('quantity') ?? 1;
        switch ($type){
            case 'list':
                //查询商品
                $pskulist = (new ProductSku)::where('product_id',$pid) -> field('id as skuid,price,stock') -> select() -> toArray();//查询产品属性
                $product_spec_info = (new Product())::where('id',$pid) -> find() -> value('product_spec_info');
                $specvalue = json_decode($product_spec_info,1)['list'];
                $data = [
                    'user_id' => (int)$uid,
                    'product_id' => (int)$pid,
                    'sku_id' => (int)$pskulist[0]['skuid'],
                    'quantity' => (int)$quantity,
                    'specvalue'=> $specvalue[0],
                    'price' => floatval($pskulist[0]['price']),
                    'createtime' => time()
                ];
                $stock = $pskulist[0]['stock'];
                break;
            case 'details':
                //查询
                $skunum = (new ProductSku)::where('id',$skuid) -> field('stock') -> find() -> toArray();
                $data = [
                    'user_id' => (int)$uid,
                    'product_id' => (int)$pid,
                    'sku_id' => (int)$skuid,
                    'quantity' => (int)$quantity,
                    'specvalue'=> $specvalue,
                    'price' => floatval($price),
                    'createtime' => time()
                ];
                $stock = $skunum['stock'];
                break;
        }
        $cartinfo = (new Cart)::where(['user_id'=>$uid,'product_id'=>$pid,'sku_id'=>$data['sku_id'],'specvalue'=>$data['specvalue']]) ->find();
        if(empty($cartinfo)){
            $res =  (new Cart) -> save($data);
        }else{
            $quantity = $cartinfo -> quantity + (int)$quantity;
            $data = [
                'quantity' => $quantity,
            ];
            $res =  (new Cart)::where(['id'=>$cartinfo->id])
                -> update($data);
        }
        if($res){
            $res=app('redis')->llen('goods_store'.$pid.$data['sku_id']);
            $count=$stock - $res;
            for($i=0;$i<$count;$i++){
                app('redis') ->lpush('goods_store'.$pid.$data['sku_id'],1);
            }
            return apiBack('success', '添加成功', '10000');
        }else{
            return apiBack('fail', '添加失败', '10004');
        }
    }

    //删除购物车商品
    public function cartdel(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $cartid = $request ->post('ids');
        if(empty($cartid)) return apiBack('fail', '请选择所删除的商品', '10004');
        $where = 'id in('.$cartid.')';
        $res = (new Cart)-> where($where)->delete();
        if($res){
            return apiBack('success', '删除成功', '10000');
        }else{
            return apiBack('fail', '删除失败', '10004');
        }
    }

    /**
     * 将商品加入购物车
     */
    public  function cartsave1(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $uid = $request -> post('uid');
        $type = $request -> post('type');
        $pid = $request -> post('product_id'); // 商品id
        $skuid = $request -> post('skuid');// 商品规格id
        $price = $request -> post('price');
        $specvalue = $request -> post('specvalue');
        $quantity = $request -> post('quantity') ?? 1;// 商品数量
        if(empty($pid))  return apiBack('fail', '请选择要购买的商品', '10004');
        if(empty($quantity))  return apiBack('fail', '购买商品数量不能为0', '10004');
        $cartLogic = new CartLogic();
        $cartLogic->setUserId($uid);
        $cartLogic->setGoodsModel($pid);
        $cartLogic->setProductSku($skuid);

        switch ($type){
            case 'list':
                //查询商品
                $pskulist = (new ProductSku)::where('product_id',$pid) -> field('id as skuid,price,stock') -> select() -> toArray();
                //查询产品属性
                $product_spec_info = (new Product())::where('id',$pid) -> find() -> value('product_spec_info');
                $specvalue = json_decode($product_spec_info,1)['list'];
                $data = [
                    'user_id' => (int)$uid,
                    'product_id' => (int)$pid,
                    'sku_id' => (int)$pskulist[0]['skuid'],
                    'quantity' => (int)$quantity,
                    'specvalue'=> $specvalue[0],
                    'price' => floatval($pskulist[0]['price']),
                    'createtime' => time()
                ];
                $stock = $pskulist[0]['stock'];
                break;
            case 'details':
                //查询
                $skunum = (new ProductSku)::where('id',$skuid) -> field('stock') -> find() -> toArray();
                $data = [
                    'user_id' => (int)$uid,
                    'product_id' => (int)$pid,
                    'sku_id' => (int)$skuid,
                    'quantity' => (int)$quantity,
                    'specvalue'=> $specvalue,
                    'price' => floatval($price),
                    'createtime' => time()
                ];
                $stock = $skunum['stock'];
                break;
        }


        $cartLogic->setGoodsBuyNum($data['quantity']);
        try {
            $cartLogic->addGoodsToCart();
            return apiBack('success', '加入购物车成功', '10000');
        } catch (TpshopException $t) {
            $error = $t->getErrorArr();
            if(!isset($error['status']))
                $error['status'] = -1;
            return apiBack('fail', '加入购物车失败', '10000');
        }
    }




    /**
     * 购物车第二步确定页面
     */
    public function cart2(Request $request){ //cartconfirm
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $uid = $request -> post('uid/d');
        $cartid = $request -> post('cartid');
        $action = $request -> post('action'); // 行为
        $pid = $request -> post('pid/d');// 商品id
        $skuid = $request -> post('skuid/d');//商品规格id
        $specvalue = $request -> post('specvalue');
        $quantity = $request -> post('quantity/d') ?? 1;
        $cartLogic = new CartLogic();
        $cartLogic->setUserId($uid);
//        $couponLogic = new CouponLogic();
        //立即购买
        if($action == 'buy_now'){
            if(!$pid)return apiBack('fail', '请选择商品', '10004');
            if(!$specvalue)return apiBack('fail', '请选择规格属性', '10004');
            if(!$skuid)return apiBack('fail', '请选择规格Id', '10004');
            $cartLogic->setGoodsModel($pid)
                -> setSpecvalue($specvalue)
                ->setProductSku($skuid)
                ->setGoodsBuyNum($quantity);
//            $buyGoods = [];
            try{
                $buyGoods = $cartLogic->buyNow();
            }catch (TpshopException $t){
                $error = $t->getErrorArr();
                return apiBack('fail', $error['msg'], '10004');
            }
            $cartList['cart'][0] = $buyGoods;
            $cartGoodsTotalNum = $quantity;
//            dump($cartGoodsTotalNum);die;
//            $setRedirectUrl = new UsersLogic();
//            $setRedirectUrl->orderPageRedirectUrl($_SERVER['REQUEST_URI'],'',$goods_id,$goods_num,$item_id ,$action);
        }else{
            if(empty($cartid)){
                return apiBack('fail', '你的购物车没有选中商品', '10004');
            }
            $cartList['cart'] = $cartLogic->getCartList($cartid); // 获取用户选中的购物车商品
//            $cartList['cartList'] = $cartLogic->getCombination($cartList['cartList']);  //找出搭配购副商品
        }
//        $cartGoodsList = get_arr_column($cartList['cart'],'product');
        $cartPriceInfo = $cartLogic->getCartPriceInfo($cartList['cart']);  //初始化数据。商品总额/节约金额/商品总共数量
//        $userCouponList = $couponLogic->getUserAbleCouponList($uid, $cartGoodsId, $cartGoodsCatId);//用户可用的优惠券列表
        $cartList = array_merge($cartList,$cartPriceInfo);
        $cartList['couponsnum'] = 0; //优惠卷
        $cartList['integral'] = 0; //积分
        return apiBack('success', '获取成功', '10000',$cartList);
    }

    /**
     * 获取订单商品价格 或者提交 订单
     */
    public function cartsubmit(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $uid = $request -> post('uid/d');
        $addressid = $request -> post("addressid/d"); //  收货地址id
        if(empty($addressid)) return apiBack('fail', '请选择地址', '10000');
        $dining = $request -> post("dining/d",0);//用餐方式 1自提 0
        $cartid = $request -> post("cartid");
        $goods_id = $request -> post("pid/d"); // 商品id
        $skuid = $request -> post("skuid/d"); // 商品规格id
        $goods_num = $request -> post("quantity/d");// 商品数量

        $totle_price = floatval($request -> post("totle_price"));//商品总价

        $shop_id = $request -> post('shop_id/d');//自提点id
        $take_time = $request -> post('take_time/d');//自提时间
        $consignee = $request -> post('consignee/s');//自提点收货人
        $mobile = $request -> post('mobile/s');//自提点联系方式

        $coupon_id = $request -> post("coupon_id/d", 0); //  优惠券id
        $pay_points = $request -> post("pay_points/d", 0); //  使用积分

        $remark = $request -> post("remark/s", ''); // 备注
//        $totle_price = $price * $goods_num;//商品总价

//        $invoice_title = $request -> post('invoice_title');  // 发票
//        $taxpayer = $request -> post('taxpayer');       // 纳税人识别号
//        $invoice_desc = $request -> post('invoice_desc');       // 发票内容
//        $user_money = $request -> post("user_money/f", 0); //  使用余额
//        $pay_pwd = $request -> post("pay_pwd/s", ''); // 支付密码
//        $data = $request -> post('request.');
//        $bespeakForm = $request -> post('bespeak_form/a');
//        $from_terminal = $request -> post("from_terminal/s"); // 下单的终端设备 /H5或微信浏览器端
//        $address = Db::name('user_address')->where("address_id", $address_id)->find();
        $cartLogic = new CartLogic();
        $placeOrder = new \app\common\model\Order();
        try {
            $cartLogic->setUserId($uid);
            if($cartid == ''){
                $cartLogic->setGoodsModel($goods_id);
                $cartLogic->setProductSku($skuid);
                $cartLogic->setGoodsBuyNum($goods_num);
                $buyGoods = $cartLogic->buyNow();
                $cartList[0] = $buyGoods;
            } else {
                $cartList = $cartLogic->getCartList($cartid);
                $cartLogic->checkStockCartList($cartList);
//                $pay->payCart($userCartList);//判断购物车
            }
//            $pay->setUserId($uid) -> setShopById($shop_id) -> setAddressid($addressid) -> setremark($remark) -> setGoodsModel($goods_id);

            $order_sn = $placeOrder->addNormalOrder($uid,$cartList,$addressid,$totle_price,0,$remark,$dining);
            if($order_sn){
                $cartLogic->clear($cartid);
                return apiBack('success', '计算成功', '10000',['order_sn'=>$order_sn]);
            }

//            $placeOrder -> setUserId($uid) -> setShopById($shop_id);
//            $placeOrder -> setShopById($shop_id);
//            $placeOrder -> addNormalOrder();
//            dump($placeOrder);die;
//            dump(1);die;
            // 提交订单
        } catch (TpshopException $t) {
            $error = $t->getErrorArr();
            return apiBack('fail', $error, '10004');
        }
    }
    /*
     * 订单支付页面
     */
    public function cart4(){
        if(empty($this->user_id)){
            return redirect('/User/login');
        }
        $order_id = input('order_id/d');
        $is_virtual= input('is_virtual/d',0);
        $order_sn= input('order_sn/s','');
        $order_where = ['user_id'=>$this->user_id];
        if($order_sn)
        {
            $order_where['order_sn'] = $order_sn;
        }else{
            $order_where['order_id'] = $order_id;
        }
        $Order = new Order();
        $order = $Order->where($order_where)->find();
        empty($order) && $this->error('订单不存在！');
        if($order['order_status'] == 3){
            if($is_virtual==2){
                $this->error('该订单已取消',url("Mobile/Order/bespeak_detail",array('id'=>$order['order_id'])));
            }else{
                $this->error('该订单已取消',url("Mobile/Order/order_detail",array('id'=>$order['order_id'])));
            }
        }
        if(empty($order) || empty($this->user_id)){
            $order_order_list = url("User/login");
            header("Location: $order_order_list");
            exit;
        }
        // 如果已经支付过的订单直接到订单详情页面. 不再进入支付页面
        if($order['pay_status'] == 1){
            if($is_virtual==2){
                $order_detail_url = url("Mobile/Cart/bespeak_success",array('goods_name'=>input('goods_name'),'take_time'=>date('Y-m-d H:i:s',input('take_time'))));
            }else{
                if($order['prom_type']==10){
                    $order_detail_url = url('mobile/ShoppingCard/pay_successful',['id'=>$order['order_id']]);
                }else{
                    $order_detail_url = url('mobile/order/paySuccess',['id'=>$order['order_id']]);
                }
            }
            header("Location: $order_detail_url");
            exit;
        }
        //如果是预约订单，需要校验预约人数
        if($is_virtual==2){
            $pay = new Pay();
            $PlaceOrder = new PlaceOrder($pay);
            try {
                $PlaceOrder->checkBespeakTemplate($order['order_goods'],$order['shop'],strtotime($order['shopOrder'][0]['take_time']),$order['order_bespeak'][0]['template_unit_id']);
            } catch (TpshopException $t) {
                $error = $t->getErrorArr();
                $this->error($error['msg'],url("Mobile/Order/bespeak_detail",array('id'=>$order['order_id'])));
            }
        }
        $orderGoodsPromType = Db::name('order_goods')->where(['order_id'=>$order['order_id']])->column('prom_type');
        //如果是预售订单，支付尾款
        if ($order['pay_status'] == 2 && $order['prom_type'] == 4) {
            if ($order['pre_sell']['pay_start_time'] > time()) {
                $this->error('还未到支付尾款时间' . date('Y-m-d H:i:s', $order['pre_sell']['pay_start_time']));
            }
            if ($order['pre_sell']['pay_end_time'] < time()) {
                $this->error('对不起，该预售商品已过尾款支付时间' . date('Y-m-d H:i:s',$order['pre_sell']['pay_end_time'] ));
            }
        }

        if ($order['prom_type'] == 8) {
            //如果是砍价订单，支付尾款
            $promotion_bargain = Db::name('promotion_bargain')->where(['id'=>$order['prom_id']])->find();
            if ($promotion_bargain['end_time'] < time() ) {
                $this->error('该活动已结束，不能支付');
            }
            $promotion_bargain_goods_item = Db::name('promotion_bargain_goods_item')->where(['bargain_id'=>$order['prom_id'],'item_id'=>$order['order_goods'][0]['item_id']])->find();
            if ($promotion_bargain_goods_item['goods_num'] <= $promotion_bargain_goods_item['buy_num'] ) {
                // 统计订单已支付的数量是否超过了 goods_num
                $goods_num = Db::view('Order','order_id,pay_status,prom_type,prom_id')
                    ->view('OrderGoods','goods_num','OrderGoods.order_id=Order.order_id','LEFT')
                    ->where('prom_id',$order['prom_id'])
                    ->where('pay_status',1)
                    ->where('order_status','in','0,1,2,4')
                    ->sum('goods_num');
                if($promotion_bargain_goods_item['goods_num'] <= $goods_num ){
                    $this->error('对不起，该商品数量已被抢空' );
                }
            }
        }
        $payment_where['type'] = 'payment';
        $no_cod_order_prom_type = [4,5,10];//预售订单，虚拟订单不支持货到付款
        if(strstr($_SERVER['HTTP_USER_AGENT'],'MicroMessenger')){
            //微信浏览器
            if(in_array($order['prom_type'],$no_cod_order_prom_type) || in_array(1,$orderGoodsPromType) || $order['shop_id'] > 0){
                //预售订单和抢购不支持货到付款
                $payment_where['code'] = 'weixin';
            }else{
                $payment_where['code'] = array('in',array('weixin','cod'));
            }
        }else{
            if(in_array($order['prom_type'],$no_cod_order_prom_type) || in_array(1,$orderGoodsPromType) || $order['shop_id'] > 0){
                //预售订单和抢购不支持货到付款
                $payment_where['code'] = array('<>','cod');
            }
            $payment_where['scene'] = array('in',array('0','1'));
        }
        $payment_where['status'] = 1;
        //预售和抢购暂不支持货到付款
        $orderGoodsPromType = Db::name('order_goods')->where(['order_id'=>$order['order_id']])->column('prom_type');
        if($order['prom_type'] == 4 || in_array(1,$orderGoodsPromType)){
            $payment_where['code'] = array('<>','cod');
        }
        $paymentList = Db::name('Plugin')->where($payment_where)->select();
        $paymentList = convert_arr_key($paymentList, 'code');

        foreach($paymentList as $key => $val)
        {
            $val['config_value'] = unserialize($val['config_value']);
            if($val['config_value']['is_bank'] == 2)
            {
                $bankCodeList[$val['code']] = unserialize($val['bank_code']);
            }
            if($key != 'cod' && (($key == 'weixin' && !is_weixin()) // 不是微信app,就不能微信付，只能weixinH5付,用于手机浏览器
                    || ($key != 'weixin' && is_weixin()) //微信app上浏览，只能微信
                    || ($key != 'alipayMobile' && is_alipay()))){ //在支付宝APP上浏览，只能用支付宝支付
                unset($paymentList[$key]);
            }
        }

        $bank_img = include APP_PATH.'home/bank.php'; // 银行对应图片
        View::assign('paymentList',$paymentList);
        View::assign('bank_img',$bank_img);
        View::assign('order',$order);
        View::assign('bankCodeList',$bankCodeList);
        View::assign('pay_date',date('Y-m-d', strtotime("+1 day")));
        return View::fetch();
    }
    /**
     * ajax 获取用户收货地址 用于购物车确认订单页面
     */
    public function ajaxAddress(){
        $regionList = get_region_list();
        $address_list = Db::name('UserAddress')->where("user_id", $this->user_id)->select()->toArray();
        $c = Db::name('UserAddress')->where("user_id = {$this->user_id} and is_default = 1")->count(); // 看看有没默认收货地址
        if((count($address_list) > 0) && ($c == 0)) // 如果没有设置默认收货地址, 则第一条设置为默认收货地址
            $address_list[0]['is_default'] = 1;

        View::assign('regionList', $regionList);
        View::assign('address_list', $address_list);
        return View::fetch('ajax_address');
    }




    /**
     * 更新购物车，并返回计算结果
     */
    public function AsyncUpdateCart(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $uid = $request -> post('uid');
        $cart = input('cart/a', []);
        $cartLogic = new CartLogic();
    }

    /**
     *  购物车加减
     */
    public function changeNum(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $cartid = $request -> post('cartid');
        $quantity = $request -> post('quantity');
        if (empty($cartid))   return apiBack('fail', '请选择要更改的商品', '10004');
        $result = (new CartLogic())->changeNum($cartid,$quantity);
        if($result){
            return apiBack('success', '更新数量成功', '10000');
        }
    }






    //选中状态
    public function cartselect(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $uid = $request -> post('uid/d');
        $cartid = $request -> post('id/d');
        $selected = $request -> post('selected/d');
        if($selected == 1){
            $result1 = (new Cart())::where(['id'=>$cartid,'user_id'=>$uid]) -> update(['selected' => 1]);
        }else{
            $result = (new Cart())::where(['id'=>$cartid,'user_id'=>$uid]) -> update(['selected' => 1]);
        }
        if($result1){
            return apiBack('success', '选中成功', '10000');
        }
    }


}
