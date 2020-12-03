<?php

namespace app\api\controller;
use think\facade\Db;
use think\Exception;
use fast\Redis;
class Seckill{
//    private $goods_id;
//    private $user_queue_key;
//    private $goods_number_key;
//    private $user_id;
//    private $sku_id;
//    private $stock;
    public $price = 10;
    public $user_id = 1;
    public $goods_id = 1;
    public $sku_id   = 11;
    public $number = 1;
    private $redis = null;

    public function __construct(){
        //模拟下单操作
        //下单前判断redis队列库存量
        $this->redis=new Redis();
//        $this->redis->connect('127.0.0.1',6379);
//        if($goods_id){
//            $this -> sku_id = $sku_id;
//            $this -> stock = $stock;
//            $this->goods_id = $goods_id;
//            $this->user_queue_key = "goods_".$goods_id."_user";//当前商品队列的用户情况
//            $this->goods_number_key = "goods".$goods_id.$sku_id;//当前商品的库存队列
//        }
//        $this->user_id = $uid;
    }
    // 主方法
    public function go(){
        // 取出用户
        if( !$this->userPop()) {
            $this->insertLog('no users buy');
            return;
        }
        /// 判断是否重复下订单
        if( in_array($this->user_id, $this->redis->sMembers( 'users_buy'))) {
            $this->insertLog($this->user_id.' repeat place order');
            return;
        }
        // 检查库存
        $count=$this->redis->lpop('goods_store');
        if(!$count){
            $this->insertLog($this->user_id .' error:no store redis');
            return;
        }

        // 开启事务 确保订单不会重复下

        //生成订单
        $order_sn=$this->build_order_no();

        $order_rs = Db::name( 'orders')
            ->insert([
                'order_sn' => $order_sn,
                'user_id' => $this->user_id,
                'goods_id' => $this->goods_id,
                'sku_id' => $this->sku_id,
                'price' => $this->price
            ]);

        //库存减少
        $store_rs = Db::name( 'store')
            ->where( 'sku_id', $this->sku_id)
            ->Dec( 'number', $this->number);
        if($store_rs){
            // 用户购买成功，把user_id存入set集合缓存。拿来判断该用户是否会继续下单
            $this->redis->sAdd( 'users_buy', $this->user_id);
            $this->insertLog($this->user_id .' 库存减少成功');
            return;
        }else{
            $this->insertLog($this->user_id .' 库存减少失败');
            return;
        }

    }

    // 用户取出队列
    // 返回用户id
    public function userPop()
    {
        return $this->user_id = $this->redis->rpoplpush( 'user_line_up', 'user_pop_queue');
    }

    public function user()
    {
        dump( $this->redis->lRange( 'user_line_up', 0, -1));
    }

    public function clear()
    {
        dump( $this->redis->flushDB());
    }

    public function make(){
        echo $this->goodsStockAddQueue();
        echo $this->userLineUp();
    }

    public function test()
    {
        dump( $this->redis->keys( '*'));

        dump( $this->redis->lLen( 'user_line_up'));
        dump( $this->redis->lLen( 'goods_store'));
        dump( $this->redis->sCard( 'users_buy'));
    }

    // 用户排队队列 $i即为user_id
    public function userLineUp()
    {
        // 模拟用户二次下单
        $num = [];
        for ( $a=0;$a<2;$a++)
        {
            for ( $i=1;$i<1001;$i++)
            {
                array_push( $num, $i);
            }
            // 打乱排序
            shuffle( $num);
        }
        for ( $i=0; $i< count( $num); $i++)
        {
            $this->redis->lPush( 'user_line_up', $num[$i]);
        }
        echo '用户排队数：'.$this->redis->lLen( 'user_line_up');
    }


    // 商品库存加入队列
    public function goodsStockAddQueue()
    {
        $store=500;
        $res=$this->redis->llen('goods_store');
        $count=$store-$res;
        for($i=0;$i<$count;$i++){
            $this->redis->lpush('goods_store',1);
        }
        echo '商品剩余库存：'.$this->redis->llen('goods_store');
    }

    //生成唯一订单号
    function build_order_no(){
        return date('ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
    }

    //记录日志
    function insertLog($event,$type=0){
        Db::name( 'log')->insert( [
                'event' => $event,
                'type' => $type
            ]);
    }
}
