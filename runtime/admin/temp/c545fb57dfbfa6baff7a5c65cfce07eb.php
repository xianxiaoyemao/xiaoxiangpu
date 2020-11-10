<?php /*a:6:{s:55:"D:\phpstudy_pro\WWW\xxp\app\admin\view\index\index.html";i:1596371714;s:55:"D:\phpstudy_pro\WWW\xxp\app\admin\view\common\meta.html";i:1576229932;s:57:"D:\phpstudy_pro\WWW\xxp\app\admin\view\common\header.html";i:1598191167;s:55:"D:\phpstudy_pro\WWW\xxp\app\admin\view\common\menu.html";i:1598181248;s:58:"D:\phpstudy_pro\WWW\xxp\app\admin\view\common\control.html";i:1598189895;s:57:"D:\phpstudy_pro\WWW\xxp\app\admin\view\common\script.html";i:1576229932;}*/ ?>
<!DOCTYPE html>
<html lang="<?php echo htmlentities($config['language']); ?>">
<head>
    <!-- 加载样式及META信息 -->
    <meta charset="utf-8">
<title><?php echo htmlentities((isset($title) && ($title !== '')?$title:'')); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="renderer" content="webkit">

<link rel="shortcut icon" href="/assets/img/favicon.ico"/>
<!-- Loading Bootstrap -->
<link href="/assets/css/backend<?php echo app('request')->env('app_debug')?'':'.min'; ?>.css?v=<?php echo htmlentities(config('site.version')); ?>"
      rel="stylesheet">
<!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
<!--[if lt IE 9]>
<script src="/assets/js/html5shiv.js"></script>
<script src="/assets/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript">
    var require = {
        config: <?php echo json_encode($config); ?>
    };
</script>
</head>
<body class="hold-transition skin-green sidebar-mini fixed" id="tabs">
<div class="wrapper">
    <!-- 头部区域 -->
    <header id="header" class="main-header">
        <a href="javascript:;" class="logo">
    <!-- 迷你模式下Logo的大小为50X50 -->
    <span class="logo-mini"><?php echo htmlentities(mb_strtoupper(mb_substr($site['web_name'],0,6,'utf-8'),'utf-8')); ?></span>
    <!-- 普通模式下Logo -->
    <span class="logo-lg"><b><?php echo htmlentities(mb_substr($site['web_name'],0,6,'utf-8')); ?></b></span>
