$(function () {
    $('form').bootstrapValidator({
        message: 'This value is not valid',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            username: {
                message: '用户名验证失败',
                validators: {
                    notEmpty: {
                        message: '用户名不能为空'
                    }
                }
            },
            verify: {
                validators: {
                    notEmpty: {
                        message: '验证码不能为空'
                    },
                    verbose: false,
                    threshold: 4,
                    remote: {
                        url: checkCodeUrl,//验证地址
                        message: '验证码不正确',//提示消息
                        delay: 2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST'//请求方式
                    }
                }
            },
            password: {
                validators: {
                    notEmpty: {
                        message: '密码不能为空'
                    }
                }
            }
        }
    }).on('success.form.bv', function (e) {
        e.preventDefault();
        var $form = $(e.target);
        var bv = $form.data('bootstrapValidator');
        $.post($form.attr('action'), $form.serialize(), function (result) {
            if (result.resp_code == 0) {
                    location.href = indexurl+'?userid='+result.user_id;
            }else{
                 alert(result.msg);
            }
           
        }, 'json');
    });
    $('#imgverify').click(function () {
        $('#imgverify').attr('src', imgurl + "?id=" + Math.random());
    });
});