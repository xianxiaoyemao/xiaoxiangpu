<?php


namespace app\api\validate;
use think\Validate;

class OrdersValidate  extends Validate{
    protected $rule = [
        'site_id' => 'require|number',
        'goods' => 'require',
    ];
    protected $message = [
        'pid.require' => '商家id不能为空',
        'pid.number' => '商家id只能是数字',

    ];
//    protected $scene = [
//        'add_orders' => 'site_id,goods',
//    ];
}
