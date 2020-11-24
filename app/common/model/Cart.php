<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2020/11/17
 * Time: 21:39
 */

namespace app\common\model;


class Cart extends BaseModel
{
    public function product(){
        return $this -> belongsTo(Product::class,'product_id','id');
    }

    public function skus(){
        return $this -> belongsTo(ProductSku::class,'sku_id','id');
    }

    /**
     * 商品优惠总额
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getGoodsFeeAttr($value, $data)
    {
        $goods_fee = round($data['goods_num'] * $data['member_goods_price'], 2);
        return $goods_fee;
    }
    /**
     * 商品总额
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getTotalFeeAttr($value, $data)
    {
        $total_fee = round($data['goods_num'] * $data['goods_price'], 2);
        return $total_fee;
    }
    /**
     * 商品总额优惠
     * @param $value
     * @param $data
     * @return mixed
     */
    public function getCutFeeAttr($value, $data)
    {
        $cut_fee = $data['goods_num'] * ($data['goods_price'] - $data['member_goods_price']);
        return $cut_fee;
    }

}
