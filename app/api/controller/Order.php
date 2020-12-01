<?php
namespace app\api\controller;
use app\BaseController;
use app\common\model\Cart;
use app\common\model\OrdersDetail;
use app\common\model\Product;
use app\common\model\ProductComment;
use app\Request;
use app\common\model\Address;
use app\common\model\Orders;
use app\common\model\Shops;
//use app\service\OrderService;
use think\facade\Db;
class Order extends BaseController
{
    //订单列表
    public function orderlist(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $uid = $request -> post('uid/d');
        $orderstatus = $request -> post('orderstatus/d') ?? 0;
        $where = "user_id = " .$uid;
        $orderwhere = "";
        switch ($orderstatus){
            case 1:
            case 2:
            case 4:
                $orderwhere.=$where." and o.status = $orderstatus";
                break;
            case 3:
                $orderwhere.=$where." and  o.status = 3";
                break;
            default:
                $orderwhere .= $where;
                break;
        }
//        dump($orderwhere);die;
//        $filed = "id,order_sn,payment_price,status,pay_status,createtime,remark";
//        if($orderwhere != 3){
//            $orderlist = $this -> orderconst($orderwhere,'list');
////            $orderlist = (new Orders)::with(['ordersdetail'])   -> select() -> toArray();
//        }else{
//
//        }
        $orderlist = $this -> orderconst($orderwhere,'list');
        return apiBack('success', "获取订单列表成功", '10000',$orderlist);
    }

    //订单详情
    public function orderdetail(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
//        $uid = $request -> post('uid/d');
        $orderid = $request -> post('orderid/d');
        if(!$orderid)  return apiBack('fail', '请选择订单', '10004');
        $addressid = (new Orders())::where('id',$orderid) -> value('addressid');
        //查询地址
        $addressinfo = (new Address)::where(['id'=>$addressid])
            -> field('contact_name,contact_phone,disarea,address')
            -> find() -> toArray();
        //查询订单详情
        $orderwhere = "o.id = $orderid";
        $orderlist = $this -> orderconst($orderwhere,'');
        $data = [
            'addresinfo' => $addressinfo,
            'orderinfo' => $orderlist,
        ];
        return apiBack('success', "获取详情成功", '10000',$data);
    }


    //商品评价
    public function evaluation(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $uid = $request -> post('uid/d');
        $pid = $request -> post('pid/d');
        $comment = $request -> post('comment');
        $images = $request -> post('images');
        $socre = $request -> post('socre');
        if(!$pid)  return apiBack('fail', '请选择商品', '10004');
        if(!$socre)  return apiBack('fail', '评分不能为空', '10004');
        $data = [
            'product_id' => $pid,
            'user_id' => $uid,
            'comment' => $comment,
            'images' => $images,
            'socre' => $socre,
            'createtime' => time(),
            'status' => 1,
        ];
        $res = (new ProductComment())::create($data);
        if($res){
            return apiBack('success', "评价成功", '10000');
        }else{
            return apiBack('fail', "评价失败", '10004');
        }
    }


    public function orderdel(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $orderid = $request -> post('orderid/d');
        $res = (new Orders())::where('id',$orderid)-> delete();
        if($res){
            (new OrdersDetail())::where('order_id',$orderid)-> delete();
            return apiBack('success', "删除成功", '10000');
        }else{
            return apiBack('fail', "删除失败", '10004');
        }
    }

