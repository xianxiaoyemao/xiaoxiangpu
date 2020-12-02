define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'product.goods/index',
                    add_url: 'product.goods/add',
                    edit_url: 'product.goods/edit',
                    del_url: 'product.goods/del',
                    multi_url: 'product.goods/multi',
                }
            });
 
            var table = $("#table");
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                columns: [
                    [
                        {field: 'state', checkbox: true, },
                        {field: 'id', title: 'ID'},
                        {field: 'images', title: '商品图片' ,events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
                        {field: 'name', title: '商品名称'},
                        {field: 'price', title: '市场价格'},
                        {field: 'sales', title: '销量'},
                        {field: 'is_hot_sale', title: '热卖商品',formatter:function (value) {
                            if(value == 1){
                                return '<span class="text-success"><i class="fa fa-circle"></i> 是</span>';
                            }else{
                                return '<span class="text-success"><i class="fa fa-circle"></i> 不是</span>';
                            }
                            },operate:false
                        },
                        {field: 'is_recommend', title: '店长推荐', formatter:function (value) {
                            if(value == 1){
                                return '<span class="text-success"><i class="fa fa-circle"></i> 是</span>';
                            }else{
                                return '<span class="text-success"><i class="fa fa-circle"></i> 不是</span>';
                            }
                            },operate:false
                        },
                        {field: 'is_new', title: '新品',  formatter:function (value) {
                            if(value == 1){
                                return '<span class="text-success"><i class="fa fa-circle"></i> 是</span>';
                            }else{
                                return '<span class="text-success"><i class="fa fa-circle"></i> 不是</span>';
                            }
                            },operate:false
                        },
                        {field: 'is_rush', title: '抢购商品',  formatter:function (value) {
                            if(value == 1){
                                return '<span class="text-success"><i class="fa fa-circle"></i> 是</span>';
                            }else{
                                return '<span class="text-success"><i class="fa fa-circle"></i> 不是</span>';
                            }
                            },operate:false
                        },
                        {field: 'status', title: '状态', formatter:function (value) {
                            if(value == 1){
                                return '<span class="text-success"><i class="fa fa-circle"></i> 正常</span>';
                            }else{
                                return '<span class="text-success"><i class="fa fa-circle"></i> 隐藏</span>';
                            }
                            },operate:false
                         },
                        {field: 'createtime', title: '创建时间', formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), events: Table.api.events.operate, formatter: function (value, row, index) {
                                return Table.api.formatter.operate.call(this, value, row, index, table);
                            }}
                    ]
                ]
            });
            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Form.api.bindevent($("form[role=form]"));
            // uploadfile()
            zhuijia()
        },
        edit: function () {
            Form.api.bindevent($("form[role=form]"));
            zhuijia()
        }

    };
    return Controller;

    //上传商品详情图
    function uploadfile(){
        alert(11111111111)
    }

    //追加
    function zhuijia(){
        $('#append').click(function () {
            var html = $('#skuattr').html();
            var str = '<div id="skuattr" class="form-group">\n' +
                '                <div class="col-xs-12 col-sm-2">\n' +
                '                    <input type="text" class="form-control" size="40" name="rowsku[sku_title][]" value="" placeholder="属性名称"/>\n' +
                '                </div>\n' +
                '                <div class="col-xs-12 col-sm-2">\n' +
                '                    <input type="text" class="form-control" name="rowsku[sku_price][]" value="" placeholder="价格"/>\n' +
                '                </div>\n' +
                '                <div class="col-xs-12 col-sm-2">\n' +
                '                    <input type="text" class="form-control" name="rowsku[stock][]" value="" placeholder="库存"/>\n' +
                '                </div>\n' +
                '                <span class="btn btn-sm btn-danger btn-remove"><i class="fa fa-times"></i></span>\n' +
                '            </div>';
            $('#append').parent().prev().children('#prev').append(str);
        });
        $(document).on("click", "#prev .form-group .btn-remove", function () {
            $(this).parent().remove();
        });
    }
});


