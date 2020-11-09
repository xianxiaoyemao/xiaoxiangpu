<?php /*a:4:{s:64:"D:\phpstudy_pro\WWW\xxp\app\admin\view\general\config\index.html";i:1604718839;s:58:"D:\phpstudy_pro\WWW\xxp\app\admin\view\layout\default.html";i:1596373268;s:55:"D:\phpstudy_pro\WWW\xxp\app\admin\view\common\meta.html";i:1576229932;s:57:"D:\phpstudy_pro\WWW\xxp\app\admin\view\common\script.html";i:1576229932;}*/ ?>
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
                                <style type="text/css">
    @media (max-width: 375px) {
        .edit-form tr td input{width:100%;}
        .edit-form tr th:first-child,.edit-form tr td:first-child{
            width:20%;
        }
        .edit-form tr th:nth-last-of-type(-n+2),.edit-form tr td:nth-last-of-type(-n+2){
            display: none;
        }
    }
    .edit-form table > tbody > tr td a.btn-delcfg{
        visibility: hidden;
    }
    .edit-form table > tbody > tr:hover td a.btn-delcfg{
        visibility: visible;
    }
</style>
<div class="panel panel-default panel-intro">
    <div class="panel-heading">
        <?php echo build_heading(null, false); ?>
        <ul class="nav nav-tabs">
            <?php foreach($siteList as $index=>$vo): ?>
            <li class="<?php echo !empty($vo['active']) ? 'active' : ''; ?>"><a href="#<?php echo htmlentities($vo['name']); ?>" data-toggle="tab"><?php echo __($vo['title']); ?></a></li>
            <?php endforeach; ?>
            <li>
                <a href="#addcfg" data-toggle="tab"><i class="fa fa-plus"></i></a>
            </li>
        </ul>
    </div>

    <div class="panel-body">
        <div id="myTabContent" class="tab-content">
            <?php foreach($siteList as $index=>$vo): ?>
            <div class="tab-pane fade <?php echo !empty($vo['active']) ? 'active in'  :  ''; ?>" id="<?php echo htmlentities($vo['name']); ?>">
                <div class="widget-body no-padding">
                    <form id="<?php echo htmlentities($vo['name']); ?>-form" class="edit-form form-horizontal" role="form" data-toggle="validator" method="POST" action="<?php echo url('general.config/edit'); ?>">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th width="15%"><?php echo __('Title'); ?></th>
                                <th width="68%"><?php echo __('Value'); ?></th>
                                <th width="15%"><?php echo __('Name'); ?></th>
                                <th width="2%"></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach($vo['list'] as $item): ?>
                            <tr>
                                <td><?php echo htmlentities($item['title']); ?></td>
                                <td>
                                    <div class="row">
                                        <div class="col-sm-8 col-xs-12">
                                            <?php switch($item['type']): case "string": ?>
                                            <input <?php echo htmlentities($item['extend']); ?> type="text" name="row[<?php echo htmlentities($item['name']); ?>]" value="<?php echo htmlentities(htmlentities($item['value'])); ?>" class="form-control" data-rule="<?php echo htmlentities($item['rule']); ?>" data-tip="<?php echo htmlentities($item['tip']); ?>" />
                                            <?php break; case "text": ?>
                                            <textarea <?php echo htmlentities($item['extend']); ?> name="row[<?php echo htmlentities($item['name']); ?>]" class="form-control" data-rule="<?php echo htmlentities($item['rule']); ?>" rows="5" data-tip="<?php echo htmlentities($item['tip']); ?>"><?php echo htmlentities(htmlentities($item['value'])); ?></textarea>
                                            <?php break; case "editor": ?>
                                            <textarea <?php echo htmlentities($item['extend']); ?> name="row[<?php echo htmlentities($item['name']); ?>]" id="editor-<?php echo htmlentities($item['name']); ?>" class="form-control editor" data-rule="<?php echo htmlentities($item['rule']); ?>" rows="5" data-tip="<?php echo htmlentities($item['tip']); ?>"><?php echo htmlentities(htmlentities($item['value'])); ?></textarea>
                                            <?php break; case "array": ?>
                                            <dl class="fieldlist" data-name="row[<?php echo htmlentities($item['name']); ?>]">
                                                <dd>
                                                    <ins><?php echo __('Array key'); ?></ins>
                                                    <ins><?php echo __('Array value'); ?></ins>
                                                </dd>
                                                <dd><a href="javascript:;" class="btn btn-sm btn-success btn-append"><i class="fa fa-plus"></i> <?php echo __('Append'); ?></a></dd>
                                                <textarea name="row[<?php echo htmlentities($item['name']); ?>]" class="form-control hide" cols="30" rows="5"><?php echo $item['value']; ?></textarea>
                                            </dl>
                                            <?php break; case "datetime": ?>
                                            <input <?php echo htmlentities($item['extend']); ?> type="text" name="row[<?php echo htmlentities($item['name']); ?>]" value="<?php echo htmlentities($item['value']); ?>" class="form-control datetimepicker" data-tip="<?php echo htmlentities($item['tip']); ?>" data-rule="<?php echo htmlentities($item['rule']); ?>" />
                                            <?php break; case "number": ?>
                                            <input <?php echo htmlentities($item['extend']); ?> type="number" name="row[<?php echo htmlentities($item['name']); ?>]" value="<?php echo htmlentities($item['value']); ?>" class="form-control" data-tip="<?php echo htmlentities($item['tip']); ?>" data-rule="<?php echo htmlentities($item['rule']); ?>" />
                                            <?php break; case "checkbox": if(is_array($item['content']) || $item['content'] instanceof \think\Collection || $item['content'] instanceof \think\Paginator): if( count($item['content'])==0 ) : echo "" ;else: foreach($item['content'] as $key=>$vo): ?>
                                            <label for="row[<?php echo htmlentities($item['name']); ?>][]-<?php echo htmlentities($key); ?>"><input id="row[<?php echo htmlentities($item['name']); ?>][]-<?php echo htmlentities($key); ?>" name="row[<?php echo htmlentities($item['name']); ?>][]" type="checkbox" value="<?php echo htmlentities($key); ?>" data-tip="<?php echo htmlentities($item['tip']); ?>" <?php if(in_array(($key), is_array($item['value'])?$item['value']:explode(',',$item['value']))): ?>checked<?php endif; ?> /> <?php echo htmlentities($vo); ?></label>
                                            <?php endforeach; endif; else: echo "" ;endif; break; case "radio": if(is_array($item['content']) || $item['content'] instanceof \think\Collection || $item['content'] instanceof \think\Paginator): if( count($item['content'])==0 ) : echo "" ;else: foreach($item['content'] as $key=>$vo): ?>
                                            <label for="row[<?php echo htmlentities($item['name']); ?>]-<?php echo htmlentities($key); ?>"><input id="row[<?php echo htmlentities($item['name']); ?>]-<?php echo htmlentities($key); ?>" name="row[<?php echo htmlentities($item['name']); ?>]" type="radio" value="<?php echo htmlentities($key); ?>" data-tip="<?php echo htmlentities($item['tip']); ?>" <?php if(in_array(($key), is_array($item['value'])?$item['value']:explode(',',$item['value']))): ?>checked<?php endif; ?> /> <?php echo htmlentities($vo); ?></label>
                                            <?php endforeach; endif; else: echo "" ;endif; break; case "select": case "selects": ?>
                                            <select <?php echo htmlentities($item['extend']); ?> name="row[<?php echo htmlentities($item['name']); ?>]<?php echo $item['type']=='selects' ? '[]' : ''; ?>" class="form-control selectpicker" data-tip="<?php echo htmlentities($item['tip']); ?>" <?php echo $item['type']=='selects' ? 'multiple' : ''; ?>>
                                                <?php if(is_array($item['content']) || $item['content'] instanceof \think\Collection || $item['content'] instanceof \think\Paginator): if( count($item['content'])==0 ) : echo "" ;else: foreach($item['content'] as $key=>$vo): ?>
                                                <option value="<?php echo htmlentities($key); ?>" <?php if(in_array(($key), is_array($item['value'])?$item['value']:explode(',',$item['value']))): ?>selected<?php endif; ?>><?php echo htmlentities($vo); ?></option>
                                                <?php endforeach; endif; else: echo "" ;endif; ?>
                                            </select>
                                            <?php break; case "image": case "images": ?>
                                            <div class="form-inline">
                                                <input id="c-<?php echo htmlentities($item['name']); ?>" class="form-control" size="50" name="row[<?php echo htmlentities($item['name']); ?>]" type="text" value="<?php echo htmlentities(htmlentities($item['value'])); ?>" data-tip="<?php echo htmlentities($item['tip']); ?>">
                                                <span><button type="button" id="plupload-<?php echo htmlentities($item['name']); ?>" class="btn btn-danger plupload" data-input-id="c-<?php echo htmlentities($item['name']); ?>" data-mimetype="image/*" data-multiple="<?php echo $item['type']=='image' ? 'false' : 'true'; ?>" data-preview-id="p-<?php echo htmlentities($item['name']); ?>"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                                                <span><button type="button" id="fachoose-<?php echo htmlentities($item['name']); ?>" class="btn btn-primary fachoose" data-input-id="c-<?php echo htmlentities($item['name']); ?>" data-mimetype="image/*" data-multiple="<?php echo $item['type']=='image' ? 'false' : 'true'; ?>"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                                                <span class="msg-box n-right" for="c-<?php echo htmlentities($item['name']); ?>"></span>
                                                <ul class="row list-inline plupload-preview" id="p-<?php echo htmlentities($item['name']); ?>"></ul>
                                            </div>
                                            <?php break; case "file": case "files": ?>
                                            <div class="form-inline">
                                                <input id="c-<?php echo htmlentities($item['name']); ?>" class="form-control" size="50" name="row[<?php echo htmlentities($item['name']); ?>]" type="text" value="<?php echo htmlentities(htmlentities($item['value'])); ?>" data-tip="<?php echo htmlentities($item['tip']); ?>">
                                                <span><button type="button" id="plupload-<?php echo htmlentities($item['name']); ?>" class="btn btn-danger plupload" data-input-id="c-<?php echo htmlentities($item['name']); ?>" data-multiple="<?php echo $item['type']=='file' ? 'false' : 'true'; ?>"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                                                <span><button type="button" id="fachoose-<?php echo htmlentities($item['name']); ?>" class="btn btn-primary fachoose" data-input-id="c-<?php echo htmlentities($item['name']); ?>" data-multiple="<?php echo $item['type']=='file' ? 'false' : 'true'; ?>"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                                                <span class="msg-box n-right" for="c-<?php echo htmlentities($item['name']); ?>"></span>
                                            </div>
                                            <?php break; case "switch": ?>
                                            <input id="c-<?php echo htmlentities($item['name']); ?>" name="row[<?php echo htmlentities($item['name']); ?>]" type="hidden" value="<?php echo $item['value']?1:0; ?>">
                                            <a href="javascript:;" data-toggle="switcher" class="btn-switcher" data-input-id="c-<?php echo htmlentities($item['name']); ?>" data-yes="1" data-no="0" >
                                                <i class="fa fa-toggle-on text-success <?php if(!$item['value']): ?>fa-flip-horizontal text-gray<?php endif; ?> fa-2x"></i>
                                            </a>
                                            <?php break; case "bool": ?>
                                            <label for="row[<?php echo htmlentities($item['name']); ?>]-yes"><input id="row[<?php echo htmlentities($item['name']); ?>]-yes" name="row[<?php echo htmlentities($item['name']); ?>]" type="radio" value="1" <?php echo !empty($item['value']) ? 'checked' : ''; ?> data-tip="<?php echo htmlentities($item['tip']); ?>" /> <?php echo __('Yes'); ?></label>
                                            <label for="row[<?php echo htmlentities($item['name']); ?>]-no"><input id="row[<?php echo htmlentities($item['name']); ?>]-no" name="row[<?php echo htmlentities($item['name']); ?>]" type="radio" value="0" <?php echo !empty($item['value']) ? '' : 'checked'; ?> data-tip="<?php echo htmlentities($item['tip']); ?>" /> <?php echo __('No'); ?></label>
                                            <?php break; ?>
                                            <?php endswitch; ?>
                                        </div>
                                        <div class="col-sm-4"></div>
                                    </div>

                                </td>
                                <td><?php echo htmlentities($item['name']); ?></td>
                                <td><a href="javascript:;" class="btn-delcfg text-muted" data-name="<?php echo htmlentities($item['name']); ?>"><i class="fa fa-times"></i></a></td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                            <tr>
                                <td></td>
                                <td>
                                    <button type="submit" class="btn btn-success btn-embossed"><?php echo __('OK'); ?></button>
                                    <button type="reset" class="btn btn-default btn-embossed"><?php echo __('Reset'); ?></button>
                                </td>
                                <td></td>
                                <td></td>
                            </tr>
                            </tfoot>
                        </table>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
            <div class="tab-pane fade" id="addcfg">
                <form id="add-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="<?php echo url('general.config/add'); ?>">
                    <div class="form-group">
                        <label  class="control-label col-xs-12 col-sm-2"><?php echo __('Type'); ?>:</label>
                        <div class="col-xs-12 col-sm-4">
                            <select name="row[type]" class="form-control selectpicker">
                                <?php if(is_array($typeList) || $typeList instanceof \think\Collection || $typeList instanceof \think\Paginator): if( count($typeList)==0 ) : echo "" ;else: foreach($typeList as $key=>$vo): ?>
                                <option value="<?php echo htmlentities($key); ?>" <?php if(in_array(($key), explode(',',"string"))): ?>selected<?php endif; ?>><?php echo htmlentities($vo); ?></option>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label  class="control-label col-xs-12 col-sm-2"><?php echo __('Group'); ?>:</label>
                        <div class="col-xs-12 col-sm-4">
                            <select name="row[group]" class="form-control selectpicker">
                                <?php if(is_array($groupList) || $groupList instanceof \think\Collection || $groupList instanceof \think\Paginator): if( count($groupList)==0 ) : echo "" ;else: foreach($groupList as $key=>$vo): ?>
                                <option value="<?php echo htmlentities($key); ?>" <?php if(in_array(($key), explode(',',"basic"))): ?>selected<?php endif; ?>><?php echo htmlentities($vo); ?></option>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="name" class="control-label col-xs-12 col-sm-2"><?php echo __('Name'); ?>:</label>
                        <div class="col-xs-12 col-sm-4">
                            <input type="text" class="form-control" id="name" name="row[name]" value="" data-rule="required; length(3~30); remote(general.config/check)" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="title" class="control-label col-xs-12 col-sm-2"><?php echo __('Title'); ?>:</label>
                        <div class="col-xs-12 col-sm-4">
                            <input type="text" class="form-control" id="title" name="row[title]" value="" data-rule="required" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="value" class="control-label col-xs-12 col-sm-2"><?php echo __('Value'); ?>:</label>
                        <div class="col-xs-12 col-sm-4">
                            <input type="text" class="form-control" id="value" name="row[value]" value="" data-rule="" />
                        </div>
                    </div>
                    <div class="form-group hide" id="add-content-container">
                        <label for="content" class="control-label col-xs-12 col-sm-2"><?php echo __('Content'); ?>:</label>
                        <div class="col-xs-12 col-sm-4">
                            <textarea name="row[content]" id="content" cols="30" rows="5" class="form-control" data-rule="required">value1|title1
