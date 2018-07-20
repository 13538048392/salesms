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
                        min: 5,
                        max: 18,
                        message: '用户名长度必须在5到18位之间'
                    },
                    regexp: {
                        regexp: /^[a-zA-Z0-9_]+$/,
                        message: '用户名只能包含大写、小写、数字和下划线'
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
            },
        },
        submitHandler: function (validator, form, submitButton) {
            $.post(form.attr('action'), form.serialize(), function(result) {
                console.log(result);
            }, 'json');
        }
    });
});


    // $(document).ready(function() {
    //     $('#defaultForm')
    //         .bootstrapValidator({
    //             message: 'This value is not valid',
    //             feedbackIcons: {
    //                 valid: 'glyphicon glyphicon-ok',
    //                 invalid: 'glyphicon glyphicon-remove',
    //                 validating: 'glyphicon glyphicon-refresh'
    //             },
    //             fields: {
    //                 username: {
    //                     message: 'The username is not valid',
    //                     validators: {
    //                         notEmpty: {
    //                             message: 'The username is required and can\'t be empty'
    //                         },
    //                         stringLength: {
    //                             min: 6,
    //                             max: 30,
    //                             message: 'The username must be more than 6 and less than 30 characters long'
    //                         },
    //                         /*remote: {
    //                             url: 'remote.php',
    //                             message: 'The username is not available'
    //                         },*/
    //                         regexp: {
    //                             regexp: /^[a-zA-Z0-9_\.]+$/,
    //                             message: 'The username can only consist of alphabetical, number, dot and underscore'
    //                         }
    //                     }
    //                 },
    //                 email: {
    //                     validators: {
    //                         notEmpty: {
    //                             message: 'The email address is required and can\'t be empty'
    //                         },
    //                         emailAddress: {
    //                             message: 'The input is not a valid email address'
    //                         }
    //                     }
    //                 },
    //                 password: {
    //                     validators: {
    //                         notEmpty: {
    //                             message: 'The password is required and can\'t be empty'
    //                         }
    //                     }
    //                 }
    //             }
    //         })
    //         .on('success.form.bv', function(e) {
    //             // Prevent form submission
    //             e.preventDefault();
    //             // Get the form instance
    //             var $form = $(e.target);
    //             // Get the BootstrapValidator instance
    //             var bv = $form.data('bootstrapValidator');
    //             // Use Ajax to submit form data
    //             $.post($form.attr('action'), $form.serialize(), function(result) {
    //                 console.log(result);
    //             }, 'json');
    //         });
    // });