</a>
<!-- 顶部通栏样式 -->
<nav class="navbar navbar-static-top">
    <!-- 边栏切换按钮-->
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
        <span class="sr-only">Toggle navigation</span>
    </a>

    <div id="nav" class="pull-left">
        <!--如果不想在顶部显示角标,则给ul加上disable-top-badge类即可-->
        <ul class="nav nav-tabs nav-addtabs disable-top-badge hidden-xs" role="tablist">
            <?php echo $navlist; ?>
        </ul>
    </div>

    <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
            <li>
                <a href="/" target="_blank"><i class="fa fa-home" style="font-size:14px;"></i></a>
                <!--href="index/index/index"-->
            </li>

            <!-- 账号信息下拉框 -->
            <li class="hidden-xs">
                <a href="javascript:;" data-toggle="checkupdate" title="<?php echo __('Check for updates'); ?>">
                    <i class="fa fa-refresh"></i>
                </a>
            </li>

            <!-- 清除缓存 -->
            <li>
                <a href="javascript:;" data-toggle="dropdown" title="<?php echo __('Wipe cache'); ?>">
                    <i class="fa fa-trash"></i>
                </a>
                <ul class="dropdown-menu wipecache">
                    <li><a href="javascript:;" data-type="all"><i class="fa fa-trash"></i> <?php echo __('Wipe all cache'); ?></a></li>
                    <li class="divider"></li>
                    <li><a href="javascript:;" data-type="content"><i class="fa fa-file-text"></i> <?php echo __('Wipe content cache'); ?></a></li>
                    <li><a href="javascript:;" data-type="template"><i class="fa fa-file-image-o"></i> <?php echo __('Wipe template cache'); ?></a></li>
                    <li><a href="javascript:;" data-type="addons"><i class="fa fa-rocket"></i> <?php echo __('Wipe addons cache'); ?></a></li>
                </ul>
            </li>

            <!-- 多语言列表 -->
            <?php if(config('lang_switch_on')): ?>
            <li class="hidden-xs">
                <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-language"></i></a>
                <ul class="dropdown-menu">
                    <li class="<?php echo $config['language']=='zh-cn' ? 'active' : ''; ?>">
                        <a href="?ref=addtabs&lang=zh-cn">简体中文</a>
                    </li>
                    <li class="<?php echo $config['language']=='en' ? 'active' : ''; ?>">
                        <a href="?ref=addtabs&lang=en">English</a>
                    </li>
                </ul>
            </li>
            <?php endif; ?>
            <!-- 全屏按钮 -->
            <li class="hidden-xs">
                <a href="#" data-toggle="fullscreen"><i class="fa fa-arrows-alt"></i></a>
            </li>

            <!-- 账号信息下拉框 -->
            <li class="dropdown user user-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <img src="<?php echo htmlentities(cdnurl($admin['avatar'])); ?>" class="user-image" alt="<?php echo htmlentities($admin['nickname']); ?>">
                    <span class="hidden-xs"><?php echo htmlentities($admin['nickname']); ?></span>
                </a>
                <ul class="dropdown-menu">
                    <!-- User image -->
                    <li class="user-header">
                        <img src="<?php echo htmlentities(cdnurl($admin['avatar'])); ?>" class="img-circle" alt="">
                        <p>
                            <?php echo htmlentities($admin['nickname']); ?>
                            <small><?php echo date('Y-m-d H:i:s',$admin['logintime']); ?></small>
                        </p>
                    </li>
                    <!-- Menu Body -->
                    <li class="user-body">
                        <div class="row">
                            <div class="col-xs-4 text-center">
                                <a href="https://www.iuok.cn" target="_blank"><?php echo __('FastAdmin'); ?></a>
                            </div>
                            <div class="col-xs-4 text-center">
                                <a href="https://forum.iuok.cn" target="_blank"><?php echo __('Forum'); ?></a>
                            </div>
                            <div class="col-xs-4 text-center">
                                <a href="https://doc.iuok.cn" target="_blank"><?php echo __('Docs'); ?></a>
                            </div>
                        </div>
                    </li>
                    <!-- Menu Footer-->
                    <li class="user-footer">
                        <div class="pull-left">
                            <a href="<?php echo url('general.profile/index'); ?>" class="btn btn-primary addtabsit"><i class="fa fa-user"></i>
                                个人资料</a>
                        </div>
                        <div class="pull-right">
                            <a href="<?php echo url('index/logout'); ?>" class="btn btn-danger"><i class="fa fa-sign-out"></i>
                                注销</a>
                        </div>
                    </li>
                </ul>
            </li>
            <!-- 控制栏切换按钮 -->
            <li class="hidden-xs">
                <a href="javascript:;" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
            </li>



            <!--<li class="dropdown notifications-menu">-->
                <!--<a href="#" class="dropdown-toggle" data-toggle="dropdown">-->
                    <!--<i class="fa fa-bell-o"></i>-->
                    <!--<span class="label label-warning"></span>-->
                <!--</a>-->
                <!--<ul class="dropdown-menu">-->
                    <!--<li class="header">Latest news</li>-->
                    <!--<li>-->
                        <!--&lt;!&ndash; FastAdmin最新更新信息,你可以替换成你自己站点的信息,assets/js/backend/index.js文件 &ndash;&gt;-->
                        <!--<ul class="menu">-->

                        <!--</ul>-->
                    <!--</li>-->
                    <!--<li class="footer"><a href="#" target="_blank">View all</a></li>-->
                <!--</ul>-->
            <!--</li>-->

            <!--<li class="dropdown messages-menu github-commits">-->
                <!--<a href="#" class="dropdown-toggle" data-toggle="dropdown">-->
                    <!--<i class="fa fa-github"></i>-->
                    <!--<span class="label label-info"></span>-->
                <!--</a>-->
                <!--<ul class="dropdown-menu">-->
                    <!--<li class="header">Recent commits</li>-->
                    <!--<li>-->
                        <!--<ul class="menu"></ul>-->
                    <!--</li>-->
                    <!--<li class="footer"><a href="#" target="_blank">View all</a></li>-->
                <!--</ul>-->
            <!--</li>-->
        </ul>
    </div>
