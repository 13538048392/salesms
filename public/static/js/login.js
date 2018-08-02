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
                validators: {
                    notEmpty: {
                        message: user_check
                    }
                }
            },
            password: {
                validators: {
                    notEmpty: {
                        message: pass_check
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
});