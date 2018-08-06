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
                alert('更新成功！');
                location.reload();
            }
        }, 'json');
    });
    $('.del').click(function () {
       var tr= $(this).parent().parent();
        $.ajax({
                url: url_deleteChannel,
                type: 'POST',
                data: {'channel_id':$(this).val()},
                dataType: 'json',
                success: function (result) {
                     if(result.resp_code==0){
                            tr.remove();
                     }

                }
            });
    });
    $('#addChannel').click(function () {
        if($('#channel_name').val()==''){
            alert('渠道名不能为空');
            return false;
        }
        $.ajax({
            url: url_addChannel,
            type: 'POST',
            data: {"userid":data_channel,"channel_name":$('#channel_name').val()},
            dataType: 'json',
            success: function (result) {
                if(result.resp_code==0)
                {
                    alert(result.msg);
                    location.reload();
                }else {
                    alert(result.msg);
                }
            }
        });
    });
    $('.addcode').click(function () {
        $.ajax({
            url:url_addCode,
            type:'POST',
            data:{'channel_id':$(this).val()},
            dataType:'json',
            success:function (result) {
                console.log(result);
            }
        })
    })
});