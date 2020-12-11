define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'user.user/index',
                    add_url: 'user.user/add',
                    edit_url: 'user.user/edit',
                    del_url: 'user.user/del',
                    multi_url: 'user.user/multi',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'createtime',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title:'ID'},
                        {field: 'avatar', title: '用户图片' ,events: Table.api.events.image, formatter: Table.api.formatter.image, operate: false},
                        {field: 'username', title: '用户名'},
                        {field: 'nickname', title: '用户昵称'},
                        {field: 'mobile', title: '手机号'},
                        {field: 'score', title: '积分'},
                        {field: 'money', title: '余额'},
                        {field: 'invitecode', title: '邀请码'},
                        {field: 'login_ip', title: '登录ip'},
                        {field: 'last_sign_time', title: '签到时间', formatter: Table.api.formatter.datetime},
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
    };
    return Controller;
});