    public function orderconst($orderwhere,$type){
        $orderlist = Db::name('orders_detail')  -> alias('od') -> where($orderwhere)
            -> join('orders o','od.order_id=o.id')
            -> join('product p','p.id=od.product_id')
            -> join('product_sku ps','ps.id=od.skuid')
            -> field('o.id as orderid,o.order_sn,o.createtime,o.paytime,o.payment_price,o.amount_price,o.status,o.is_code,o.up_code,
            od.speckey,od.specvalue,od.price,od.number,od.skuid,
            p.id as pid,p.shop_id,p.name,p.images,ps.title as skutitle')
            -> order('o.createtime desc')
            -> select() -> toArray();
        $result=[];
        $arr = [];
        if($type == 'list'){
            foreach ($orderlist as $v){
                $result[$v['shop_id']]['shopid'] = $v['shop_id'];
                $result[$v['shop_id']]['orderid'] = $v['orderid'];
                $result[$v['shop_id']]['order_sn'] = $v['order_sn'];
                $result[$v['shop_id']]['payment_price'] = $v['payment_price'];
                $result[$v['shop_id']]['amount_price'] = $v['amount_price'];
                $result[$v['shop_id']]['createtime'] = $v['createtime'];
                $result[$v['shop_id']]['paytime'] = $v['paytime'];
                $result[$v['shop_id']]['status'] = $v['status'];
                $result[$v['shop_id']]['is_code'] = $v['is_code'];
                $result[$v['shop_id']]['up_code'] = $v['up_code'];
                $result[$v['shop_id']]['shoptitle'] =  (new Shops())::where('id', $v['shop_id']) -> value('title');
                $result[$v['shop_id']]['orderlist'][]=[
                    'skuid' => $v['skuid'],
                    'pid' => $v['pid'],
                    'speckey' => $v['speckey'],
                    'specvalue' => $v['specvalue'],
                    'price' => $v['price'],
                    'number' => $v['number'],
                    'name' => $v['name'],
                    'images' => $v['images'],
                    'skutitle' => $v['skutitle'],
//                'images' => $v['images']
                ];
            }
        }else{
            foreach ($orderlist as $v){
                $result[$v['orderid']]['orderid'] = $v['orderid'];
                $result[$v['orderid']]['order_sn'] = $v['order_sn'];
                $result[$v['orderid']]['payment_price'] = $v['payment_price'];
                $result[$v['orderid']]['amount_price'] = $v['amount_price'];
                $result[$v['orderid']]['createtime'] = $v['createtime'];
                $result[$v['orderid']]['status'] = $v['status'];
                $result[$v['orderid']]['shopid'] = $v['shop_id'];
                $result[$v['orderid']]['shoptitle'] = (new Shops())::where('id', $v['shop_id']) -> value('title');
                $result[$v['orderid']]['orderlist'][]=[
                    'skuid' => $v['skuid'],
                    'pid' => $v['pid'],
                    'speckey' => $v['speckey'],
                    'specvalue' => $v['specvalue'],
                    'price' => $v['price'],
                    'number' => $v['number'],
                    'name' => $v['name'],
                    'images' => $v['images'],
                    'skutitle' => $v['skutitle'],
//                'images' => $v['images']
                ];
            }
        }
        $cartlist = array_merge($arr,$result);
        $ret = $this -> getOrderprice($cartlist);
        return $ret;
    }
    //计算订单总价
    public function getOrderprice($cartlist){
        $total_price = $coun_price = $pay_price = 0;
        foreach ($cartlist as $key => $val){
            foreach ($val['orderlist'] as $k => $v){
                $cartlist[$key]['total_number'] +=$v['number'];
                $cartlist[$key]['total_price'] += $v['price'] * (int)$v['number'];
            }
        }
        return $cartlist;
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
//        $order_rs=mysqli_query($conn,$sql); https://blog.csdn.net/qq43599939/article/details/78471459

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
        );

        //订单信息暂存 redis 哈希表，后续处理
        app('redis') -> hSet('order_info',$user_id,json_encode($goods_info));
        return '购买成功';
    }

        /**
     * 得到新订单号
     * @return  string
     */
    public function build_order_no(){
        /* 选择一个随机的方案 */
        $osn = 'XXP'.intval(date('Ymd')).
            substr(time(), -5) .
            substr(microtime(), 2, 5) .
            sprintf('%02d', rand(0, 99));
        return $osn;
    }






}
