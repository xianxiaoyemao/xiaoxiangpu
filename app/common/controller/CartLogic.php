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
 * Author: IT宇宙人
 * Date: 2015-09-09
 */

namespace app\common\controller;

//use app\common\model\Combination;
//use app\common\model\CombinationGoods;
//use app\common\model\SpecGoodsPrice;
use app\common\model\Cart;
use app\common\model\Product;
use app\common\model\ProductSku;
use app\common\model\Shops;
use app\common\model\User;
use app\common\util\TpshopException;
/**
 * 购物车 逻辑定义
 * Class CatsLogic
 * @package Home\Logic
 */
class CartLogic extends Common {

    protected $session_id;//session_id
    protected $user_id = 0;//user_id
    protected $cartc_id = 0;//user_id
    protected $userGoodsTypeCount = 0;//用户购物车的全部商品种类
    protected $userCouponNumArr; //用户符合购物车店铺可用优惠券数量
    protected $combination;


    /**
     * @param int $selected |是否被用户勾选中的 0 为全部 1为选中  一般没有查询不选中的商品情况
     * 获取用户的购物车列表
     * @return false|\PDOStatement|string|\think\Collection
     */
    public function getCartList($cartid = ''){
        $cartWhere = "user_id = $this->user_id";
        if ($cartid) {
            $cartWhere .= " and id in($cartid)";
        }
//      $haswhere['category_id'] = $this-> cartc_id;
//      $cartlist = (new Cart) -> hasWhere('product', $haswhere) -> with(['product','skus'])->where($cartWhere) -> select() -> toArray();
        $cartlist = (new Cart)  -> with(['product','skus']) ->where($cartWhere)  -> select() -> toArray();
        $cartCheckAfterList = $this->checkCartList($cartlist);// 获取购物车商品
        $arr =[];
        $result=[];
        foreach($cartCheckAfterList as $v){
            $result[$v['product']['shop_id']]['shopid'] = $v['product']['shop_id'];
            $result[$v['product']['shop_id']]['shoptitle'] =  (new Shops())::where('id', $v['product']['shop_id']) -> value('title');
            $result[$v['product']['shop_id']]['cartlist'][]=[
                'cartid'=>$v['id'],
                'pid'=>$v['product']['id'],
                'name'=>$v['product']['name'],
                'images'=>$v['product']['images'],
                'sales'=>$v['product']['sales'],
                'bar_code'=>$v['product']['bar_code'],
                'price'=>$v['price'],
                'quantity'=>$v['quantity'],
                'speckey'=> json_decode($v['product']['product_spec_info'],1)['name'],
                'specvalue'=>$v['specvalue'],
                'skuid'=>$v['skus']['id'],
                'skutitle' => $v['skus']['title'],
                'is_rush'=>$v['is_rush'],
                'stock' => $v['skus']['stock'],
            ];
        }
        $cartlist = array_merge($arr,$result);
        foreach ($cartlist as $key => $val){
            foreach ($val['cartlist'] as $k => $v){
                if($v['stock'] <= 5){
                    $val['cartlist'][$k]['isstock'] = '已售完';//已售完
                }else{
                    $val['cartlist'][$k]['isstock'] = '在售中';
                }
                if($v['is_rush'] == 1){
                    if(strtotime(C('skill_end')) - time() < 0){
                        $val['cartlist'][$k]['isstock'] = "失效商品";
                    }
                    $val['cartlist'][$k]['secskill'] = ['skill_start'=>strtotime(C('skill_start')) - time(),'skill_end'=>strtotime(C('skill_end')) - time()];
                }
//                dump($val);die;
            }
            $cartlist[$key] = $val;
        }
        return $cartlist;
//        dump($cartlist);die;
//        $arr = [];
//        foreach ($cartCheckAfterList as $key => $val){
//            //获取店铺信息
//            $arr[$key]['id'] = $val['id'];
//            $arr[$key]['user_id'] = $val['user_id'];
//            $arr[$key]['shop_id'] = $val['product']['shop_id'];
//            $arr[$key]['bar_code'] = $val['product']['bar_code'];
//            $arr[$key]['shoptitle'] = (new Shops())::where('id', $val['product']['shop_id']) -> value('title');
//            $arr[$key]['quantity'] = $val['quantity'];
//            $arr[$key]['price'] = $val['price'];
//            $arr[$key]['product_id'] = $val['product_id'];
//            $arr[$key]['sku_id'] = $val['sku_id'];
//            $arr[$key]['speckey'] = json_decode($val['product']['product_spec_info'],1)['name'];
//            $arr[$key]['specvalue'] = $val['specvalue'];
//            $arr[$key]['name'] = $val['product']['name'];
//            $arr[$key]['category_id'] = $val['product']['category_id'];
//            $arr[$key]['images'] = $val['product']['images'];
////            $arr[$key]['price'] = $val['product']['price'];
//            $arr[$key]['discount_price'] = $val['price'] * ($val['product']['discount_price']/100);
//            $arr[$key]['sales'] = $val['product']['sales'];
//            $arr[$key]['is_rush'] = $val['is_rush'];
//            $arr[$key]['stock'] = $val['skus']['stock'];
//            if($val['is_rush'] == 1){
//                $arr[$key]['secskill'] = ['skill_start'=>strtotime(C('skill_start')) - time(),'skill_end'=>strtotime(C('skill_end')) - time()];
//            }
//
//            $arr[$key]['title'] = $val['skus']['title'];
//            if($val['skus']['stock'] == 0){
//                $arr[$key]['isstock'] = '已售完';//已售完
//            }else{
//                $arr[$key]['isstock'] = '在售中';
//            }
//        }
    }
    /**
     * 过滤掉无效的购物车商品
     * @param $cartList
     */
    public function checkCartList($cartList){
        foreach ($cartList as $cartKey => $cart) {
            //商品不存在或者已经下架
            if (empty($cart['product']) || $cart['product']['status'] != 1 || $cart['inventory'] != 0) {
                (new Cart())->delete();
                unset($cartList[$cartKey]);
                continue;
            }
        }
        return $cartList;
    }




