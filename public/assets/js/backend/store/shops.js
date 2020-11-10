define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'store.shops/index',
                    add_url: 'store.shops/add',
                    edit_url: 'store.shops/edit',
                    del_url: 'store.shops/del',
                    multi_url: 'store.shops/multi',
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
                        {field: 'id', title:'ID'},
                        {field: 'name', title: '英文名称'},
                        {field: 'title', title: '店铺名称'},
                        {field: 'phone', title:'店铺联系人'},
                        {field: 'createtime', title: '添加时间', formatter: Table.api.formatter.datetime},
                        {field: 'status', title: '状态',formatter:function (value) {
                                if(value == 1){
                                    return '<span class="text-success"><i class="fa fa-circle"></i> 正常</span>';
                                }else if (value == 2){
                                    return '<span class="text-success"><i class="fa fa-circle"></i> 隐藏</span>';
                                }else{
                                    return '<span class="text-success"><i class="fa fa-circle"></i> 删除</span>';
                                }
                            },operate:false
                        },
                        {field: 'audit_status', title: '审核状态', formatter:function (value) {
                                if(value == 1){
                                    return '<span style="color: red;">未审核</span>';
                                }else if (value == 2){
                                    return '<span class="text-success">审核通过</span>';
                                }else{
                                    return '<span style="color: red;">审核未通过</span>';
                                }
                            },operate: false
                        },
                        {field: 'operate', title: __('Operate'), events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });
            // 绑定TAB事件
            $('.panel-heading a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var field = $(this).closest("ul").data("field");
                var value = $(this).data("value");
                var options = table.bootstrapTable('getOptions');
                options.pageNumber = 1;
                options.queryParams = function (params) {
                    var filter = {};
                    if (value !== '') {
                        filter[field] = value;
                    }

                    params.filter = JSON.stringify(filter);
                    return params;
                };

                table.bootstrapTable('refresh', {});
                return false;
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        select:function(){
            alert($("#shen_btu").val());
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
