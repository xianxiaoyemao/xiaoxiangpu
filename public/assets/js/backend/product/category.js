define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'product.category/index',
                    add_url: 'product.category/add',
                    edit_url: 'product.category/edit',
                    del_url: 'product.category/del',
                    multi_url: 'product.category/multi',
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
                        {field: 'cate_name', title: '分类名称'},
                        {field: 'status', title: '状态', formatter:function (value) {
                            if(value == 1){
                                return '<span class="text-success"><i class="fa fa-circle"></i> 正常</span>';
                            }else if (value == 9){
                                return '<span class="text-success"><i class="fa fa-circle"></i> 隐藏</span>';
                            }
                            },operate:false
                        },
                        {field: 'createtime', title: '创建时间', formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), events: Table.api.events.operate, formatter: function (value, row, index) {
                                if(row.id == 1){
                                    return '';
                                }
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
        },
        edit: function () {
            Form.api.bindevent($("form[role=form]"));
        }
    };
    return Controller;
});
