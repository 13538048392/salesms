$(function () {
    var $fadeList = $('[data-fade]').css('opacity', 0);

    function amin() {
        for (var i = 0; i < $fadeList.length; i++) {
            var $item = $($fadeList[i]);
            $item.fadeTo(600, 1);
            $item.data('fade', null);
        }
    }

    setTimeout(function () {
        amin();
    }, 100);

    //设置选中头部的
    var href = window.location.pathname,
        allNav = $('.header-navbar > li > a').not('.dropdown-toggle');

    allNav.each((index, item) => {
        var $item = $(item),
            itemHref = $(item).prop('href');

        if (itemHref.indexOf('#') == -1 && itemHref.search(href) != -1) {
            var $itemParents = $item.parent('li');

            $itemParents.siblings('li').removeClass('active');
            $itemParents.addClass('active');

            return false;
        }
    });


    // function scrollAmin() {
    //     var $this = $(document),
    //         wHeight = $(window).height(),
    //         sTop = $this.scrollTop();

    //     for (var i = 0; i < $fadeList.length; i++) {
    //         var $item = $($fadeList[i]),
    //             itemHTop = $item.offset().top;
    //         if (wHeight + sTop >= itemHTop) {
    //             $item.fadeTo(600, 1);
    //             $item.data('fade', null);
    //         }
    //     }
    // }

    // setTimeout(function () {
    //     scrollAmin();
    // }, 100);
});