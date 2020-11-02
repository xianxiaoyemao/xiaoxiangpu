<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/8/19
 * Time: 10:05
 */
namespace app\admin\controller\article;
use app\common\controller\Backend;
use think\facade\Db;
class Article extends Backend{
    protected $model = null;
    protected $noNeedRight = ['check', 'rulelist'];
    protected $category = null;
    public function initialize(){
        parent::initialize();
        $this->model = Db::name('articles');
        $this -> category = Db::name('article_cate') -> where('status','normal') -> order('id asc') -> select();
        $artcate =[];
        foreach ($this -> category as $k => $v) {
            $artcate[$v['id']] = $v['title'];
        }
        $this -> assign('artcate',$artcate);
    }
    public function index(){
        if ($this->request->isAjax()) {
            [$where, $sort, $order, $offset, $limit] = $this->buildparams();
            $total = $this->model
                ->where($where)
                ->order($sort, $order)
                ->count();
            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select()->toArray();
            foreach ($this -> category as $kk => $vv) {
                foreach ($list as $k => &$v) {
                    if ($v['pid'] == $vv['id']) {
                        $list[$k]['catname'] = $vv['title'];
                    }
                }
            }
            unset($v);
            $result = ['total' => $total, 'rows' => $list];
            return json($result);
        }
        return $this -> fetch();
    }

    public function add(){
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            if($params){
                $data['cat_id'] = $params['cat_id'];
                $data['title'] = $params['title'];
                $data['intro'] = $params['intro'];
                $data['arturl'] = $params['arturl'];
                $data['author'] = $params['author'];
                $data['copyform'] = $params['copyform'];
                $data['ordersort'] = $params['ordersort'];
                $data['iselite'] = $params['iselite'];
                $data['status'] = $params['status'];
                $data['createtime'] =time();
                $data['updatetime'] =time();
                $articleid = $this -> model -> insert($data);
                if($articleid>0){
                    $data1['articles_id'] = $articleid;
                    $data1['content'] = $params['content'];
                    Db::name('articles_content') -> insert($data1);
                    Db::name('article_cate') -> where('id',$params['cat_id']) -> inc('articlenum');
                    $this->success();
                }
            }
            $this->error();
        }
        return $this-> fetch();
    }

    public function edit($ids=null){
        $row = $this ->model ->find($ids);
        if (! $row) {
            $this->error(__('No Results were found'));
        }
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            if($params){
                $data['cat_id'] = $params['cat_id'];
                $data['title'] = $params['title'];
                $data['intro'] = $params['intro'];
                $data['arturl'] = $params['arturl'];
                $data['author'] = $params['author'];
                $data['copyform'] = $params['copyform'];
                $data['ordersort'] = $params['ordersort'];
                $data['iselite'] = $params['iselite'];
                $data['status'] = $params['status'];
                $data['updatetime'] =time();
                $this ->model -> where('id',$params['id']) ->  update($data);

                $data1['content'] = $params['content'];
                Db::name('articles_content') -> where('articles_id',$params['id']) -> insert($data1);
                $this->success();
            }
            $this->error();
        }
        $this->assign('row', $row);
        return $this-> fetch();
    }

    /**
     * 删除.
     */
    public function del($ids = ''){
        if ($ids) {
            $this -> model -> where('id',$ids) -> delete();
            Db::name('articles_content') -> where('articles_id',$ids)-> delete();
            $this->success();
        }
        $this->error();
    }


    /**
     * 批量更新.
     *
     * @internal
     */
    public function multi($ids = '')
    {
        // 管理员禁止批量操作
        $this->error();
    }

    public function selectpage()
    {
        return parent::selectpage();
    }
}