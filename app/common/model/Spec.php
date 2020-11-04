<?php


namespace app\common\model;


class Spec extends BaseModel{
    // 设置字段信息
    protected $schema = [
        'id'         => 'int',
        'cate_id'     => 'int',
        'spec_name'  => 'string',
        'status'     => 'int',
        'createtime' => 'int',
        'updatetime' => 'int',
    ];

    //一对多关联规格信息表
    public function specinfo(){
        //hasOne表示一对一关联，参数一表示附表，参数二表示外键，参数三表示主键
        return $this->hasMany(SpecInfo::class,'spec_id','id');
    }
    //获取属性
//    public function getStatusAttr($value)
//    {
//        $status = [9 => 'deleted', 1 => 'normal'];
//        return $status[$value];
//    }
}
