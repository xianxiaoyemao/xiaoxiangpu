define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'fast'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'index.web/index',
                    edit_url: 'index.web/edit',
                    del_url: 'index.web/del',
                    multi_url: 'index.web/multi',
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
                        {field: 'username', title: '用户姓名'},
                        {field: 'mobile', title: '手机号码'},
                        {field: 'remark', title: '备注信息'},
                        {field: 'status', title: '状态', formatter: Table.api.formatter.status},
                        {field: 'create_time', title: '创建时间', formatter: Table.api.formatter.datetime},
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
            /*$('#cate').on('change', function () {
               var category = $('#cate').val();
               $.post('goods/getSpec', {cate: category}, function (e) {
                   console.log(e);
               });
            });*/
        },
        edit: function () {
            Form.api.bindevent($("form[role=form]"));
        }
    };
    return Controller;
});
