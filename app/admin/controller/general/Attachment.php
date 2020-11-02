<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/8/15
 * Time: 14:31
 */
namespace app\admin\controller\general;

use app\common\controller\Backend;
class Attachment extends Backend{
    protected $model = null;

    public function initialize(){
        parent::initialize();
        $this->model = new \app\common\model\Attachment();
        $this ->assign("mimetypeList", \app\common\model\Attachment::getMimetypeList());
    }
    /**
     * 查看.
     */
    public function index()
    {
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            $mimetypeQuery = [];
            $filter = $this->request->request('filter');
            $filterArr = (array) json_decode($filter, true);
            if (isset($filterArr['mimetype']) && stripos($filterArr['mimetype'], ',') !== false) {
                $this->request->get(['filter' => json_encode(array_merge($filterArr, ['mimetype' => '']))]);
                $mimetypeQuery = function ($query) use ($filterArr) {
                    $mimetypeArr = explode(',', $filterArr['mimetype']);
                    foreach ($mimetypeArr as $index => $item) {
                        $query->whereOr('mimetype', 'like', '%'.$item.'%');
                    }
                };
            }

            [$where, $sort, $order, $offset, $limit] = $this->buildparams();
            $total = $this->model
                ->where($mimetypeQuery)
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($mimetypeQuery)
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
            $cdnurl = preg_replace("/\/(\w+)\.php$/i", '', $this->request->domain());
            foreach ($list as $k => &$v) {
                $v['fullurl'] = ($v['storage'] == 'local' ? $cdnurl : $this->view->config['upload']['cdnurl']).$v['url'];
            }
            unset($v);
            $result = ['total' => $total, 'rows' => $list];

            return json($result);
        }

        return $this ->fetch();
    }

    /**
     * 选择附件.
     */
    public function select()
    {
        if ($this->request->isAjax()) {
            return $this->index();
        }

        return $this->fetch();
    }

    /**
     * 添加.
     */
    public function add()
    {
        if ($this->request->isAjax()) {
            $this->error();
        }

        return $this->fetch();
    }

    public function edit($ids = null)
    {
        $row = $this->model->find(['id' => $ids]);
        if (! $row) {
            $this->error(__('No Results were found'));
        }
        if ($this->request->isAjax()) {
            $this->error();
        }
        $this->assign('row', $row);
        return $this->fetch();
    }

    /**
     * 删除附件.
     *
     * @param array $ids
     */
    public function del($ids = '')
    {
        if ($ids) {
            \think\facade\Event::listen('upload_delete', function ($params) {
                $attachmentFile = app()->getRootPath().'/public'.$params['url'];
                if (is_file($attachmentFile)) {
                    @unlink($attachmentFile);
                }
            });
            $attachmentlist = $this->model->where('id', 'in', $ids)->select();
            foreach ($attachmentlist as $attachment) {
                \think\facade\Event::trigger('upload_delete', $attachment);
                $attachment->delete();
            }
            $this->success();
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }
}
