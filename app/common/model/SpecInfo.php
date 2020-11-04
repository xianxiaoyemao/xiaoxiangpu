<?php


namespace app\common\model;


class SpecInfo extends BaseModel
{
    // 设置字段信息
    protected $schema = [
        'id'         => 'int',
        'spec_id'     => 'int',
        'spec_value'  => 'string',
        'status'     => 'int',
        'createtime' => 'int',
        'updatetime' => 'int',
    ];

//    public function getGoodName(){
//        return $this->belongsTo(Spec::class,'spec_id','id', [], 'LEFT')->setEagerlyType(0);
//    }

    public function specparent(){
        return $this->hasMany(Spec::class, 'spec_id', 'id', [], 'LEFT')->setEagerlyType(0);
    }

//    public function specparent(){
//        return $this->belongsTo(Spec::class);
//    }
//    public function specparent(){
//        return $this->belongsTo(Spec::class,'stu_id','id');
//    }
}
