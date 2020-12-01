<?php
// +----------------------------------------------------------------------
// | 应用设置
// +----------------------------------------------------------------------

use think\facade\Env;

return [
    // 应用地址
    'app_host'         => Env::get('app.host', ''),
    // 应用的命名空间
    'app_namespace'    => '',
    // 是否启用路由
    'with_route'       => true,
    // 是否启用事件
    'with_event'       => true,
    // 自动多应用模式
    'auto_multi_app'   => true,
    // 应用映射（自动多应用模式有效）
    'app_map'          => ['*'=>'', 'api'=>'api', 'admin'=>'admin','wap'=>'wap'],
    // 默认应用
    'default_app'      => '',
    // 默认时区
    'default_timezone' => 'Asia/Shanghai',
    // 域名绑定（自动多应用模式有效）
    'domain_bind'      => [
        'mxxp' => 'api'
    ],

    'url_domain_deploy'=>true,
    // 禁止URL访问的应用列表（自动多应用模式有效）
    'deny_app_list'    => ['common'],

    // 默认输出类型
    'default_return_type'   => 'html',
    // 默认AJAX 数据返回格式,可选json xml ...
    'default_ajax_return'   => 'json',

    // 异常页面的模板文件
    'exception_tmpl'   => app()->getThinkPath() . 'tpl/think_exception.tpl',
    'dispatch_success_tmpl' => app()->getBasePath().'common'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'tpl'.DIRECTORY_SEPARATOR.'dispatch_jump.tpl',
    'dispatch_error_tmpl'   => app()->getBasePath().'common'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.'tpl'.DIRECTORY_SEPARATOR.'dispatch_jump.tpl',
    // 错误显示信息,非调试模式有效
    'error_message'         => '页面错误！请稍后再试～',
    // 显示错误信息
    'show_error_msg'        => true,

];
