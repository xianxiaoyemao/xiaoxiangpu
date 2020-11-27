<?php


namespace app\common\model;


class OrdersDetail extends BaseModel
{
    public function product(){
        return $this -> belongsTo(Product::class,'product_id','id');
    }

    public function orders(){
        return $this -> hasMany(Orders::class,'order_id','id');
    }
}
