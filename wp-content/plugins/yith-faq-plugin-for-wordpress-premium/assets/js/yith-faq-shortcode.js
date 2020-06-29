jQuery(function ($) {

    function yith_faq_resize_thickbox() {

        var myWidth = 600,
            myHeight = 600,
            tbWindow = $('#TB_window'),
            tbFrame = $('#TB_iframeContent'),
            wpadminbar = $('#wpadminbar'),
            width = $(window).width(),
            height = $(window).height(),
            adminbar_height = 0;

        if (wpadminbar.length) {
            adminbar_height = parseInt(wpadminbar.css('height'), 10);
        }

        var TB_newWidth = (width < (myWidth + 50)) ? (width - 50) : myWidth;
        var TB_newHeight = (height < (myHeight + 45 + adminbar_height)) ? (height - 45 - adminbar_height) : myHeight;

        tbWindow.css({
            'marginLeft': -(TB_newWidth / 2),
            'marginTop' : -(TB_newHeight / 2),
            'top'       : '50%',
            'width'     : TB_newWidth,
            'height'    : TB_newHeight
        });

        tbFrame.css({
            'width' : TB_newWidth,
            'height': TB_newHeight - 30
        });

    }

    if (window.QTags !== undefined) {

        QTags.addButton('yfwp_shortcode', yfwp_shortcode.title, function () {

            $('#yfwp_shortcode').click()

        });

    }

    $('#yfwp_shortcode').on('click', function () {

        tb_show(yfwp_shortcode.title, yfwp_shortcode.lightbox_url);

        yith_faq_resize_thickbox();

    });

    $(window).resize(function () {

        yith_faq_resize_thickbox();

    });

});
