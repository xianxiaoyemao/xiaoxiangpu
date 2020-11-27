<?php


namespace app\common\model;


class OrdersShop extends BaseModel
{
    public function orders(){
        return $this -> belongsTo(Orders::class,'order_id','id');
    }
}
