<?php
namespace app\common\model;

class User extends BaseModel
{
//    protected $connection = 'mysql';
    public $incrementing = false;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','openid','username', 'password', 'nickname','status','mobile','login_ip','invitecode'
    ];
    public function userinfo()
    {
        return $this->hasOne(UserInfo::class);
    }

    public function cartItems(){
        return $this->hasMany(CartItem::class);
    }

    public function order(){
        return $this->hasMany(Order::class,'user_id');
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }
}
