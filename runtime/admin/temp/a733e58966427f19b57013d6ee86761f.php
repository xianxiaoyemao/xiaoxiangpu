<?php /*a:4:{s:62:"D:\phpstudy_pro\WWW\xxp\app\admin\view\product\goods\edit.html";i:1604921378;s:58:"D:\phpstudy_pro\WWW\xxp\app\admin\view\layout\default.html";i:1596373268;s:55:"D:\phpstudy_pro\WWW\xxp\app\admin\view\common\meta.html";i:1576229932;s:57:"D:\phpstudy_pro\WWW\xxp\app\admin\view\common\script.html";i:1576229932;}*/ ?>
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
        <label for="name" class="control-label col-xs-12 col-sm-2">商品名称:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" id="name" name="row[name]" value="<?php echo htmlentities(htmlentities($row['name'])); ?>" data-rule="required;name" />
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">商品分类:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo build_select('row[category_id]', $category, $row['category_id'], ['class'=>'form-control', 'required'=>'', 'id'=>'cate']); ?>
        </div>
    </div>

    <div class="form-group">
        <label for="price" class="control-label col-xs-12 col-sm-2">规格名称:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" name="row[spec_name]" value="<?php echo htmlentities($row['spec_name']); ?>"  placeholder="例：口味" />
        </div>
    </div>

    <div class="form-group">
        <label for="price" class="control-label col-xs-12 col-sm-2">规格属性:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" name="row[spec_value]" value="<?php echo htmlentities($row['spec_value']); ?>"  placeholder="属性多时，用 - 分割。例：麻辣-微辣-特辣" />
        </div>
    </div>

    <div class="form-group" style="margin-bottom: unset">
        <label class="control-label col-xs-12 col-sm-2">SKU属性:</label>
        <div id="prev" style="padding-left: 18%">
            <?php if(is_array($sku) || $sku instanceof \think\Collection || $sku instanceof \think\Paginator): if( count($sku)==0 ) : echo "" ;else: foreach($sku as $key=>$vo): ?>
            <div id="skuattr" class="form-group">
                <div class="col-xs-12 col-sm-2">
                    <input type="text" class="form-control" size="40" name="row[sku_title][]" value="<?php echo htmlentities($vo['title']); ?>" placeholder="属性名称"/>
                </div>
                <div class="col-xs-12 col-sm-2">
                    <input type="text" class="form-control" name="row[sku_price][]" value="<?php echo htmlentities($vo['price']); ?>" placeholder="价格"/>
                </div>
                <div class="col-xs-12 col-sm-2">
                    <input type="text" class="form-control" name="row[stock][]" value="<?php echo htmlentities($vo['stock']); ?>" placeholder="库存"/>
                </div>
                <span class="btn btn-sm btn-danger btn-remove"><i class="fa fa-times"></i></span>
            </div>
            <?php endforeach; endif; else: echo "" ;endif; ?>
        </div>

    </div>

    <div class="form-group" style="text-align: center">
        <a href="javascript:;" id="append" class="btn btn-sm btn-success btn-append"><i class="fa fa-plus"></i> <?php echo __('Append'); ?></a>
    </div>

    <div class="form-group">
        <label for="price" class="control-label col-xs-12 col-sm-2">市场价格:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" name="row[price]" value="<?php echo htmlentities(htmlentities($row['price'])); ?>" data-rule="required" />
        </div>
    </div>

    <div class="form-group">
        <label for="sales" class="control-label col-xs-12 col-sm-2">商品销量:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="number" class="form-control" name="row[sales]" value="<?php echo htmlentities(htmlentities($row['sales'])); ?>" />
        </div>
    </div>
    <div class="form-group">
        <label for="sales" class="control-label col-xs-12 col-sm-2">商品库存:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="number" class="form-control" name="row[inventory]" value="<?php echo htmlentities(htmlentities($row['inventory'])); ?>" />
        </div>
    </div>
    <div class="form-group">
        <label for="is_hot_sale" class="control-label col-xs-12 col-sm-2">是否热卖:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo build_radios('row[is_hot_sale]', ['normal'=>'是', 'hidden'=>'不是'], $row['is_hot_sale']); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="c-avatar" class="control-label col-xs-12 col-sm-2">商品主图:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group">
                <input id="c-avatar" data-rule="" class="form-control" size="50" name="row[images]" type="text" value="<?php echo htmlentities($row['images']); ?>">
                <div class="input-group-addon no-border no-padding">
                    <span>
                        <button type="button" id="plupload-avatar" class="btn btn-danger plupload" data-input-id="c-avatar" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="true" data-preview-id="p-avatar">
                            <i class="fa fa-upload"></i> 上传
                        </button>
                    </span>
                </div>
            </div>
            <span class="msg-box n-right" for="c-avatar"></span>
            <ul class="row list-inline plupload-preview" id="p-avatar"></ul>
        </div>
    </div>
    <div class="form-group">
        <label for="is_recommend" class="control-label col-xs-12 col-sm-2">店长推荐:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo build_radios('row[is_recommend]', ['normal'=>'是', 'hidden'=>'不是'], $row['is_recommend']); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="is_new" class="control-label col-xs-12 col-sm-2">新品:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo build_radios('row[is_new]', ['normal'=>'是', 'hidden'=>'不是'], $row['is_new']); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="content" class="control-label col-xs-12 col-sm-2">状态:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo build_radios('row[status]', ['normal'=>__('Normal'), 'hidden'=>__('Hidden')], $row['status']); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="content" class="control-label col-xs-12 col-sm-2">商品描述:</label>
        <div class="col-xs-12 col-sm-8">
            <textarea name="row[introduce]" id="c-editor" cols="60" rows="5" class="form-control editor"><?php echo htmlentities(htmlentities($row['introduce'])); ?></textarea>
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
<style>
    .radio label {
        margin-right: 50px;
    }
    .checkbox label {
        margin-right: unset;
    }
    .checkbox {
        width: 45px;
        margin-right: 0;
    }
</style>

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