<?php
namespace app\api\controller;
use app\admin\model\Category;
use app\BaseController;
//use app\common\SearchBuilders\ProductSearchBuilder;
//use app\Exceptions\InvalidRequestException;
//use Illuminate\Http\Request;
//use Illuminate\Pagination\LengthAwarePaginator;
use think\facade\Db;
use app\Request;
use app\common\model\Product as PModel;
use app\common\model\ProductComment;
class Product extends BaseController{
    //产品列表
    public function productlist(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $type = $request->post('type');
        $where = "status=1";
        $limit = "";
        switch ($type){
            case 'ms':
                $where.=" and is_rush=1";
                break;
            case 'jmj':
                $where.=" and category_id=2";
                break;
            case 'xjtcp':
                $where.=" and category_id=3";
                break;
        }
        $productsfild = 'id,name,images,price,discount_price,shop_id,category_id,sales';
        $list = (new PModel)::with('shops')->where($where)
            ->field($productsfild)
            -> order('createtime desc')
            ->  select()  -> toArray();
        return apiBack('success', '成功', '10000', $list);
    }

    //商品详情
    public function productdetails(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $productid = $request->post('id');
        // 可以使用闭包查询
//        $details = (new PModel)::with(['shops' => function($query) {
//            $query->field('id');
//        }])->select() -> toArray();
        $details = (new PModel)::with(['skus'])->where('id',$productid)
            -> field('id,name,images,price,discount_price,shop_id,sales,rating,review,introduce,product_spec_info,parea')
            -> find() -> toArray();
        $details['product_spec_info'] = json_decode($details['product_spec_info'],true);
        return apiBack('success', '成功', '10000', $details);
    }

    //产品评价
    public function productevaluation(Request $request){
        if (!$request->isPost()) return apiBack('fail', '请求方式错误', '10004');
        $productid = $request->post('id');
        $details = (new ProductComment)::with(['product','user']) -> where('id',$productid)
            -> order('createtime desc')
            -> limit(0,20)
            -> select() -> toArray();
        return apiBack('success', '成功', '10000', $details);
    }


    //添加商品到购物车
    public function addcart(){
        //https://blog.csdn.net/yanhui_wei/article/details/8585509
//        https://blog.csdn.net/weixin_30333885/article/details/98378934
        //点击添加购物车按钮时，传递过来两个参数
        $product_spec_id = isset ( $_GET ['spid'] ) ? intval ( $_GET ['spid'] ) : 0;
        //产品规格id：product_spec_id,对应product_spec_id表中product_spec_id字段的值
        $quantity = isset ( $_GET ['num'] ) ? intval ( $_GET ['num'] ) : 0;//产品数量

    }

}
