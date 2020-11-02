define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'article.article/index',
                    add_url: 'article.article/add',
                    edit_url: 'article.article/edit',
                    del_url: 'article.article/del',
                    multi_url: 'article.article/multi',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'category_id', title: __('Category_id')},
                        {field: 'catname', title: '分类名称'},
                        {field: 'title', title: '文章标题'},
                        {field: 'user_id', title: __('User_id')},
                        {field: 'user_ids', title: __('User_ids')},
                        {field: 'image', title: __('Image'), formatter: Table.api.formatter.image},
                        {field: 'views', title: __('Views')},
                        {field: 'createtime', title: '发布时间', formatter: Table.api.formatter.datetime},
                        {field: 'status', title: __('Status'), formatter: Table.api.formatter.status},
                        {field: 'state_text', title: __('State'), operate: false},
                        {field: 'operate', title: __('Operate'), events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});