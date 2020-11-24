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
class Pay extends Common {


//    public function toArray()
//    {
//        return [
//            'shipping_price' => round($this->shippingPrice, 2),
//			'real_shipping_price' => round($this->realShippingPrice, 2),
//            'coupon_price' => round($this->couponPrice, 2),
//            'card_price'=> round($this->cardPrice,2),
//            'user_money' => round($this->userMoney, 2),
//            'integral_money' => $this->integralMoney,
//            'pay_points' => $this->payPoints,
//            'order_amount' => round($this->orderAmount, 2),
//            'total_amount' => round($this->totalAmount, 2),
//            'goods_price' => round($this->goodsPrice, 2),
//            'total_num' => $this->totalNum,
//            'order_prom_amount' => round($this->orderPromAmount, 2),
//            'integral_msg' => $this->message ? $this->message : '',
//        ];
//    }
}
