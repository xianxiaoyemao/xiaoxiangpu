define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'software.management/index',
                    add_url: 'software.management/add',
                    edit_url: 'software.management/edit',
                    del_url: 'software.management/del',
                    multi_url: 'software.management/multi',
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
                        {field: 'name', title: '英文名称'},
                        {field: 'title', title: '中文名称'},
                        {field: 'edition', title: '版本系统'},
                        {field: 'version_id', title: '最新版本号'},
                        // {field: 'istype', title: '是否授权', formatter: Table.api.formatter.istype},
                        {field: 'status', title: __("Status"), formatter: Table.api.formatter.status},
                        {field: 'createtime', title:'创建时间', formatter: Table.api.formatter.datetime},
                        {field: 'updateime', title:'更新时间', formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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
        },
        api: {
            // formatter: {
            //     istype: function (value, row, index) {
            //         console.log(value)
            //         return '<div class="input-group input-group-sm" style="width:250px;">授权</div>';
            //     },
            // },
        }
    };
    return Controller;
});





