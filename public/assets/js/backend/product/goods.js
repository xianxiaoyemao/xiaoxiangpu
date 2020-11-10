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
                        {field: 'name', title: '商品名称'},
                        {field: 'price', title: '价格'},
                        {field: 'discount_price', title: '优惠价格', operate:false, formatter: Table.api.formatter.label},
                        {field: 'sales', title: '销量'},
                        {field: 'is_hot_sale', title: '热卖商品'},
                        {field: 'is_recommend', title: '店长推荐'},
                        {field: 'is_new', title: '新品'},
                        {field: 'status', title: '状态', formatter: Table.api.formatter.status},
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
