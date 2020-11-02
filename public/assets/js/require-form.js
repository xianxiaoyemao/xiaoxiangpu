define(['jquery', 'bootstrap', 'upload', 'validator'], function ($, undefined, Upload, Validator) {
    var Form = {
        config: {
            fieldlisttpl: '<dd class="form-inline"><input type="text" name="<%=name%>[<%=index%>][key]" class="form-control" value="<%=row.key%>" size="10" /> <input type="text" name="<%=name%>[<%=index%>][value]" class="form-control" value="<%=row.value%>" /> <span class="btn btn-sm btn-danger btn-remove"><i class="fa fa-times"></i></span> <span class="btn btn-sm btn-primary btn-dragsort"><i class="fa fa-arrows"></i></span></dd>'
        },
        events: {
            validator: function (form, onBeforeSubmit, onAfterSubmit) {
                if (!form.is("form"))
                    return;
                //绑定表单事件
                form.validator($.extend({
                    validClass: 'has-success',
                    invalidClass: 'has-error',
                    bindClassTo: '.form-group',
                    formClass: 'n-default n-bootstrap',
                    msgClass: 'n-right',
                    stopOnError: true,
                    display: function (elem) {
                        return $(elem).closest('.form-group').find(".control-label").text().replace(/\:/, '');
                    },
                    target: function (input) {
                        var $formitem = $(input).closest('.form-group'),
                            $msgbox = $formitem.find('span.msg-box');
                        if (!$msgbox.length) {
                            return [];
                        }
                        return $msgbox;
                    },
                    valid: function (ret) {
                        //验证通过提交表单
                        Form.api.submit($(ret), onBeforeSubmit, function (data, ret) {
                            if (typeof onAfterSubmit == 'function') {
                                if (!onAfterSubmit.call($(this), data, ret)) {
                                    return false;
                                }
                            }
                            //提示及关闭当前窗口
                            parent.Toastr.success(__('Operation completed'));
                            parent.$(".btn-refresh").trigger("click");
                            var index = parent.Layer.getFrameIndex(window.name);
                            parent.Layer.close(index);
                        });
                        return false;
                    }
                }, form.data("validator-options") || {}));

                //移除提交按钮的disabled类
                $(".layer-footer .btn.disabled", form).removeClass("disabled");
            },
            selectpicker: function (form) {
                //绑定select元素事件
                if ($(".selectpicker", form).size() > 0) {
                    require(['bootstrap-select', 'bootstrap-select-lang'], function () {
                        $('.selectpicker', form).selectpicker();
                        $(form).on("reset", function () {
                            setTimeout(function () {
                                $('.selectpicker').selectpicker('refresh').trigger("change");
                            }, 1);
                        });
                    });
                }
            },
            selectpage: function (form) {
                //绑定selectpage元素事件
                if ($(".selectpage", form).size() > 0) {
                    require(['selectpage'], function () {
                        $('.selectpage', form).selectPage({
                            eAjaxSuccess: function (data) {
                                data.list = typeof data.rows !== 'undefined' ? data.rows : (typeof data.list !== 'undefined' ? data.list : []);
                                data.totalRow = typeof data.total !== 'undefined' ? data.total : (typeof data.totalRow !== 'undefined' ? data.totalRow : data.list.length);
                                return data;
                            }
                        });
                    });
                    //给隐藏的元素添加上validate验证触发事件
                    $(document).on("change", ".sp_hidden", function () {
                        $(this).trigger("validate");
                    });
                    $(document).on("change", ".sp_input", function () {
                        $(this).closest(".sp_container").find(".sp_hidden").trigger("change");
                    });
                    $(form).on("reset", function () {
                        setTimeout(function () {
                            $('.selectpage', form).selectPageClear();
                        }, 1);
                    });
                }
            },
            cxselect: function (form) {
                //绑定cxselect元素事件
                if ($("[data-toggle='cxselect']", form).size() > 0) {
                    require(['cxselect'], function () {
                        $.cxSelect.defaults.jsonName = 'name';
                        $.cxSelect.defaults.jsonValue = 'value';
                        $.cxSelect.defaults.jsonSpace = 'data';
                        $("[data-toggle='cxselect']", form).cxSelect();
                    });
                }
            },
            citypicker: function (form) {
                //绑定城市远程插件
                if ($("[data-toggle='city-picker']", form).size() > 0) {
                    require(['citypicker'], function () {
                        $(form).on("reset", function () {
                            setTimeout(function () {
                                $("[data-toggle='city-picker']").citypicker('refresh');
                            }, 1);
                        });
                    });
                }
            },
            datetimepicker: function (form) {
                //绑定日期时间元素事件
                if ($(".datetimepicker", form).size() > 0) {
                    require(['bootstrap-datetimepicker'], function () {
                        var options = {
                            format: 'YYYY-MM-DD HH:mm:ss',
                            icons: {
                                time: 'fa fa-clock-o',
                                date: 'fa fa-calendar',
                                up: 'fa fa-chevron-up',
                                down: 'fa fa-chevron-down',
                                previous: 'fa fa-chevron-left',
                                next: 'fa fa-chevron-right',
                                today: 'fa fa-history',
                                clear: 'fa fa-trash',
                                close: 'fa fa-remove'
                            },
                            showTodayButton: true,
                            showClose: true
                        };
                        $('.datetimepicker', form).parent().css('position', 'relative');
                        $('.datetimepicker', form).datetimepicker(options);
                    });
                }
            },
            daterangepicker: function (form) {
                //绑定日期时间元素事件
                if ($(".datetimerange", form).size() > 0) {
                    require(['bootstrap-daterangepicker'], function () {
                        var ranges = {};
                        ranges[__('Today')] = [Moment().startOf('day'), Moment().endOf('day')];
                        ranges[__('Yesterday')] = [Moment().subtract(1, 'days').startOf('day'), Moment().subtract(1, 'days').endOf('day')];
                        ranges[__('Last 7 Days')] = [Moment().subtract(6, 'days').startOf('day'), Moment().endOf('day')];
                        ranges[__('Last 30 Days')] = [Moment().subtract(29, 'days').startOf('day'), Moment().endOf('day')];
                        ranges[__('This Month')] = [Moment().startOf('month'), Moment().endOf('month')];
                        ranges[__('Last Month')] = [Moment().subtract(1, 'month').startOf('month'), Moment().subtract(1, 'month').endOf('month')];
                        var options = {
                            timePicker: false,
                            autoUpdateInput: false,
                            timePickerSeconds: true,
                            timePicker24Hour: true,
                            autoApply: true,
                            locale: {
                                format: 'YYYY-MM-DD HH:mm:ss',
                                customRangeLabel: __("Custom Range"),
                                applyLabel: __("Apply"),
                                cancelLabel: __("Clear"),
                            },
                            ranges: ranges,
                        };
                        var origincallback = function (start, end) {
                            $(this.element).val(start.format(this.locale.format) + " - " + end.format(this.locale.format));
                            $(this.element).trigger('blur');
                        };
                        $(".datetimerange", form).each(function () {
                            var callback = typeof $(this).data('callback') == 'function' ? $(this).data('callback') : origincallback;
                            $(this).on('apply.daterangepicker', function (ev, picker) {
                                callback.call(picker, picker.startDate, picker.endDate);
                            });
                            $(this).on('cancel.daterangepicker', function (ev, picker) {
                                $(this).val('').trigger('blur');
                            });
                            $(this).daterangepicker($.extend({}, options, $(this).data()), callback);
                        });
                    });
                }
            },
            plupload: function (form) {
                //绑定plupload上传元素事件
                if ($(".plupload", form).size() > 0) {
                    Upload.api.plupload($(".plupload", form));
                }
            },
            faselect: function (form) {
                //绑定fachoose选择附件事件
                if ($(".fachoose", form).size() > 0) {
                    $(".fachoose", form).on('click', function () {
                        var that = this;
                        var multiple = $(this).data("multiple") ? $(this).data("multiple") : false;
                        var mimetype = $(this).data("mimetype") ? $(this).data("mimetype") : '';
                        var admin_id = $(this).data("admin-id") ? $(this).data("admin-id") : '';
                        var user_id = $(this).data("user-id") ? $(this).data("user-id") : '';
                        parent.Fast.api.open("general.attachment/select?element_id=" + $(this).attr("id") + "&multiple=" + multiple + "&mimetype=" + mimetype + "&admin_id=" + admin_id + "&user_id=" + user_id, __('Choose'), {
                            callback: function (data) {
                                var button = $("#" + $(that).attr("id"));
                                var maxcount = $(button).data("maxcount");
                                var input_id = $(button).data("input-id") ? $(button).data("input-id") : "";
                                maxcount = typeof maxcount !== "undefined" ? maxcount : 0;
                                if (input_id && data.multiple) {
                                    var urlArr = [];
                                    var inputObj = $("#" + input_id);
                                    var value = $.trim(inputObj.val());
                                    if (value !== "") {
                                        urlArr.push(inputObj.val());
                                    }
                                    urlArr.push(data.url)
                                    var result = urlArr.join(",");
                                    if (maxcount > 0) {
                                        var nums = value === '' ? 0 : value.split(/\,/).length;
                                        var files = data.url !== "" ? data.url.split(/\,/) : [];
                                        var remains = maxcount - nums;
                                        if (files.length > remains) {
                                            Toastr.error(__('You can choose up to %d file%s', remains));
                                            return false;
                                        }
                                    }
                                    inputObj.val(result).trigger("change").trigger("validate");
                                } else {
                                    $("#" + input_id).val(data.url).trigger("change").trigger("validate");
                                }
                            }
                        });
                        return false;
                    });
                }
            },
            fieldlist: function (form) {
                //绑定fieldlist
                if ($(".fieldlist", form).size() > 0) {
                    require(['dragsort', 'template'], function (undefined, Template) {
                        //刷新隐藏textarea的值
                        var refresh = function (name) {
                            var data = {};
                            var textarea = $("textarea[name='" + name + "']", form);
                            var container = textarea.closest("dl");
                            var template = container.data("template");
                            $.each($("input,select,textarea", container).serializeArray(), function (i, j) {
                                var reg = /\[(\w+)\]\[(\w+)\]$/g;
                                var match = reg.exec(j.name);
                                if (!match)
                                    return true;
                                match[1] = "x" + parseInt(match[1]);
                                if (typeof data[match[1]] == 'undefined') {
                                    data[match[1]] = {};
                                }
                                data[match[1]][match[2]] = j.value;
                            });
                            var result = template ? [] : {};
                            $.each(data, function (i, j) {
                                if (j) {
                                    if (!template) {
                                        if (j.key != '') {
                                            result[j.key] = j.value;
                                        }
                                    } else {
                                        result.push(j);
                                    }
                                }
                            });
                            textarea.val(JSON.stringify(result));
                        };
                        //监听文本框改变事件
                        $(document).on('change keyup', ".fieldlist input,.fieldlist textarea,.fieldlist select", function () {
                            refresh($(this).closest("dl").data("name"));
                        });
                        //追加控制
                        $(".fieldlist", form).on("click", ".btn-append,.append", function (e, row) {
                            var container = $(this).closest("dl");
                            var index = container.data("index");
                            var name = container.data("name");
                            var template = container.data("template");
                            var data = container.data();
                            index = index ? parseInt(index) : 0;
                            container.data("index", index + 1);
                            var row = row ? row : {};
                            var vars = {index: index, name: name, data: data, row: row};
                            var html = template ? Template(template, vars) : Template.render(Form.config.fieldlisttpl, vars);
                            $(html).insertBefore($(this).closest("dd"));
                            $(this).trigger("fa.event.appendfieldlist", $(this).closest("dd").prev());
                        });
                        //移除控制
                        $(".fieldlist", form).on("click", "dd .btn-remove", function () {
                            var container = $(this).closest("dl");
                            $(this).closest("dd").remove();
                            refresh(container.data("name"));
                        });
                        //拖拽排序
                        $("dl.fieldlist", form).dragsort({
                            itemSelector: 'dd',
                            dragSelector: ".btn-dragsort",
                            dragEnd: function () {
                                refresh($(this).closest("dl").data("name"));
                            },
                            placeHolderTemplate: "<dd></dd>"
                        });
                        //渲染数据
                        $(".fieldlist", form).each(function () {
                            var container = this;
                            var textarea = $("textarea[name='" + $(this).data("name") + "']", form);
                            if (textarea.val() == '') {
                                return true;
                            }
                            var template = $(this).data("template");
                            var json = {};
                            try {
                                json = JSON.parse(textarea.val());
                            } catch (e) {
                            }
                            $.each(json, function (i, j) {
                                $(".btn-append,.append", container).trigger('click', template ? j : {
                                    key: i,
                                    value: j
                                });
                            });
                        });
                    });
                }
            },
            switcher: function (form) {
                form.on("click", "[data-toggle='switcher']", function () {
                    if ($(this).hasClass("disabled")) {
                        return false;
                    }
                    var input = $(this).prev("input");
                    input = $(this).data("input-id") ? $("#" + $(this).data("input-id")) : input;
                    if (input.size() > 0) {
                        var yes = $(this).data("yes");
                        var no = $(this).data("no");
                        if (input.val() == yes) {
                            input.val(no);
                            $("i", this).addClass("fa-flip-horizontal text-gray");
                        } else {
                            input.val(yes);
                            $("i", this).removeClass("fa-flip-horizontal text-gray");
                        }
                        input.trigger('change');
                    }
                    return false;
                });
            },
            bindevent: function (form) {

            },
            slider: function (form) {
                if ($(".slider", form).size() > 0) {
                    require(['bootstrap-slider'], function () {
                        $('.slider').removeClass('hidden').css('width', function (index, value) {
                            return $(this).parents('.form-control').width();
                        }).slider().on('slide', function (ev) {
                            var data = $(this).data();
                            if (typeof data.unit !== 'undefined') {
                                $(this).parents('.form-control').siblings('.value').text(ev.value + data.unit);
                            }
                        });
                    });
                }
            },
            summernote:function (form) {
                if ($(".summernote", form).size() > 0) {
                    require(['summernote'], function () {
                        $(".summernote", form).summernote({
                            height: 250,
                            lang: 'zh-CN',
                            fontNames: [
                                'Arial', 'Arial Black', 'Serif', 'Sans', 'Courier',
                                'Courier New', 'Comic Sans MS', 'Helvetica', 'Impact', 'Lucida Grande',
                                "Open Sans", "Hiragino Sans GB", "Microsoft YaHei",
                                '微软雅黑', '宋体', '黑体', '仿宋', '楷体', '幼圆',
                            ],
                            fontNamesIgnoreCheck: [
                                "Open Sans", "Microsoft YaHei",
                                '微软雅黑', '宋体', '黑体', '仿宋', '楷体', '幼圆'
                            ],
                            dialogsInBody: true,
                            callbacks: {
                                onChange: function (contents) {
                                    $(this).val(contents);
                                    $(this).trigger('change');
                                },
                                onInit: function () {
                                },
                                onImageUpload: function (files) {
                                    var that = this;
                                    //依次上传图片
                                    for (var i = 0; i < files.length; i++) {
                                        Upload.api.send(files[i], function (data) {
                                            var url = Fast.api.cdnurl(data.url);
                                            $(that).summernote("insertImage", url, 'filename');
                                        });
                                    }
                                }
                            }
                        });
                    });
                }
            }
        },
        api: {
            submit: function (form, onBeforeSubmit, onAfterSubmit) {
                if (form.size() == 0)
                    return Toastr.error("表单未初始化完成,无法提交");
                //提交前事件
                var beforeSubmit = form.data("before-submit");
                //元素绑定函数
                if (beforeSubmit && typeof Form.api.custom[beforeSubmit] == 'function') {
                    if (!Form.api.custom[beforeSubmit].call(form)) {
                        return false;
                    }
                }
                //自定义函数
                if (typeof onBeforeSubmit == 'function') {
                    if (!onBeforeSubmit.call(form)) {
                        return false;
                    }
                }
                var type = form.attr("method").toUpperCase();
                type = type && (type == 'GET' || type == 'POST') ? type : 'GET';
                url = form.attr("action");
                url = url ? url : location.href;
                $.ajax({
                    type: type,
                    url: url,
                    data: form.serialize(),
                    dataType: 'json',
                    success: function (ret) {
                        if (ret.hasOwnProperty("code")) {
                            var data = ret.hasOwnProperty("data") && ret.data != "" ? ret.data : null;
                            var msg = ret.hasOwnProperty("msg") && ret.msg != "" ? ret.msg : "";
                            if (ret.code === 1) {
                                $('.form-group', form).removeClass('has-feedback has-success has-error');
                                //成功提交后事件
                                var afterSubmit = form.data("after-submit");
                                //元素绑定函数
                                if (afterSubmit && typeof Form.api.custom[afterSubmit] == 'function') {
                                    if (!Form.api.custom[afterSubmit].call(form, data, ret)) {
                                        return false;
                                    }
                                }
                                //自定义函数
                                if (typeof onAfterSubmit == 'function') {
                                    if (!onAfterSubmit.call(form, data, ret)) {
                                        return false;
                                    }
                                }
                                Toastr.success(msg ? msg : __('Operation completed'));
                            } else {
                                if (data && typeof data === 'object' && typeof data.token !== 'undefined') {
                                    $("input[name='__token__']").val(data.token);
                                }
                                Toastr.error(msg ? msg : __('Operation failed'));
                            }
                        } else {
                            Toastr.error(__('Unknown data format'));
                        }
                    }, error: function () {
                        Toastr.error(__('Network error'));
                    }, complete: function (e) {
                    }
                });
                return false;
            },

            bindevent: function (form, onBeforeSubmit, onAfterSubmit) {
                form = typeof form === 'object' ? form : $(form);
                var events = Form.events;
                events.bindevent(form);
                events.validator(form, onBeforeSubmit, onAfterSubmit);
                //绑定select元素事件
                events.selectpicker(form);
                //绑定selectpage元素事件
                events.selectpage(form);
                //绑定cxselect元素事件
                events.cxselect(form);
                //绑定城市远程插件
                events.citypicker(form);
                //绑定日期时间元素事件
                events.datetimepicker(form);
                //绑定日期时间元素事件
                events.daterangepicker(form);
                //绑定plupload上传元素事件
                events.plupload(form);
                //绑定fachoose选择附件事件
                events.faselect(form);
                //绑定fieldlist
                events.fieldlist(form);
                //
                events.switcher(form);

                events.slider(form);
                //绑定summernote事件
                events.summernote(form);

            },
            custom: {}
        },
    };
    return Form;
});