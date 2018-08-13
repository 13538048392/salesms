var InterValObj; //timer变量，控制时间
var count = 60; //间隔函数，1秒执行
var curCount;//当前剩余秒数

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
                    different: {//不能和用户名相同
                        field: 'username',//需要进行比较的input name值
                        message: user_both
                    },
                    regexp: {
                        regexp: /^((13[0-9])|(14[5|7])|(15([0-3]|[5-9]))|(18[0,5-9]))\d{8}$/,
                        message: phone_rule
                    },
                    remote: {
                        url: url_phone,//验证地址
                        message: phone_exist,//提示消息
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
            code: {
                validators: {
                    notEmpty: {
                        message: '请输入短信验证码'
                    },
                    remote: {
                        url: url_code,//验证地址
                        message: '验证码输入错误',//提示消息
                        delay: 2000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
                        type: 'POST',//请求方式
                       // data:{'phone':$('#phone').val(),'code':$('#code').val()}
                        //自定义提交数据，默认值提交当前input value
                        data: function(validator) {
                               return {
                                   phone: $('#phone').val(),
                                   code: $('#code').val()
                               };
                            }

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
                        field: 'username',//需要进行比较的input name值
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
                        field: 'username',//需要进行比较的input name值
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
            if (result.resp_code == "0") {
                alert(result.msg);
                setTimeout(function () {
                    location.href = url_login;
                }, 4);
            } else {
                alert(result.msg);
            }
        }, 'json');
    });

    $("#phone").focus(function () {
        $('form .row:eq(2)').show();
    });

    $("#phone").blur(function () {
        if (!$("#phone").val()) {
            $('form .row:eq(2)').hide();
        }
    })

    $("#sendMessage").click(function () {
        if ($.trim($("#phone").val()) == "") {
            alert('请输入手机号码');
            return false;
        }
        var ret = /^((13[0-9])|(14[5|7])|(15([0-3]|[5-9]))|(18[0,5-9]))\d{8}$/;
        if (!ret.test($("#phone").val())) {
            alert('手机号码不正确');
            return false;
        }
        //00+国际区号+号码
        var section = $('.regoin').data('value');
        var phone = $("#phone").val();//获取手机号码
        $.ajax({
            url: url_send_message,//请求地址，html页面传过来的
            type: "post",
            data: {'phone': phone,'section':section},
            success: function (result) {
                if (result.resp_code == 0) {
                    curCount = count;
                    //设置button效果，开始计时
                    $("#sendMessage").css("background-color", "LightSkyBlue");
                    $("#sendMessage").attr("disabled", "true");
                    // $('#sendMessage').html('<s></s>' + curCount + '秒');
                    InterValObj = window.setInterval(SetRemainTime, 1000); //启动计时器，1秒执行一次
                    alert(result.msg);
                } else {
                    alert(result.msg);
                }
            }
        });
    });
});

function SetRemainTime() {

    if (curCount == 0) {
        window.clearInterval(InterValObj);//停止计时器
        $("#sendMessage").removeAttr("disabled");//启用按钮
        $("#sendMessage").css("background-color", "");
        $("#sendMessage").html("重发验证码");
       // code = ""; //清除验证码。如果不清除，过时间后，输入收到的验证码依然有效
    }
    else {
        curCount--;
        $('#sendMessage').html('<s></s>' + curCount + '秒');
    }
}



