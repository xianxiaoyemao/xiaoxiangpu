<?php


namespace app\admin\model;


use app\common\model\BaseModel;

class Category extends BaseModel
{
    // 设置字段信息
    protected $schema = [
        'id'         => 'int',
        'pid'         => 'int',
        'cate_name'  => 'string',
        'status'     => 'int',
        'createtime' => 'int',
        'updatetime' => 'int',
    ];

}
