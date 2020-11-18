<?php


namespace app\common\model;


class ProductDetails extends BaseModel
{
    public function product(){
        return $this -> belongsTo(Product::class,'product_id','id');
    }

}
