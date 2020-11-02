define(['jquery', 'bootstrap', 'backend'], function ($, undefined, Backend) {
    var Controller = {
        index: function () {
            $(".btn-embossed").click(function () {
                var value = $("#sqlquery").val();
                // console.log(location.href);return
                // $.ajax('command.command/index')
                $.ajax({
                    type: "POST",
                    url: 'command.command/index',
                    data: {value:value},
                    dataType: 'json',
                    success: function (ret) {
                        console.log(ret)
                    }, error: function () {
                        Toastr.error(__('Network error'));
                    }, complete: function (e) {
                    }
                });
                return false;
            })
        }
    };
    return Controller;
});