<?php


namespace app\api\controller;
use app\BaseController;
use app\Request;
use app\common\SearchBuilders\SearchBuilders;
use fast\Redis;
use fast\Exclfile;
class Ajax extends BaseController {
    public function search(Request $request){
        $page = $request->get('page',1);
        $perPage = 16;
        $keywords = $request -> get('keywords','');
        $order = ['id' => ['order' => 'desc']];
        $resutl =(new SearchBuilders()) -> search_doc('products',$keywords,$page,$perPage,$order);
        dump($resutl);die;
    }


    public function ajaxupload(Request $request){
        $name = $request -> get('action');
        $uplaodsrc = '/storage/';
        $upload = new Exclfile(SHUJUCUNCHU);
        switch ($name){
            case 'pjtu':
                $url = $upload -> ImgSaceUpload('pjtuimg',$uplaodsrc);
                break;
        }
        if($url){
            return apiBack('success', '上传成功', '10000',['url'=>$url]);
        }else{
            return apiBack('fail', "上传失败", '10004');
        }
    }
}
