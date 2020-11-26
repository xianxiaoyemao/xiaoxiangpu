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
use think\facade\Db;
/**
 * 购物车 逻辑定义
 * Class CatsLogic
 * @package Home\Logic
 */
class CartLogic extends CommonController {

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
        $procdut =  $this -> getGoodsprice($cartlist);
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
                'is_rush'=>$v['product']['is_rush'],
                'isstock' => $v['skus']['stock'],
            ];
        }
        $cartlist = array_merge($arr,$result);
        foreach ($cartlist as $key => $val){
            foreach ($val['cartlist'] as $k => $v){
                if($v['is_rush'] == 1){
                    if(strtotime(C('skill_end')) - time() < 0){
                        $val['cartlist'][$k]['isfailure'] = 0;
                    }
                    $val['cartlist'][$k]['isfailure'] = 1;
                    $val['cartlist'][$k]['secskill'] = ['skill_start'=>strtotime(C('skill_start')) - time(),'skill_end'=>strtotime(C('skill_end')) - time()];
                }
            }
            $cartlist[$key] = $val;
        }
        $data = [
            'total_price' => $procdut['total_price'],
            'goods_num' => $procdut['goods_num'],
            'data' => $cartlist
        ];
        return $data;
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

    //获取商品总价和商品总数量
    public function getGoodsprice($cartCheckAfterList){
        $total_price = $goods_num = 0;
        foreach ($cartCheckAfterList as $key => $val){
            $goods_num += (int)$val['quantity'];
            $total_price += $val['price'] * (int)$val['quantity'];
        }
        $total_price = round($total_price,2);
        return compact('total_price', 'goods_num');
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

//        if($this->goods['is_rush'] == 1){
//
//        }
        $store_count = $this->specGoodsPrice['stock'];
        if ($this->goodsBuyNum > $store_count) {
            throw new TpshopException('立即购买', 0, '商品库存不足，剩余' . $store_count);
        }
        $shopdata = [
            'shop_id' => $this->goods['shop_id'],//店铺ID
            'shoptitle' => (new Shops())::where('id',$this->goods['shop_id']) -> value('title'),//店铺ID
            'cartlist' => $buyGoods
        ];
        $procdut =  $this -> getGoodsprice($buyGoods);

        $data = [
            'total_price' => $procdut['total_price'],
            'goods_num' => $procdut['goods_num'],
            'data' => $shopdata
        ];
        return $data;
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
        $where = "user_id = $this->user_id";
        if($cartid){
            $where .=" and id in($cartid)";
        }
        (new Cart())->where($where)->delete();
    }

    /**
     * 函数：editCart
     * 功能：编辑购物车信息[物品购物数量+1-1]
     * 简介：根据提供的购物车名$cartName及操作名$action结合指定购物车物品序号$skey对指定物品的购买数量进行+1-1操作
     * 时间：2011年7月30日 23:09:27
     * 作者：by zhjp
     * Enter description here ...
     * @param String $cartName
     * @param String $action[plus+][minus-]
     * @param Int $skey
     */
    public function editCart($action,$skey,$number=1){
        switch ($action){
            case 'plus':
                $this->_plusOne($skey);
                break;
            case 'minus':
                $this->_minusOne($skey);
                break;
            case 'input';
                $this->_minusOne($skey,$number);
                break;
        }
        $this -> getCartList();
        //更新购物车信息
    }

    /**
     * 函数：_plusOne
     * 功能：将物品的购买数量+1
     * 简介：根据提供的购物车物品序号$skey将指定的商品数量+1
     * 时间：2011年7月30日 23:24:26
     * 作者：by zhjp
     * Enter description here ...

     * @param Int $skey
     */
    private function _plusOne($skey){
        //指定物品购买数量+1
        (new Cart()) -> where('id',$skey) -> Inc('quantity') -> update();
    }
    /**
     * 函数：_minusOne
     * 功能：将物品的购买数量-1
     * 简介：根据提供的购物车物品序号$skey将指定的商品数量-1
     * 时间：2011年7月30日 23:27:19
     * 作者：by zhjp
     * Enter description here ...
     * @param unknown_type $cartName
     * @param unknown_type $skey
     */
    private function _minusOne($skey){
        //查询购物车单个信息
        $quantity = (new Cart())::where('id',$skey) -> value('quantity');
        if($quantity < 2){
//            return 2;
            throw new TpshopException('立即购买', 0, '商品数量不能小于1' );
        }
        (new Cart()) -> where('id',$skey) -> dec('quantity') -> update();
        //更新购物车信息
    }

}
