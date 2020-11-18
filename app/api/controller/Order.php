<?php
namespace app\api\controller;
use app\BaseController;
use app\common\model\Cart;
use app\Request;
use app\common\model\Address;
use app\common\model\Order as OrderModel;
//use app\service\OrderService;
class Order extends BaseController
{
    //创建订单
    public function createorder(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $uid = $request->post('uid');
        $type = $request->post('type');
        $cartid = $request->post('cartids');
        $pid = $request->post('pid');
        $skuid = $request->post('skuid');
        $price = $request->post('price');
        $skuid = $request->post('skuid');
        $quantity = $request->post('quantity');
        $addressid = $request->post('addressid');
        $orderdata =[
            'user_id' => $uid,
            'createtime' => time(),
        ];
        switch ($type){
            case 'cart':
                //查询店铺id
                $cartlist = (new Cart)::with(['product']) -> where('id in('.$cartid.')') -> select() -> toArray();

                $order_no = $this -> build_order_no();
                $orderdata['order_no'] = $order_no;
                break;
            case 'ljgm':
                $order_no = $this -> build_order_no();
                break;
        }

        dump();die;
        $orderdata =[
            'user_id' => $uid,
            'order_no' => $order_no,
            'createtime' => time(),
        ];

        $deleiedata = [
            'order_id'=>'order_id',
            'product_id' =>'product_id',
            'skuid' =>'skuid',
            'price' =>'price',
            'total_price' =>'total_price',
            'specvalue' =>'specvalue',
            'number' =>'number',
            'createtime' =>time(),
        ];
//        $res = (new OrderModel) -> save($param);
    }


    public function ordersave(){

    }

    //秒杀
    public function seconds_kill(){
        // 此处假设有10000个用户同时来抢购商品，注意：我们的库存只有500个
// 预期情况是：500个库存都被抢光，且没有出现超卖现象
//        if(app('redis') -> lLen('goods_list')==0){
//            return '已经卖完';
//        }
        $count=app('redis')->lpop('goods_store');
        if(!$count){
            insertLog('已经抢光了哦');
            return '已经抢光了哦';
        }
        //生成订单
        $order_sn= $this -> build_order_no();

//        $sql="insert into ih_order(order_sn,user_id,goods_id,sku_id,price)
//values('$order_sn','$user_id','$goods_id','$sku_id','$price')";
//        $order_rs=mysqli_query($conn,$sql);

//库存减少
//        $sql="update ih_store set number=number-{$number} where sku_id='$sku_id'";
//        $store_rs=mysqli_query($conn,$sql);
//        if(mysqli_affected_rows()){
//            insertLog('库存减少成功');
//        }else{
//            insertLog('库存减少失败');
//        }
        $users = 10000;

// 待秒杀的商品编号
        $goodsId = 1000001;

        for ($i=1; $i<=$users; $i++) {
            // 从队列左侧弹出一个元素，如果有值，说明还有剩余库存
            $rs = Redis::pop('stock_'.$goodsId);
            $num = sprintf('%05s', $i);
            if (!$rs) {
                echo '售罄了！用户'.$num;
                echo '<br/>';
                // 输出最终抢购成功的用户数量
                echo '最终抢购人数：'.app('redis')->getListSize('users').' 人';
                echo '<br/>';
                echo '<br/>';
                return;
            } else {
                // 将抢购成功的用户存入队列
                app('redis')->setLeftList('users',$num);
                echo '恭喜您！用户'.$num;
            }
            echo '<br/>';
        }

        //获取用户 id
        $user_id=input('post.id');

        //通过 集合的唯一性，判读此用户是否已经购买
        if(app('redis')->sIsMember('exist_list',$user_id)){
            return '你已经购买过了!';
        }
        //从商品队列删除一个商品，并获取值为商品 id
        $goods_id = app('redis')->rpop('goods_list');

        //订单信息
        $goods_info = array(
            'user_id'  =>$user_id,
            'goods_id' =>$goods_id,
            'time'     =>time(),
        ...
        );

        //订单信息暂存 redis 哈希表，后续处理
        app('redis') -> hSet('order_info',$user_id,json_encode($goods_info));
        return '购买成功';
    }
    //查看订单
    public function showorder(){

    }
    //生成订单
    public function ordercreate(Request $request,OrderService $orderService){

        app('redis') -> set('ss','1111111');
        dump(app('redis')-> get('ss'));die;
        $uid = $this -> uid;
        $address_id = $request->post('address_id');
        $remark = $request->post('remark');
        $items = $request->post('items');
        $shop_id = $request->post('shop_id');
        $address = Address::find($address_id);
        return $orderService -> addOrder($uid,$address,$remark,$items,$shop_id);
    }
    /**
     * 得到新订单号
     * @return  string
     */
    public function build_order_no(){
        /* 选择一个随机的方案 */
        mt_srand((double) microtime() * 1000000);
        return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }






}