    /**
     * 加入购物车入口
     */
    public function addGoodsToCart(){
        //有商品规格，和没有商品规格
        $prom_type = $this->goods['is_rush'];
        switch($prom_type) {
            case 1:
                $this->addFlashSaleCart();
                break;
            case 2:
                $this->addGroupBuyCart();
                break;
            case 3:
                $this->addPromGoodsCart();
                break;
            default:
                $this->addNormalCart();
        }
    }



    /**
     * 购物车添加普通商品
     */
    private function addNormalCart(){

    }

    /**
     * 购物车添加秒杀商品
     */
    private function addFlashSaleCart(){


    }

    /**
     *  购物车添加团购商品
     */
    private function addGroupBuyCart(){


    }

    /**
     *  购物车添加优惠促销商品
     */
    private function addPromGoodsCart(){


    }



    /**
     * 立即购买
     * @return mixed
     * @throws TpshopException
     */
    public function buyNow(){
        if (empty($this->goods)) {
            throw new TpshopException('立即购买', 0, '购买商品不存在');
        }
        if (empty($this->goodsBuyNum)) {
            throw new TpshopException('立即购买', 0, '购买商品数量不能为0');
        }
        $buyGoods[] = [
            'pid' => $this->goods['id'],
            'skuid' => $this->specGoodsPrice['id'],
            'speckey' => json_decode($this->goods['product_spec_info'],1)['name'],
            'specvalue' => $this->specvalue,
            'price' => $this->specGoodsPrice['price'],
            'skutitle' => $this->specGoodsPrice['title'],
            'stock' => $this->specGoodsPrice['stock'],
            'quantity' => $this->goodsBuyNum, // 购买数量
            'createtime' => time(), // 加入购物车时间
            'is_rush' => $this->goods['is_rush'],   // 0 普通订单,1 限时抢购, 2 团购 , 3 促销优惠
            'name' => $this->goods['name'],
            'bar_code' => $this->goods['bar_code'],
            'images' => $this->goods['images'],
        ];
        $shopdata = [
            'shop_id' => $this->goods['shop_id'],//店铺ID
            'shoptitle' => (new Shops())::where('id',$this->goods['shop_id']) -> value('title'),//店铺ID
            'cartlist' => $buyGoods
        ];
//        if($this->goods['is_rush'] == 1){
//
//        }
        $store_count = $this->specGoodsPrice['stock'];
        if ($this->goodsBuyNum > $store_count) {
            throw new TpshopException('立即购买', 0, '商品库存不足，剩余' . $store_count);
        }

//        $cart = new Cart();
//        $buyGoods['member_goods_price']?$buyGoods['member_goods_price']=round($buyGoods['member_goods_price'],2):'';
//        $buyGoods['cut_fee'] = $cart->getCutFeeAttr(0, $buyGoods);
//        $buyGoods['goods_fee'] = $cart->getGoodsFeeAttr(0, $buyGoods);
//        $buyGoods['total_fee'] = $cart->getTotalFeeAttr(0, $buyGoods);
        return $shopdata;
    }





    /**
     * 获取用户购物车商品总数
     * @return float|int
     */
    public function getUserCartGoodsNum($uid)
    {
        if ($this->user_id) {
            $goods_num = (new Cart())->where(['user_id' => $uid])->sum('quantity');
        }
        $goods_num = empty($goods_num) ? 0 : $goods_num;
        setcookie('cn', $goods_num, null, '/');
        return $goods_num;
    }





