/*
* options: object,对象
*   title：模态框标题
*   body: 模态框内容，可以是jquery
*   size：'', 模态框大小，sm表示框是小的，不填则普通大小
    closeText: string，关闭按钮的文字，如果btns定义了优先使用btns，则忽略这个属性
*   btns: [], 数组，里面存btn对象
*       object：
*           btTitle: string, 按钮名称    
*           fn: function，按钮click的方法
*           class: string, 要添加的 class 名称
*/
var MessageBox = function (options) {
    var that = this,
        $dialogContainer = $('<div class="modal fade" tabindex="-1" role="dialog"></div>'),
        $mdialog = $('<div class="modal-dialog" role="document"></div>'),
        $content = $('<div class="modal-content"></div>'),
        _op = {
            title: options.title || '模态框',
            body: options.body || '内容',
            size: options.size || '', //
            btns: options.btns || []
        }

    if (options.size == 'sm') {
        $mdialog.addClass('modal-sm');
    }

    //创建头
    function buildHeader(title) {
        var $mHeader = $('<div class="modal-header"></div>');

        $mHeader.append('<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>');
        $mHeader.append('<h4 class="modal-title">' + title + '</h4>');

        return $mHeader;
    }

    //创建内容
    function buildBody(body) {
        var $mbody = $('<div class="modal-body"></div>');
        $mbody.append(body);

        return $mbody;
    }

    //创建按钮
    function buildBtns(btns, closeText) {
        var $footer = $('<div class="modal-footer"></div>');

        if (btns.length) {
            for (var i = 0; i < btns.length; i++) {
                var item = btns[i];

                var $b = $('<button type="button" class="btn"></button>');

                $b.html(item.title);
                $b.addClass(item.class);
                $b.click = function(e){
                    item.fn(e, that.dialog);
                }
                
                $footer.append($b);
            }
        } else {
            var $closeBtn = $('<button type="button" class="btn btn-default">'+ (closeText || '关闭') +'</button>');

            //关闭按钮
            $closeBtn.click(function(){
                that.close();
            });

            $footer.append($closeBtn);
        }

        return $footer;
    }

    $content.append(buildHeader(_op.title));
    $content.append(buildBody(_op.body));
    $content.append(buildBtns(_op.btns, _op.closeText));

    $mdialog.append($content);
    $dialogContainer.append($mdialog);

    //放入this中
    that.dialog = $dialogContainer;
    that.show();
}

MessageBox.prototype.show = function(){
    $('body').append(this.dialog);
    this.dialog.modal('show');
}

MessageBox.prototype.close = function(){
    var that = this;

    that.dialog.modal('hide');

    setTimeout(function(){
        that.dialog.remove();
    }, 300);
}

/*
* options: object,对象
*   title：模态框标题
*   body: 模态框内容，可以是jquery
*   size：'', 模态框大小，sm表示框是小的，不填则普通大小
    closeText: string，关闭按钮的文字，如果btns定义了优先使用btns，则忽略这个属性
*   btns: [], 数组，里面存btn对象
*       object：
*           btTitle: string, 按钮名称    
*           fn: function，按钮click的方法
*           class: string, 要添加的 class 名称
*/
MessageBox.create = function(options){
    new MessageBox(options);
}

// //调用方式
MessageBox.create({
    title: '模态',
    body: '555',
    size: 'sm'
})
