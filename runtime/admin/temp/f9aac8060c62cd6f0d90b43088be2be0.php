<?php /*a:4:{s:58:"D:\phpstudy_pro\WWW\xxp\app\admin\view\auth\admin\add.html";i:1596974067;s:58:"D:\phpstudy_pro\WWW\xxp\app\admin\view\layout\default.html";i:1596373268;s:55:"D:\phpstudy_pro\WWW\xxp\app\admin\view\common\meta.html";i:1576229932;s:57:"D:\phpstudy_pro\WWW\xxp\app\admin\view\common\script.html";i:1576229932;}*/ ?>
<!DOCTYPE html>
<html lang="<?php echo htmlentities($config['language']); ?>">
    <head>
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

    <body class="inside-header inside-aside <?php echo defined('IS_DIALOG') && IS_DIALOG ? 'is-dialog' : ''; ?>">
        <div id="main" role="main">
            <div class="tab-content tab-addtabs">
                <div id="content">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <section class="content-header hide">
                                <h1>
                                    <?php echo __('Dashboard'); ?>
                                    <small><?php echo __('Control panel'); ?></small>
                                </h1>
                            </section>

                            <!-- RIBBON -->
                            <div id="ribbon">
                                <ol class="breadcrumb pull-left">
                                    <li><a href="dashboard" class="addtabsit"><i class="fa fa-dashboard"></i> <?php echo __('Dashboard'); ?></a></li>
                                </ol>
                                <ol class="breadcrumb pull-right">

                                </ol>
                            </div>
                            <!-- END RIBBON -->

                            <div class="content">
                                <form id="edit-form" class="form-horizontal form-ajax" role="form" data-toggle="validator" method="POST" action="">
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">所属组别:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo build_select('row[groupid]', $groupdata, null, ['class'=>'form-control', 'required'=>'']); ?>
            <!--<select class="form-control selectpicker" multiple="" name="group[]">-->
                <!--<option value="1"> 超级管理员</option>-->
                <!--<option value="2" selected="selected">&nbsp;├ 二级管理员</option>-->
                <!--<option value="3">&nbsp;│&nbsp;├ 三级管理员</option>-->
                <!--<option value="5">&nbsp;│&nbsp;└ 三级管理员2</option>-->
                <!--<option value="4">&nbsp;└ 二级管理员2</option>-->
            <!--</select>-->
        </div>
    </div>
    <div class="form-group">
        <label for="username" class="control-label col-xs-12 col-sm-2">用户名:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" id="username" name="row[username]" value="" data-rule="required;username" />
        </div>
    </div>
    <div class="form-group">
        <label for="email" class="control-label col-xs-12 col-sm-2">Email:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="email" class="form-control" id="email" name="row[email]" value="" data-rule="required;email" />
        </div>
    </div>
    <div class="form-group">
        <label for="nickname" class="control-label col-xs-12 col-sm-2">昵称:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" id="nickname" name="row[nickname]" value="" data-rule="required" />
        </div>
    </div>
    <div class="form-group">
        <label for="password" class="control-label col-xs-12 col-sm-2">密码:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="password" class="form-control" id="password" name="row[password]" value="" data-rule="password" />
        </div>
    </div>
    <div class="form-group">
        <label for="content" class="control-label col-xs-12 col-sm-2">状态:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo build_radios('row[status]', ['normal'=>__('Normal'), 'hidden'=>__('Hidden')]); ?>
        </div>
    </div>
    <div class="form-group hidden layer-footer">
        <label class="control-label col-xs-12 col-sm-2"></label>
        <div class="col-xs-12 col-sm-8">
            <button type="submit" class="btn btn-success btn-embossed disabled">确定</button>
            <button type="reset" class="btn btn-default btn-embossed">重置</button>
        </div>
    </div>
</form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo app('request')->env('app_debug')?'':'.min'; ?>.js"
        data-main="/assets/js/require-backend<?php echo app('request')->env('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
    </body>
</html>