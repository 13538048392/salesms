$(function () {
    $('form').bootstrapValidator({
        message: 'This value is not valid',
        excluded: [':disabled'],
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
                        message: user_not_null
                    },
                    stringLength: {
                        min: 8,
                        max: 18,
                        message: user_length
                    },
                    verbose: false,
                    threshold: 6,
                    remote: {
                        url: url_username,//验证地址
                        message: user_exist,//提示消息
                        delay: 2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST'//请求方式
                        /**自定义提交数据，默认值提交当前input value
                         *  data: function(validator) {
                               return {
                                   password: $('[name="passwordNameAttributeInYourForm"]').val(),
                                   whatever: $('[name="whateverNameAttributeInYourForm"]').val()
                               };
                            }
                         */
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9]+$/,
                        message: user_rule
                    }
                }
            },
            email: {
                validators: {
                    notEmpty: {
                        message: email_not_null
                    },
                    emailAddress: {
                        message: email_rule
                    },
                    verbose: false,
                    threshold: 6,
                    remote: {
                        url: url_email,//验证地址
                        message: email_exist,//提示消息
                        delay: 2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST'//请求方式
                        /**自定义提交数据，默认值提交当前input value
                         *  data: function(validator) {
                               return {
                                   password: $('[name="passwordNameAttributeInYourForm"]').val(),
                                   whatever: $('[name="whateverNameAttributeInYourForm"]').val()
                               };
                            }
                         */
                    }
                }
            },
            phone: {
                validators: {
                    notEmpty: {
                        message: phone_not_null
                    },
                    regexp: {
                        regexp: /^((13[0-9])|(14[5|7])|(15([0-3]|[5-9]))|(18[0,5-9]))\d{8}$/,
                        message: phone_rule
                    }
                }
            },
            password: {
                validators: {
                    notEmpty: {
                        message: pass_not_null
                    },
                    stringLength: {
                        /*长度提示*/
                        min: 6,
                        max: 30,
                        message: pass_length
                    },
                    different: {//不能和用户名相同
                        field: 'loginName',//需要进行比较的input name值
                        message: user_both
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9_\.]+$/,
                        message: pass_rule
                    }
                }
            },
            confirm_password: {
                validators: {
                    notEmpty: {
                        message: pass2_not_null
                    },
                    stringLength: {
                        /*长度提示*/
                        min: 6,
                        max: 30,
                        message: pass_length
                    },
                    different: {//不能和用户名相同
                        field: 'loginName',//需要进行比较的input name值
                        message: user_both
                    },
                    identical: {//相同
                        field: 'password',
                        message: two_pass_differ
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9_\.]+$/,
                        message: pass_rule
                    }
                }
            },
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
            if (result.resp_code =="0") {
                alert(result.msg);
                setTimeout(function () {
                    location.href = url_login;
                }, 4);
            } else {
                alert(result.msg);
            }
        }, 'json');
    });
});
