<?php
namespace app\api\controller;
use app\common\model\Product;
use app\common\util\TpshopException;

class SecondsKill{
    private $goods_id;
    private $user_queue_key;
    private $goods_number_key;
    private $user_id;
    private $sku_id;
    private $stock;
    //现在初始化里面定义后边要使用的redis参数
    public function __construct($goods_id,$uid,$sku_id,$stock)
    {
        if($goods_id){
            $this -> sku_id = $sku_id;
            $this -> stock = $stock;
            $this->goods_id = $goods_id;
            $this->user_queue_key = "goods_".$goods_id."_user";//当前商品队列的用户情况
            $this->goods_number_key = "goods".$goods_id.$sku_id;//当前商品的库存队列
        }
        $this->user_id = $uid;
    }

    /**
     * 访问产品前先将当前产品库存队列
     * @access public
     * @author bieanju
     */
    public function _before_detail(){
        $skilltime = strtotime(C('skill_end')) - time();
//        $goodsinfo = (new Product)::where('id', $this->goods_id)-> find();
//        if($skilltime < 0) throw new TpshopException('秒杀', 0, '当前秒杀已结束！');
//        $where['id'] = $this->goods_id;
//        $where['start_time'] = array("lt",time());
//        $where['end_time'] =  array("gt",time());
//        $goods = M("goods")->where($where)->field('goods_num,start_time,end_time')->find();
        if($this -> stock > 3){
            $getUserRedis =   app('redis') -> hGetAll("{$this->user_queue_key}");
            $gnRedis = app('redis') ->llen("{$this->goods_number_key}");
            /* 如果没有会员进来队列库存 */
            if(!count($getUserRedis) && !$gnRedis){
                for ($i = 0; $i < $this -> stock; $i ++) {
                    app('redis')->lpush("{$this->goods_number_key}", 1);
                }
            }
            $resetRedis = app('redis')->llen("{$this->goods_number_key}");
            if(!$resetRedis){
                throw new TpshopException('秒杀', 0, '系统繁忙，请稍后抢购！');
            }
        }else{
            throw new TpshopException('秒杀', 0, '当前产品已经秒杀完!');
        }
    }

    /**
     * 抢购商品前处理当前会员是否进入队列
     * @access public
     * @author bieanju
     */
    public function goods_number_queue(){
        $goodsinfo = (new Product)::where('id', $this->goods_id)-> find();
        if(!$goodsinfo < 0) throw new TpshopException('秒杀', 0, '对不起当前商品不存在或已下架！');
        /* redis 队列 */
        $redis = app('redis');
        /* 进入队列  */
        $goods_number_key = $redis->llen("{$this->goods_number_key}");
        if (!$redis->hGet("{$this->user_queue_key}", $this->user_id)) {
            $goods_number_key = $redis->lpop("{$this->goods_number_key}");
        }
        if($goods_number_key){
            // 判断用户是否已在队列
            if (!$redis->hGet("{$this->user_queue_key}", $this->user_id)) {
                // 插入抢购用户信息
                $userinfo = array(
                    "user_id" => $this->user_id,
                    "create_time" => time()
                );
                $redis->hSet("{$this->user_queue_key}", $this->user_id, serialize($userinfo));
                throw new TpshopException('秒杀', 1, '秒杀');
            }else{
//                $modelCart = M("cart");
//                $condition['user_id'] = $this->user_id;
//                $condition['goods_id'] = $this->goods_id;
//                $condition['prom_type'] = 1;
//                $cartlist = $modelCart->where($condition)->count();
//                if($cartlist > 0){
//                    throw new TpshopException('秒杀', 2, '秒杀');
//                }else{
//                    throw new TpshopException('秒杀', 1, '秒杀');
//                }
            }
        }else{
            throw new TpshopException('秒杀', 0, '系统繁忙,请重试！');
        }
    }
    // 用户取出队列
    // 返回用户id
    public function userPop(){
        return $this->user_id = $this->redis->rpoplpush( 'user_line_up', 'user_pop_queue');
    }
    // 主方法
    public function go(){
        // 取出用户
        if( !$this->userPop())
        {
            $this->insertLog('no users buy');
            return;
        }
        /// 判断是否重复下订单
        if( in_array( $this->user_id, $this->redis->sMembers( 'users_buy')))
        {
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

        $order_rs = Db::name( 'order')
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
            ->setDec( 'number', $this->number);
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

    //https://www.cnblogs.com/bieanju/p/6225722.html    https://blog.csdn.net/hzbskak/article/details/103718369

    public function clearRedis(){
        set_time_limit(0);
        $redis = app('redis');
        //$Rd = $redis->del("{$this->user_queue_key}");
        $Rd = $redis->hDel("{$this->goods_number_key}",$this->user_id);
         $a = $redis->hGet("{$this->user_queue_key}", $this->user_id); ////{$this->goods_number_key}$this->user_id
         if(!$a){
             dump($a);
         }
         if($Rd == 0){
              exit("Redis队列已释放！");
         }
     }
}
