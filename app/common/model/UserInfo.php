<?php


namespace app\common\model;
class UserInfo extends BaseModel{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
