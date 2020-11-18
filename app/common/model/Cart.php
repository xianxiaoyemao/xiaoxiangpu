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
}
