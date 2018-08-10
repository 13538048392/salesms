$(function () {
    $('form').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            firstname: {
                message: '用户名验证失败',
                validators: {
                    notEmpty: {
                        message: 'FirstName can not be empty'
                    }
                }
            },
            lastname: {
                message: '用户名验证失败',
                validators: {
                    notEmpty: {
                        message: 'LastName can not be empty'
                    }
                }
            },
            phone: {
                validators: {
                    notEmpty: {
                        message: 'Phone can not be empty'
                    },
                    regexp: {
                        regexp: /^((13[0-9])|(14[5|7])|(15([0-3]|[5-9]))|(18[0,5-9]))\d{8}$/,
                        message: 'Phone is not available'
                    }
                }
            }
        }
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        $.post($form.attr('action'), $form.serialize(), function (result) {
            if (result.resp_code == 0 || result.resp_code == 1) {
                alert(lang_update_success);
                location.reload();
            }
        }, 'json');
    });
    $('.del').click(function () {
        var tr = $(this).parent().parent();
        $.ajax({
            url: url_deleteChannel,
            type: 'POST',
            data: {'channel_id': $(this).val()},
            dataType: 'json',
            success: function (result) {
                if (result.resp_code == 0) {
                    tr.remove();
                }

            }
        });
    });
    $('#addChannel').click(function () {
        if ($('#channel_name').val() == '') {
            alert(lang_channel_name);
            return false;
        }
        $.ajax({
            url: url_addChannel,
            type: 'POST',
            data: {"userid": data_channel, "channel_name": $('#channel_name').val()},
            dataType: 'json',
            success: function (result) {
                if (result.resp_code == 0) {
                    alert(result.msg);
                    location.reload();
                } else {
                    alert(result.msg);
                }
            }
        });
    });

    $('#search').click(function () {
        $.ajax({
            url: url_search,
            type: 'POST',
            dataType: 'json',
            data: {
                "first_name": $('#first_name').val(),
                "last_name": $('#last_name').val(),
                "username": $('#username').val(),
                "channel_name": $('#channel_name').val()
            },
            success: function (result) {
                var res = getDataList(result);
                $('#referrer_list').dataTable().fnClearTable();
                $('#referrer_list').dataTable().fnDestroy();
                // $('#referrer_list').dataTable().fnAddData(html);  //重新绑定table数据
               $('tbody').html(res);
                $('#referrer_list').dataTable(
                    {
                        searching: false, //去掉搜索框方法
                        bLengthChange: false,   //去掉每页显示多少条数据方法
                        bAutoWidth: false,                   //是否启用自动适应列宽
                        pageNumber: 1,            // 显示第几页数据，默认1
                        iDisplayLength: 5,
                        bSort: false,
                        bDe: true,
                        bPaginate: true,
                    }
                );
                html = '';
            }
        })
    });
var html='';
    function getDataList(data) {
        if (!data) return;
            $.each(data, function (key, value) {
                if ($.isArray(this)) {
                    getDataList(this);//当前节点是数组，继续递归
                } else {
                    html += '<tr>' +
                        '<td>' + value.user_name + '</td>' +
                        '<td>' + value.channel_name + '</td>' +
                        '<td>' + value.first_name + '</td>' +
                        '<td>' + value.last_name + '</td>' +
                        '<td>' + value.create_time + '</td>' +
                        '</tr>';
                }
            });
        return html;
    }
});