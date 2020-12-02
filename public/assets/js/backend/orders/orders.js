define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'orders.orders/index',
                    add_url: '',
                    edit_url: '',
                    del_url: 'orders.orders/del',
                    multi_url: 'orders.orders/multi',
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
                        {field: 'order_sn', title: '订单编号'},

                        {field: 'user_id', title: '用户详情', table: table, events: Table.api.events.operate,
                        buttons:[
                        {
                            name: 'order_detail',
                            hidden:false,
                            title: '查看详情',
                            classname: 'btn btn-xs btn-success btn-dialog',
                            icon: 'fa fa-newspaper-o',
                            url: 'order/ordergoods/detail',
                        }]
                        , formatter: Table.api.formatter.operate},
                        {field: 'goods_price', title: '总金额'},
                        {field: 'amount_price', title: '优惠金额'},
                        {field: 'payment_price', title: '实际支付金额'},
                        {field: 'createtime', title: '添加时间', formatter: Table.api.formatter.datetime},
                        {field: 'paytime', title: '支付时间', formatter: Table.api.formatter.datetime},
                        {field: 'status', title: '状态',formatter:function (value) {
                                if(value == 1){
                                    return '<span style="color:red"><i class="fa fa-circle"></i> 未付款</span>';
                                }else if (value == 2){
                                    return '<span style="color:green"><i class="fa fa-circle"></i> 已付款</span>';
                                }else if (value == 3){
                                    return '<span class="text-success"><i class="fa fa-circle"></i> 已发货</span>';
                                }else if (value == 4){
                                    return '<span class="text-success"><i class="fa fa-circle"></i> 已签收</span>';
                                }
                            },operate:false
                        },
                        {field: 'operate', title: __('Operate'), events: Controller.api.events.operate, formatter: Controller.api.formatter.operate}
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
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            },
            formatter: {
                browser: function (value, row, index) {
                    return '<a class="btn btn-xs btn-browser">' + row.useragent.split(" ")[0] + '</a>';
                },
                operate: function (value, row, index) {
                    return '<a class="btn btn-info btn-xs btn-detail">订单详情</a> '
                            + Table.api.formatter.operate(value, row, index, $("#table"));
                },
            },
            events: {
                operate: $.extend({
                    'click .btn-detail': function (e, value, row, index) {
                        // console.log(row['id'])
                        Backend.api.open('orders.orders/detail/ids/' + row['id'], '订单详情');
                    }
                }, Table.api.events.operate)
            }
        }

    };
    return Controller;
});


