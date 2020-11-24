<?php
namespace app\common\controller;
use app\common\model\Cart;
use app\common\model\Order;
use app\common\model\Product;
use app\common\model\ProductSku;
use app\common\model\Shops;

class Common{
    protected $user_id = 0;
    protected $cartc_id = 0;//user_id
    protected $specvalue;
    protected $goodsBuyNum;//购买的商品数量
    protected $goods;//商品模型
    protected $specGoodsPrice;//商品规格模型
    protected $shop;
    protected $addressid;
    protected $remark;
    public function setremark($remark){
        $this -> remark = $remark;
        return  $this;
    }
    public function getremark(){
        return $this -> remark;
    }
    public function setAddressid($addressid){
        $this -> addressid = $addressid;
        return $this;
    }
    public function getAddressid(){
        return $this -> addressid;
    }
    //供应商模型
    public function setShopById($shop_id){
        if($shop_id){
            $this->shop = Shops::find($shop_id);
        }
        return $this;
    }
    //获取供应商
    public function getShop(){
        return $this->shop;
    }
    /**
     * 设置用户ID
     * @param $user_id
     */
    public function setUserId($user_id){
        $this->user_id = $user_id;
        return $this;
    }

    //获取用户id
    public function getUserid(){
        return $this -> user_id;
    }

    //设置购物分类
    public function setCartcartory($cartc_id){
        $this-> cartc_id = $cartc_id;
        return $this;
    }

    /**
     * 设置购买的商品数量
     * @param $goodsBuyNum
     */
    public function setGoodsBuyNum($goodsBuyNum){
        $this->goodsBuyNum = $goodsBuyNum;
        return $this;
    }
    //设置属性规格值
    public function setSpecvalue($specvalue){
        $this->specvalue = $specvalue;
        return $this;
    }

    /**
     *  modify ：cart_count
     *  获取用户购物车欲购买的商品有多少种
     * @return int|string
     */
    public function getUserCartOrderCount(){
        $count = (new Cart()) ->where(['user_id' => $this->user_id])->count();
        return $count;
    }

    /**
     * 包含一个商品模型
     * @param $goods_id
     */
    public function setGoodsModel($goods_id){
        if ($goods_id > 0) {
            //加了判断，上架才能买
            $this->goods = (new Product)::where(['id'=>$goods_id,'status'=>1])->find();
        }
        return $this;
    }
    /**
     * 通过item_id包含一个商品规格模型
     * @param $item_id
     */
    public function setProductSku($item_id){
        if ($item_id > 0) {
            $this->specGoodsPrice = (new ProductSku())::where('id',$item_id)->find();
        }else{
            $this->specGoodsPrice = null;
        }
        return $this;
    }


    /**
     * 获取订单 order_sn
     * @return string
     */
    public function get_order_sn(){
        $order_sn = null;
        // 保证不会有重复订单号存在
        while(true){
            $order_sn = $this ->createOrderNm() ; // 订单编号
//            $order_sn = date('YmdHis').rand(1000,9999); // 订单编号
            $order_sn_count = (new Order)::where("order_sn = '$order_sn'")->count();
            if($order_sn_count == 0)
                break;
        }
        return $order_sn;
    }

    /**
     * 生成15位的订单号
     * @return string 订单号
     */
    public function createOrderNm(){
        $year_code = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');
        $date_code = array('0',
            '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A',
            'C', 'D', 'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M',
            'N', 'O', 'P', 'Q', 'R', 'T', 'U', 'V', 'W', 'X', 'Y');
        //一共15位订单号,同一秒内重复概率1/10000000,26年一次的循环\
        $order_sn = $year_code[(intval(date('Y')) - 2010) % 26] . //年 1位
            strtoupper(dechex(date('m'))) . //月(16进制) 1位
            $date_code[intval(date('d'))] . //日 1位
            substr(time(), -5) . substr(microtime(), 2, 5) . //秒 5位 // 微秒 5位
            sprintf('%02d', rand(0, 99)); //  随机数 2位
        return $order_sn;
    }


}
