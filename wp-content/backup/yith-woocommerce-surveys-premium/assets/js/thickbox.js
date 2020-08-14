var  yith_resize_thickbox = function( w, h ) {

    w   =   w || 400;
    h   =   h || 350;


    var myWidth = w,
        myHeight = h;


    var tbWindow = jQuery('#TB_window'),
        tbFrame = jQuery('#TB_iframeContent'),
        wpadminbar = jQuery('#wpadminbar'),
        width = jQuery(window).width(),
        height = jQuery(window).height(),

        adminbar_height = 0;

    if (wpadminbar.length) {
        adminbar_height = parseInt(wpadminbar.css('height'), 10);
    }

    var TB_newWidth = ( width < (myWidth + 50)) ? ( width - 50) : myWidth;
    var TB_newHeight = ( height < (myHeight + 45 + adminbar_height)) ? ( height - 45 - adminbar_height) : myHeight;

    tbWindow.css({
        'marginLeft': -(TB_newWidth / 2),
        'marginTop' : -(TB_newHeight / 2),
        'top'       : '50%',
        'width'     : TB_newWidth,
        'height'    : TB_newHeight
    });

    tbFrame.css({
        'padding': '10px',
        'width'  : TB_newWidth - 20,
        'height' : TB_newHeight - 50
    });
}