value2|title2</textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="tip" class="control-label col-xs-12 col-sm-2"><?php echo __('Tip'); ?>:</label>
                        <div class="col-xs-12 col-sm-4">
                            <input type="text" class="form-control" id="tip" name="row[tip]" value="" data-rule="" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="rule" class="control-label col-xs-12 col-sm-2"><?php echo __('Rule'); ?>:</label>
                        <div class="col-xs-12 col-sm-4">
                            <div class="input-group pull-left">
                                <input type="text" class="form-control" id="rule" name="row[rule]" value="" data-tip="<?php echo __('Rule tips'); ?>"/>
                                <span class="input-group-btn">
                                    <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown" type="button"><?php echo __('Choose'); ?></button>
                                    <ul class="dropdown-menu pull-right rulelist">
                                        <?php if(is_array($ruleList) || $ruleList instanceof \think\Collection || $ruleList instanceof \think\Paginator): $i = 0; $__LIST__ = $ruleList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$item): $mod = ($i % 2 );++$i;?>
                                        <li><a href="javascript:;" data-value="<?php echo htmlentities($key); ?>"><?php echo htmlentities($item); ?><span class="text-muted">(<?php echo htmlentities($key); ?>)</span></a></li>
                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                    </ul>
                                </span>
                            </div>
                            <span class="msg-box n-right" for="rule"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="extend" class="control-label col-xs-12 col-sm-2"><?php echo __('Extend'); ?>:</label>
                        <div class="col-xs-12 col-sm-4">
                            <textarea name="row[extend]" id="extend" cols="30" rows="5" class="form-control" data-rule=""></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-xs-12 col-sm-2"></label>
                        <div class="col-xs-12 col-sm-4">
                            <button type="submit" class="btn btn-success btn-embossed"><?php echo __('OK'); ?></button>
                            <button type="reset" class="btn btn-default btn-embossed"><?php echo __('Reset'); ?></button>
                        </div>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

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