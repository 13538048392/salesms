function drawQrcodeImag(type, qucodeUrl) {
    // var canvas = document.getElementById('canvas'),
    var canvas = document.createElement('canvas'),
        ctx = canvas.getContext('2d'),
        image = new Image();

    // image.setAttribute("crossOrigin", 'anonymous')

    canvas.setAttribute('width', 420);
    canvas.setAttribute('height', 680);

    image.onload = function (e) {
        ctx.drawImage(image, 0, 0, 420, 680, 0, 0, 420, 680);

        var qrCodeImage = new Image(500, 500);

        // qrCodeImage.setAttribute("crossOrigin", 'anonymous')

        qrCodeImage.onload = function (e) {

            ctx.scale(1, 1);

            ctx.drawImage(qrCodeImage, 0, 0, 500, 500, 85, 265, 500, 500);

            // document.body.append(canvas);

            exportImage(canvas);
        }

        qrCodeImage.src = qucodeUrl;
    }

    image.src = (type == "sales" ? '/static/images/sales_qrcode_bg.jpg' : '/static/images/doctor_qrcode_bg.jpg')
}

// drawQrcodeImag('sales', './qrcode.jpeg');

// function openDataUriWindow(url) {
//     var html = '<html>' +
//         '<style>html, body { padding: 0; margin: 0; } iframe { width: 100%; height: 100%; border: 0;}  </style>' +
//         '<body>' +
//         '<iframe src="' + url + '"></iframe>' +
//         '</body></html>';
//     a = window.open();
//     a.document.write(html);
// }

// function dataURLtoBlob(dataurl) {
//     var arr = dataurl.split(','),
//         mime = arr[0].match(/:(.*?);/)[1],
//         bstr = atob(arr[1]),
//         n = bstr.length,
//         u8arr = new Uint8Array(n);
//     while (n--) {
//         u8arr[n] = bstr.charCodeAt(n);
//     }
//     return new Blob([u8arr], {
//         type: mime
//     });
// }


function exportImage(canvas) {
    // var canvas = document.getElementById('canvas');
    //  var aEle = document.createElement('a');

    // // openDataUriWindow(canvas.toDataURL("image/png"));

    // // debugger

    // // aEle.setAttribute('href', canvas.toDataURL("image/png"));
    // aEle.setAttribute('href', canvas.toDataURL("image/png", 1.0).replace("image/png", "image/octet-stream"));
    // aEle.setAttribute('target', '_blank');
    // aEle.setAttribute('download', 'qrcode.png');

    // aEle.click();

    // console.log(canvas.toDataURL("image/png"));

    // alert('111');
    if (/Safari/.test(navigator.userAgent) && !/Chrome/.test(navigator.userAgent)) {
        // window.open(canvas.toDataURL("image/png"));

        window.open('http://www.baidu.com');
    } else {
        aEle = document.createElement('a');

        aEle.setAttribute('href', canvas.toDataURL("image/png"));
        aEle.setAttribute('target', '_blank');

        //支持download的浏览器
        if ('download' in aEle) {
            aEle.setAttribute('download', 'qrcode.png');
        }

        document.body.appendChild(aEle);
        aEle.click();

        document.body.removeChild(aEle);
    }
}

$(function(){
    var eles = $('.bg-qrocde'),
        canvas = document.createElement('canvas'),
        ctx = canvas.getContext('2d'),
        sales_image = new Image(),
        doc_image = new Image(),
        loadDone = 0,
        loadFn = function (evt) {
            loadDone ++;
        };


    sales_image.onload = loadFn;
    doc_image.onload = loadFn;

    sales_image.src = '/static/images/sales_qrcode_bg.jpg';
    doc_image.src = '/static/images/doctor_qrcode_bg.jpg';

    // image.setAttribute("crossOrigin", 'anonymous')
    canvas.setAttribute('width', 420);
    canvas.setAttribute('height', 680);

    var bulidImage = function () {     

        for(var i = 0; i < eles.length; i++){
            ctx.clearRect(0, 0, 420, 680);

            var $item = $(eles[i]),
                $qrcode = $item.siblings('.qrcode'),
                type = $item.data('type');

            if(type == 'sales'){
                ctx.drawImage(sales_image, 0, 0, 420, 680, 0, 0, 420, 680);
            }else{
                ctx.drawImage(doc_image, 0, 0, 420, 680, 0, 0, 420, 680);
            }

            ctx.drawImage($qrcode.get(0), 0, 0, 500, 500, 85, 265, 500, 500);


            $item.prop('src', canvas.toDataURL('image/png'));
        }

        delete canvas;
    }

    function waitImageLoad(){
        // debugger

        if(loadDone == 2){
            bulidImage();
        }else{
            setTimeout(function(){
                waitImageLoad();
            }, 2e2);
        }
    };

    waitImageLoad();




});