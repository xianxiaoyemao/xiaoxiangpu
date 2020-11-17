<?php


namespace app\common\model;


class ProductComment extends BaseModel
{
    public function product(){
        return $this->belongsTo(Product::class,'product_id');
    }

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }


}