</nav>
    </header>
    <!-- 左侧菜单栏-->
    <aside class="main-sidebar">
        <section class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel hidden-xs">
        <div class="pull-left image">
            <a href="<?php echo url('general.profile/index'); ?>" class="addtabsit"><img src="/<?php echo htmlentities(cdnurl($admin['avatar'])); ?>" class="img-circle" /></a>
        </div>
        <div class="pull-left info">
            <p><?php echo htmlentities($admin['nickname']); ?></p>
            <i class="fa fa-circle text-success"></i> 在线        </div>
    </div>

    <!-- search form -->
    <form action="" method="get" class="sidebar-form" onsubmit="return false;">
        <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="搜索菜单">
            <span class="input-group-btn">
                                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                                </button>
                            </span>
            <div class="menuresult list-group sidebar-form hide">
            </div>
        </div>
    </form>
    <!-- 移动端一级菜单 -->
    <div class="mobilenav visible-xs">

    </div>
    <!--如果想始终显示子菜单,则给ul加上show-submenu类即可,当multiplenav开启的情况下默认为展开-->
    <ul class="sidebar-menu <?php if($config['fastadmin']['multiplenav']): ?>show-submenu<?php endif; ?>">
        <li class="header">站内导航</li>
        <?php echo $menulist; ?>
        <li class="header">相关链接</li>
        <!--<li><a href="http://doc.fastadmin.net" target="_blank"><i class="fa fa-list text-red"></i> <span>官方文档</span></a></li>-->
        <!--<li><a href="http://forum.fastadmin.net" target="_blank"><i class="fa fa-comment text-yellow"></i> <span>社区交流</span></a></li>-->
        <!--<li><a href="https://jq.qq.com/?_wv=1027&k=487PNBb" target="_blank"><i class="fa fa-qq text-aqua"></i> <span>QQ交流群</span></a></li>-->

    </ul>
</section>
    </aside>
    <!-- 主体内容区域 -->
    <div class="content-wrapper tab-content tab-addtabs">

    </div>


    <<!-- 底部链接,默认隐藏 -->
    <footer class="main-footer hide">
        <div class="pull-right hidden-xs">
        </div>
        <strong>Copyright &copy; 2017-2018 <a href="http://fastadmin.net">Fastadmin</a>.</strong> All rights reserved.
    </footer>

    <!-- 右侧控制栏 -->
    <div class="control-sidebar-bg"></div>
    <style>
    .skin-list li{
        float:left; width: 33.33333%; padding: 5px;
    }
    .skin-list li a{
        display: block; box-shadow: 0 0 3px rgba(0,0,0,0.4);
    }
