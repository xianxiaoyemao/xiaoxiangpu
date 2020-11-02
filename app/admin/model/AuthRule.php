<?php
/**
 * Created by PhpStorm.
 * User: LENOVO
 * Date: 2020/8/3
 * Time: 20:36
 */
namespace app\admin\model;
use think\facade\Cache;
use app\common\model\BaseModel;
class AuthRule extends BaseModel{
// 开启自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';
    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';

//    protected $connection = 'admin'; //指定配置项

    protected static function onAfterWrite($model)
    {
        Cache::delete('__menu__');
    }

    public function getTitleAttr($value, $data)
    {
        return __($value);
    }
}