<?php
namespace app\common\model;
class Shops extends BaseModel{
//    protected $connection = 'mysql_products';
    public function product(){
        return $this->hasMany(Product::class,'shop_id','id');
    }
//
//    public function admin_user()
//    {
//        return $this->belongsTo(AdminUser::Class);
//    }
//
//    public function order()
//    {
//        return $this->hasMany(Order::class);
//    }


}
