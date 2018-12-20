$(function () {
    $('form').bootstrapValidator({
        useBlur:true,
        message: 'This value is not valid',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        fields: {
            first_name: {
                validators: {
                    notEmpty: {
                        message: first_name_not_null
                    }
                }
            },
            last_name: {
                validators: {
                    notEmpty: {
                        message: last_name_not_null
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
                    // tr.remove();
                    location.reload();
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


    $('.status').on('click', function(){
           var status = $(this).data('value');
           var id=$(this).data('id');
        $.post(url_changeStatus,{'status':status,'id':id},function(result){
            if(result.resp_code==0){
                alert(result.msg);
                location.reload();
            }
        })

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
                // console.log(result.data);
                var res = getDataList(result.data);
                // console.log(res);
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
function getDataList(data){
    for (var i = 0; i < data.length; i++) {
        var list = [data[i]];
        //console.log(list);
        var c_fix = '';
        while(list != ''){
            var one = list.shift();
            //console.log(one);
            html += "<tr>" +
                    "<td>" +c_fix+one.user_name+ "</td>" +
                    "<td>" +one.channel_name+ "</td>" +
                    "<td>" +one.first_name+ "</td>" +
                    "<td>" +one.last_name+ "</td>" +
                    "<td>" +one.create_time+ "</td>" +
                    "</tr>";
            // console.log(one);
            if( typeof one._child != "undefined" ) {
                list = list.concat(one._child);
                // console.log(list);
            }
            c_fix+='--';
        }
    }
    return html;
}

});
