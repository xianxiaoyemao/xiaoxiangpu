<?php


namespace app\api\controller;
use app\BaseController;
use app\Request;
use app\common\SearchBuilders\SearchBuilders;
use fast\Redis;

class Ajax extends BaseController {
    public function search(Request $request){
        $page = $request->get('page',1);
        $perPage = 16;
        $keywords = $request -> get('keywords','');
        $order = ['id' => ['order' => 'desc']];
        $resutl =(new SearchBuilders()) -> search_doc('products',$keywords,$page,$perPage,$order);
        dump($resutl);die;
    }


    public function index(){
        Redis::set('as','1111111');
        dump(Redis::get('as'));die;
    }
}
