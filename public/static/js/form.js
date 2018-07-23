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
                    },
                    stringLength: {
                        min: 6,
                        max: 18,
                        message: '用户名长度必须在6到18位之间'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9_\-]+$/,
                        message: '用户名只能包含大写、小写、数字、下划线和-'
                    }
                }
            },
            email: {
                validators: {
                    notEmpty: {
                        message: '邮箱不能为空'
                    },
                    emailAddress: {
                        message: '邮箱地址格式有误'
                    }
                }
            },
            password: {
                validators: {
                    notEmpty: {
                        message: '密码不能为空'
                    },
                    stringLength: {
                        /*长度提示*/
                        min: 6,
                        max: 30,
                        message: '密码长度必须在6到30之间'
                    },
                    different: {//不能和用户名相同
                        field: 'loginName',//需要进行比较的input name值
                        message: '不能和用户名相同'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9_\.]+$/,
                        message: '密码由数字字母下划线和.组成'
                    }
                }
            },
            confirm_password: {
                validators: {
                    notEmpty: {
                        message: '密码无效'
                    },
                    stringLength: {
                        /*长度提示*/
                        min: 6,
                        max: 30,
                        message: '密码长度必须在6到30之间'
                    },
                    different: {//不能和用户名相同
                        field: 'loginName',//需要进行比较的input name值
                        message: '不能和用户名相同'
                    },
                    identical: {//相同
                        field: 'password',
                        message: '两次密码不一致'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9_\.]+$/,
                        message: '密码由数字字母下划线和.组成'
                    }
                }
            }
        }
    }).on('success.form.bv', function (e) {
        // Prevent form submission
        e.preventDefault();
        // Get the form instance
        var $form = $(e.target);
        // Get the BootstrapValidator instance
        var bv = $form.data('bootstrapValidator');
        // Use Ajax to submit form data
        $.post($form.attr('action'), $form.serialize(), function (result) {
            console.log(result);
        }, 'json');
    });
});
