<?php


namespace app\common\model;


class Order extends BaseModel
{

    public function cacheKeyCreateOrder(){
        self::beginTrans();
    }
}
