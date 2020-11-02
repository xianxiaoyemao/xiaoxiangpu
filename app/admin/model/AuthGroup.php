<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2020/8/5
 * Time: 20:05
 */
namespace app\admin\model;

use app\common\model\BaseModel;

class AuthGroup extends BaseModel
{
//    protected $connection = 'admin';
    // 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

    public function getNameAttr($value, $data)
    {
        return __($value);
    }
}
