<?php
namespace app\admin\controller\auth;
use app\common\controller\Backend;
use fast\Tree;
use think\facade\Cache;
use app\admin\model\AuthRule;
use think\Db;
use app\admin\model\AdminLog;
class Rule extends Backend{
    protected $model = null;
    protected $rulelist = [];
    protected $multiFields = 'ismenu,status';

    public function initialize(){
        parent::initialize();
        $this->model = new \app\admin\model\AuthRule();
        // 必须将结果集转换为数组
        $ruleList = $this->model->order('weigh', 'desc')->order('id', 'asc')->select()->toArray();
        Tree::instance()->init($ruleList);
        $this->rulelist = Tree::instance()->getTreeList(Tree::instance()->getTreeArray(0), 'title');
        $ruledata = [0 => __('None')];
        foreach ($this->rulelist as $k => &$v) {
            if (! $v['ismenu']) {
                continue;
            }
            $ruledata[$v['id']] = $v['title'];
        }
        unset($v);
        $this->assign('ruledata', $ruledata);
    }
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index(){
        if ($this->request->isAjax()) {
            $list = $this->rulelist;
            $total = count($this->rulelist);
            $result = ['total' => $total, 'rows' => $list];
            return json($result);
        }
        return $this -> fetch();
    }

    /**
     * 添加.
     */
    public function add(){
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a', [], 'strip_tags');
            if ($params) {
                if (! $params['ismenu'] && ! $params['pid']) {
                    $this->error(__('The non-menu rule must have parent'));
                }
                $result = $this->model->save($params);
                if ($result === false) {
                    $this->error($this->model->getError());
                }
                Cache::delete('__menu__');
                $this->success();
            }
            $this->error();
        }

        return $this->fetch();
    }


    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($ids = null){
        $row = $this->model->find(['id' => $ids]);
        if (! $row) {
            $this->error(__('No Results were found'));
        }
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a', [], 'strip_tags');
            if ($params) {
                if (! $params['ismenu'] && ! $params['pid']) {
                    $this->error(__('The non-menu rule must have parent'));
                }
                if ($params['pid'] != $row['pid']) {
                    $childrenIds = Tree::instance()->init(AuthRule::select()->toArray())->getChildrenIds($row['id']);
                    if (in_array($params['pid'], $childrenIds)) {
                        $this->error(__('Can not change the parent to child'));
                    }
                }

                //这里需要针对name做唯一验证
                $ruleValidate = validate('AuthRule', [], false, false);
                $ruleValidate->rule([
                    'name' => 'require|format|unique:AuthRule,name,'.$row->id,
                ]);

                $rs = $ruleValidate->check($params);
                if (! $rs) {
                    $this->error($ruleValidate->getError());
                }

                $result = $row->save($params);
                if ($result === false) {
                    $this->error($row->getError());
                }
                Cache::delete('__menu__');
                $this->success();
            }
            $this->error();
        }
        $this->assign('row', $row);
        return $this->fetch();
    }

    /**
     * 删除.
     */
    public function del($ids = ''){
        if ($ids) {
            $delIds = [];
            foreach (explode(',', $ids) as $k => $v) {
                $delIds = array_merge($delIds, Tree::instance()->getChildrenIds($v, true));
            }
            $delIds = array_unique($delIds);
            $count = $this->model->where('id', 'in', $delIds)->delete();
            if ($count) {
                Cache::delete('__menu__');
                $this->success();
            }
        }
        $this->error();
    }
}
