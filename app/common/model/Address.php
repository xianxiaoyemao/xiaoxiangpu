<?php


namespace app\common\model;


class Address extends BaseModel
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