</style>
<aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
        <!--<li class="active">-->
            <!--<a href="#control-sidebar-setting-tab" data-toggle="tab" aria-expanded="true">-->
                <!--<i class="fa fa-wrench"></i>-->
            <!--</a>-->
        <!--</li>-->
        <li>
            <a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a>
        </li>
        <li>
            <a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a>
        </li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
        <!-- Home tab content -->
        <div class="tab-pane active" id="control-sidebar-setting-tab">
            <h4 class="control-sidebar-heading">布局设定</h4>
            <div class="form-group">
                <label class="control-sidebar-subheading">
                    <input type="checkbox" data-layout="fixed" class="pull-right"> 固定布局
                </label>
                <p>盒子模型和固定布局不能同时启作用</p>
            </div>
            <div class="form-group">
                <label class="control-sidebar-subheading">
                    <input type="checkbox" data-layout="layout-boxed" class="pull-right"> 盒子布局
                </label>
                <p>盒子布局最大宽度将被限定为1250px</p>
            </div>
            <div class="form-group">
                <label class="control-sidebar-subheading">
                    <input type="checkbox" data-layout="sidebar-collapse" class="pull-right"> 切换菜单栏
                </label>
                <p>切换菜单栏的展示或收起</p>
            </div>
            <div class="form-group">
                <label class="control-sidebar-subheading">
                    <input type="checkbox" data-enable="expandOnHover" class="pull-right"> 菜单栏自动展开
                </label>
                <p>鼠标移到菜单栏自动展开</p>
            </div>
            <div class="form-group">
                <label class="control-sidebar-subheading">
                    <input type="checkbox" data-menu="show-submenu" class="pull-right"> 显示菜单栏子菜单
                </label>
                <p>菜单栏子菜单将始终显示</p>
            </div>
            <div class="form-group">
                <label class="control-sidebar-subheading">
                    <input type="checkbox" data-menu="disable-top-badge" class="pull-right"> 禁用顶部彩色小角标
                </label>
                <p>左边菜单栏的彩色小角标不受影响</p>
            </div>
            <div class="form-group">
                <label class="control-sidebar-subheading">
                    <input type="checkbox" data-controlsidebar="control-sidebar-open" class="pull-right"> 切换右侧操作栏
                </label>
                <p>切换右侧操作栏覆盖或独占</p>
            </div>
            <div class="form-group">
                <label class="control-sidebar-subheading">
                    <input type="checkbox" data-sidebarskin="toggle" class="pull-right"> 切换右侧操作栏背景
                </label>
                <p>将右侧操作栏背景亮色或深色切换</p>
            </div>
            <h4 class="control-sidebar-heading">皮肤</h4>
            <ul class="list-unstyled clearfix skin-list">
                <li><a href="javascript:;" data-skin="skin-blue" style="" class="clearfix full-opacity-hover">
                    <div><span style="display:block; width: 20%; float: left; height: 7px; background: #367fa9;"></span><span class="bg-light-blue" style="display:block; width: 80%; float: left; height: 7px;"></span></div>
                    <div><span style="display:block; width: 20%; float: left; height: 20px; background: #222d32;"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span></div></a>
                    <p class="text-center no-margin">Blue</p>
                </li>
                <li><a href="javascript:;" data-skin="skin-black" class="clearfix full-opacity-hover">
                    <div style="box-shadow: 0 0 2px rgba(0,0,0,0.1)" class="clearfix"><span style="display:block; width: 20%; float: left; height: 7px; background: #fefefe;"></span><span style="display:block; width: 80%; float: left; height: 7px; background: #fefefe;"></span></div>
                    <div><span style="display:block; width: 20%; float: left; height: 20px; background: #222;"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span></div></a>
                    <p class="text-center no-margin">Black</p>
                </li>
                <li><a href="javascript:;" data-skin="skin-purple" class="clearfix full-opacity-hover">
                    <div><span style="display:block; width: 20%; float: left; height: 7px;" class="bg-purple-active"></span><span class="bg-purple" style="display:block; width: 80%; float: left; height: 7px;"></span></div>
                    <div><span style="display:block; width: 20%; float: left; height: 20px; background: #222d32;"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span></div></a>
                    <p class="text-center no-margin">Purple</p>
                </li>
                <li><a href="javascript:;" data-skin="skin-green" class="clearfix full-opacity-hover">
                    <div><span style="display:block; width: 20%; float: left; height: 7px;" class="bg-green-active"></span><span class="bg-green" style="display:block; width: 80%; float: left; height: 7px;"></span></div>
                    <div><span style="display:block; width: 20%; float: left; height: 20px; background: #222d32;"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span></div></a>
                    <p class="text-center no-margin">Green</p>
                </li>
                <li><a href="javascript:;" data-skin="skin-red" class="clearfix full-opacity-hover">
                    <div><span style="display:block; width: 20%; float: left; height: 7px;" class="bg-red-active"></span><span class="bg-red" style="display:block; width: 80%; float: left; height: 7px;"></span></div>
                    <div><span style="display:block; width: 20%; float: left; height: 20px; background: #222d32;"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span></div></a>
                    <p class="text-center no-margin">Red</p>
                </li>
                <li><a href="javascript:;" data-skin="skin-yellow" class="clearfix full-opacity-hover">
                    <div><span style="display:block; width: 20%; float: left; height: 7px;" class="bg-yellow-active"></span><span class="bg-yellow" style="display:block; width: 80%; float: left; height: 7px;"></span></div>
                    <div><span style="display:block; width: 20%; float: left; height: 20px; background: #222d32;"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span></div></a>
                    <p class="text-center no-margin">Yellow</p>
                </li>
                <li><a href="javascript:;" data-skin="skin-blue-light" class="clearfix full-opacity-hover">
                    <div><span style="display:block; width: 20%; float: left; height: 7px; background: #367fa9;"></span><span class="bg-light-blue" style="display:block; width: 80%; float: left; height: 7px;"></span></div>
                    <div><span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc;"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span></div></a>
                    <p class="text-center no-margin" style="font-size: 12px">Blue Light</p>
                </li>
                <li><a href="javascript:;" data-skin="skin-black-light" class="clearfix full-opacity-hover">
                    <div style="box-shadow: 0 0 2px rgba(0,0,0,0.1)" class="clearfix"><span style="display:block; width: 20%; float: left; height: 7px; background: #fefefe;"></span><span style="display:block; width: 80%; float: left; height: 7px; background: #fefefe;"></span></div>
                    <div><span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc;"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span></div></a>
                    <p class="text-center no-margin" style="font-size: 12px">Black Light</p>
                </li>
                <li><a href="javascript:;" data-skin="skin-purple-light" class="clearfix full-opacity-hover">
                    <div><span style="display:block; width: 20%; float: left; height: 7px;" class="bg-purple-active"></span><span class="bg-purple" style="display:block; width: 80%; float: left; height: 7px;"></span></div>
                    <div><span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc;"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span></div></a>
                    <p class="text-center no-margin" style="font-size: 12px">Purple Light</p>
                </li>
                <li><a href="javascript:;" data-skin="skin-green-light" class="clearfix full-opacity-hover">
                    <div><span style="display:block; width: 20%; float: left; height: 7px;" class="bg-green-active"></span><span class="bg-green" style="display:block; width: 80%; float: left; height: 7px;"></span></div>
                    <div><span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc;"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span></div></a>
                    <p class="text-center no-margin" style="font-size: 12px">Green Light</p>
                </li>
                <li><a href="javascript:;" data-skin="skin-red-light" class="clearfix full-opacity-hover">
                    <div><span style="display:block; width: 20%; float: left; height: 7px;" class="bg-red-active"></span><span class="bg-red" style="display:block; width: 80%; float: left; height: 7px;"></span></div>
                    <div><span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc;"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span></div></a>
                    <p class="text-center no-margin" style="font-size: 12px">Red Light</p>
                </li>
                <li><a href="javascript:;" data-skin="skin-yellow-light" class="clearfix full-opacity-hover">
                    <div><span style="display:block; width: 20%; float: left; height: 7px;" class="bg-yellow-active"></span><span class="bg-yellow" style="display:block; width: 80%; float: left; height: 7px;"></span></div>
                    <div><span style="display:block; width: 20%; float: left; height: 20px; background: #f9fafc;"></span><span style="display:block; width: 80%; float: left; height: 20px; background: #f4f5f7;"></span></div></a>
                    <p class="text-center no-margin" style="font-size: 12px;">Yellow Light</p>
                </li>
            </ul>
        </div>
        <div class="tab-pane" id="control-sidebar-home-tab">
            <h3 class="control-sidebar-heading">Recent Activity</h3>
            <ul class="control-sidebar-menu">
                <li>
                    <a href="javascript:void(0)">
                        <i class="menu-icon fa fa-birthday-cake bg-red"></i>

                        <div class="menu-info">
                            <h4 class="control-sidebar-subheading">Langdon's Birthday</h4>

                            <p>Will be 23 on April 24th</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)">
                        <i class="menu-icon fa fa-user bg-yellow"></i>

                        <div class="menu-info">
                            <h4 class="control-sidebar-subheading">Frodo Updated His Profile</h4>

                            <p>New phone +1(800)555-1234</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)">
                        <i class="menu-icon fa fa-envelope-o bg-light-blue"></i>

                        <div class="menu-info">
                            <h4 class="control-sidebar-subheading">Nora Joined Mailing List</h4>

                            <p>nora@example.com</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)">
                        <i class="menu-icon fa fa-file-code-o bg-green"></i>

                        <div class="menu-info">
                            <h4 class="control-sidebar-subheading">Cron Job 254 Executed</h4>

                            <p>Execution time 5 seconds</p>
                        </div>
                    </a>
                </li>
            </ul>
            <!-- /.control-sidebar-menu -->

            <h3 class="control-sidebar-heading">Tasks Progress</h3>
            <ul class="control-sidebar-menu">
                <li>
                    <a href="javascript:void(0)">
                        <h4 class="control-sidebar-subheading">
                            Custom Template Design
                            <span class="label label-danger pull-right">70%</span>
                        </h4>

                        <div class="progress progress-xxs">
                            <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)">
                        <h4 class="control-sidebar-subheading">
                            Update Resume
                            <span class="label label-success pull-right">95%</span>
                        </h4>

                        <div class="progress progress-xxs">
                            <div class="progress-bar progress-bar-success" style="width: 95%"></div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)">
                        <h4 class="control-sidebar-subheading">
                            Laravel Integration
                            <span class="label label-warning pull-right">50%</span>
                        </h4>

                        <div class="progress progress-xxs">
                            <div class="progress-bar progress-bar-warning" style="width: 50%"></div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="javascript:void(0)">
                        <h4 class="control-sidebar-subheading">
                            Back End Framework
                            <span class="label label-primary pull-right">68%</span>
                        </h4>

                        <div class="progress progress-xxs">
                            <div class="progress-bar progress-bar-primary" style="width: 68%"></div>
                        </div>
                    </a>
                </li>
            </ul>
            <!-- /.control-sidebar-menu -->

        </div>
        <!-- /.tab-pane -->
        <!-- Stats tab content -->
        <div class="tab-pane" id="control-sidebar-stats-tab">Stats Tab Content</div>
        <!-- /.tab-pane -->
        <!-- Settings tab content -->
        <div class="tab-pane" id="control-sidebar-settings-tab">
            <form method="post">
                <h3 class="control-sidebar-heading">General Settings</h3>

                <!-- /.form-group -->

                <div class="form-group">
                    <label class="control-sidebar-subheading">
                        Allow mail redirect
                        <input type="checkbox" class="pull-right" checked>
                    </label>

                    <p>
                        Other sets of options are available
                    </p>
                </div>
                <!-- /.form-group -->

                <div class="form-group">
                    <label class="control-sidebar-subheading">
                        Expose author name in posts
                        <input type="checkbox" class="pull-right" checked>
                    </label>

                    <p>
                        Allow the user to show his name in blog posts
                    </p>
                </div>
                <!-- /.form-group -->

                <!-- /.form-group -->
            </form>
        </div>
        <!-- /.tab-pane -->
    </div>
</aside>
</div>
<script src="/assets/js/require<?php echo app('request')->env('app_debug')?'':'.min'; ?>.js"
        data-main="/assets/js/require-backend<?php echo app('request')->env('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
</body>
</html>