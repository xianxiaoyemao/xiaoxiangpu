<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/8/10
 * Time: 17:28
 */
namespace app\admin\controller\general;

use think\Exception;
//use app\common\library\Email;
use app\common\controller\Backend;
use app\common\model\Config as ConfigModel;
use think\facade\Db;
class Config extends Backend{
    protected $model = null;
    protected $noNeedRight = ['check', 'rulelist'];
    public function initialize(){
        parent::initialize();
        $this -> model = new ConfigModel();
//        ConfigModel::event('before_write', function ($row) {
//            if (isset($row['name']) && $row['name'] == 'name' && preg_match("/fast" . "admin/i", $row['value'])) {
//                throw new Exception(__("Site name incorrect"));
//            }
//        });
    }
    public function index(){
        $siteList = [];
        $groupList = ConfigModel::getGroupList();
        foreach ($groupList as $k => $v) {
            $siteList[$k]['name'] = $k;
            $siteList[$k]['title'] = $v;
            $siteList[$k]['list'] = [];
        }
        foreach ($this->model->select() as $k => $v) {
            if (!isset($siteList[$v['group']])) {
                continue;
            }
            $value = $v->toArray();
            $value['title'] = __($value['title']);
            if (in_array($value['type'], ['select', 'selects', 'checkbox', 'radio'])) {
                $value['value'] = explode(',', $value['value']);
            }
            $value['content'] = json_decode($value['content'], true);
            $value['tip'] = htmlspecialchars($value['tip']);
            $siteList[$v['group']]['list'][] = $value;
        }
        $index = 0;
        foreach ($siteList as $k => &$v) {
            $v['active'] = !$index ? true : false;
            $index++;
        }


        $this->assign('siteList', $siteList);
        $this->assign('typeList', ConfigModel::getTypeList());
        $this->assign('ruleList', ConfigModel::getRegexList());
        $this->assign('groupList', ConfigModel::getGroupList());
        return $this -> fetch();
    }
    /**
     * 添加.
     */
    public function add(){
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            if ($params) {
                foreach ($params as $k => &$v) {
                    $v = is_array($v) ? implode(',', $v) : $v;
                }
                try {
                    if (in_array($params['type'], ['select', 'selects', 'checkbox', 'radio', 'array'])) {
                        $params['content'] = json_encode(ConfigModel::decode($params['content']),
                            JSON_UNESCAPED_UNICODE);
                    } else {
                        $params['content'] = '';
                    }
                    $result = $this->model->create($params);
                    if ($result !== false) {
                        try {
                            $this->refreshFile();
                        } catch (Exception $e) {
                            $this->error($e->getMessage());
                        }
                        $this->success();
                    } else {
                        $this->error($this->model->getError());
                    }
                } catch (Exception $e) {
                    $this->error($e->getMessage());
                }
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
        return $this->fetch();
    }
    /**
     * 编辑.
     *
     * @param null $ids
     */
    public function edit($ids = null){
        if ($this->request->isPost()) {
            $row = $this->request->post('row/a');
            if ($row) {
//                $arrkey = array_keys($row);
                $configList = [];
                foreach ($this->model->select()-> toArray()  as $k => $v) {
                    if (isset($row[$v['name']])) {
                        $value = $row[$v['name']];
                        if (is_array($value) && isset($value['field'])) {
                            $value = json_encode(ConfigModel::getArrayData($value), JSON_UNESCAPED_UNICODE);
                        } else {
                            $value = is_array($value) ? implode(',', $value) : $value;
                        }
                        $v['value'] = $value;
                        $configList[] = $v;
                    }
                }
                $this->model->saveAll($configList);
                try {
                    $this->refreshFile();
                } catch (Exception $e) {
                    $this->error($e->getMessage());
                }
                $this->success();
            }
            $this->error(__('Parameter %s can not be empty', ''));
        }
    }

    public function del($ids = ''){
        $name = $this->request->request('name');
        $config = ConfigModel::getByName($name);
        if ($config) {
            try {
                $config->delete();
                $this->refreshFile();
            } catch (Exception $e) {
                $this->error($e->getMessage());
            }
            $this->success();
        } else {
            $this->error(__('Invalid parameters'));
        }
    }


    /**
     * 检测配置项是否存在.
     *
     * @internal
     */
    public function check(){
        $params = $this->request->post('row/a');
        if ($params) {
            $config = $this->model->find($params);
            if (! $config) {
                return $this->success();
            } else {
                return $this->error(__('Name already exist'));
            }
        } else {
            return $this->error(__('Invalid parameters'));
        }
    }

    /**
     * 刷新配置文件.
     */
    protected function refreshFile()
    {
        $config = [];
        foreach ($this->model->select() as $k => $v) {
            $value = $v->toArray();
            if (in_array($value['type'], ['selects', 'checkbox', 'images', 'files'])) {
                $value['value'] = explode(',', $value['value']);
            }
            if ($value['type'] == 'array') {
                $value['value'] = (array) json_decode($value['value'], true);
            }
            $config[$value['name']] = $value['value'];
        }
        file_put_contents(app()->getConfigPath().'site.php',
            '<?php'."\n\nreturn ".varexport($config, true).';');
    }

    /**
     * 发送测试邮件.
     *
     * @internal
     */
    public function emailtest(){
        $row = $this->request->post('row/a');
        \think\facade\Config::set(array_merge(\think\facade\Config::get('site'), $row), 'site');
        $receiver = $this->request->request('receiver');
        $email = new Email();
        $result = $email
            ->to($receiver)
            ->subject(__('This is a test mail'))
            ->message('<div style="min-height:550px; padding: 100px 55px 200px;">'.__('This is a test mail content').'</div>')
            ->send();
        if ($result) {
            $this->success();
        } else {
            $this->error($email->getError());
        }
    }

}