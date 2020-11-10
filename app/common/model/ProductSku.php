<?php


namespace app\common\model;


class ProductSku extends BaseModel
{
    public function product ()
    {
        return $this->belongsTo(Product::class);
    }
}