    /**
     * 更改购物车的商品数量
     * @param $cart_id |购物车id
     * @param $goods_num |商品数量
     * @return array
     */
    public function changeNum($cart_id, $goods_num)
    {
        $Cart = new Cart();
        $cart = (new Cart)::find($cart_id);
        $cart_goods_where = ['user_id' => $cart['user_id'], 'product_id' => $cart['product_id'], 'item_id' => $cart['item_id']];
        if (!$this->user_id) {
            $cart_goods_where['session_id'] = $this->session_id;
        }
        //判断属性库存 和购物车有几个
        $cart_goods_where['id'] = array('<>',$cart_id);
        $cart_goods_where['combination_group_id'] = array('<>',$cart_id);
        $cart_goods_num_sum = Db::name('cart')->where($cart_goods_where)->sum('goods_num');
        // $store_count = Db::name('spec_goods_price')->where(['item_id'=>$cart['item_id'],'goods_id'=>$cart['goods_id']])->value('store_count');
        //if($store_count){
        //    $cart->limit_num = $store_count;
        // }

        if ($goods_num + $cart_goods_num_sum > $cart->limit_num) {
            return ['status' => 0, 'msg' => $cart->goods_name.$cart->spec_key_name.'商品数量不能大于' . $cart->limit_num, 'result' => ['limit_num' => $cart->limit_num]];
        }
        if ($goods_num > 200) {
            $goods_num = 200;
        }
        $cart->goods_num = $goods_num;
        if ($cart['prom_type'] == 0) {
            $cartGoods = Goods::find($cart['goods_id']);
            if (!empty($cartGoods['price_ladder'])) {
                //如果有阶梯价格,就是用阶梯价格
                $goodsLogic = new GoodsLogic();
                $price_ladder = $cartGoods['price_ladder'];
                $cart->goods_price = $cart->member_goods_price = $goodsLogic->getGoodsPriceByLadder($goods_num, $cartGoods['shop_price'], $price_ladder);
            }
            $cart->save();
        }
        if ($cart['prom_type'] == 7) {
//            $carts = $Cart->where(['combination_group_id' => $cart['combination_group_id'], ['id' , '<>', $cart['id']]])->select();
            //xwy-2018-6-4,加入购物车改了主商品的combination_group_id为0 ，这里只能能拿id
            $carts = $Cart->where(['combination_group_id' => $cart['id'], ['id' , '<>', $cart['id']]])->select();
            // 启动事务
            Db::startTrans();
            foreach ($carts as $cart_item) {
                $cart_goods_where = ['user_id' => $cart_item['user_id'], 'goods_id' => $cart_item['goods_id'], 'item_id' => $cart_item['item_id']];
                if (!$this->user_id) {
                    $cart_goods_where['session_id'] = $this->session_id;
                }
                //判断属性库存 和购物车有几个
                $cart_goods_where['id'] = array('<>',$cart_id);
                $cart_goods_where['combination_group_id'] = array('<>',$cart_id);
                $cart_goods_num_sum = Db::name('cart')->where($cart_goods_where)->sum('goods_num');
                $store_count = Db::name('spec_goods_price')->where(['item_id'=>$cart_item['item_id'],'goods_id'=>$cart_item['goods_id']])->value('store_count');
                if($store_count){
                    $cart_item->limit_num = $store_count;
                }
                if($goods_num + $cart_goods_num_sum > $cart_item->limit_num){
                    // 回滚事务
                    Db::rollback();
                    return ['status' => 0, 'msg' => $cart_item->goods_name.$cart_item->spec_key_name.'商品数量不能大于' . $cart_item->limit_num, 'result' => ['limit_num' => $cart_item->limit_num]];
                }
                $cart_item->goods_num = $goods_num;
                $cart_item->save();
            }
            // 提交事务
            Db::commit();
        }
        $cart->save();
        return ['status' => 1, 'msg' => '修改商品数量成功', 'result' => ''];
    }







    /**
     * 获取购物车的价格详情
     * @param $cartList |购物车列表
     * @return array
     */
    public function getCartPriceInfo($cartList = null){
        $total_price = $goods_fee = $goods_num = 0;//初始化数据。商品总额/节约金额/商品总共数量
        if ($cartList) {

            foreach ($cartList as $cartKey => $cartItem) {
                foreach ($cartItem['cartlist'] as $key => $val){
                    $total_price += $val['price'] * $val['quantity'];
                    $goods_fee += 0;
                    $goods_num += $val['quantity'];
                }
            }
        }
        $total_price = round($total_price,2);
        $goods_fee = round($goods_fee,2);
        return compact('total_price', 'goods_fee', 'goods_num');
    }




    /**
     * 检查购物车数据是否满足库存购买
     * @param $cartList
     * @throws TpshopException
     */
    public function checkStockCartList($cartList){
        foreach ($cartList as $cartKey => $cartVal) {
            if ($cartVal['quantity'] > $cartVal['stock']) {
                throw new TpshopException('', 0, $cartVal['quantity']. '购买数量不能大于' .$cartVal['stock']);
            }
        }
    }

    /**
     * 清除用户购物车选中
     * @throws \think\Exception
     */
    public function clear($cartid = ''){
        if($cartid){
            $where = "user_id = $this->user_id and id in($cartid)";
        }
        (new Cart())->where($where)->delete();
    }



}
