<?php


namespace app\admin\model;


use app\common\model\BaseModel;

class Adv extends BaseModel
{
    public function advposition(){
        return $this->belongsTo(Advposition::class, 'pid', 'id');
    }
}
