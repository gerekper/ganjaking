/*Select2 for ajax category search*/

var  ywcca_resize_thickbox = function( w, h ) {

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
jQuery( document ).ready( function( $ ) {
    
    $(document).on('widget-added widget-updated', function (e, widget) {

        var input_hidden = widget.find('.wc-product-search.enhanced');
        input_hidden.removeClass('enhanced');

        $(document.body).trigger('wc-enhanced-select-init');
        

    });

  



    var style1_count_select      =   $('#ywcca_style_1_count'),
        style1_rect_bg           =   $('#ywcca_style1_back_rect_count'),
        style1_rect_bd           =   $('#ywcca_style1_border_rect_count'),
        style1_round_bg          =   $('#ywcca_style1_back_round_count'),
        style1_round_bd           =   $('#ywcca_style1_border_round_count'),

        style2_count_select      =   $('#ywcca_style_2_count'),
        style2_rect_bg           =   $('#ywcca_style2_back_rect_count'),
        style2_rect_bd           =   $('#ywcca_style2_border_rect_count'),
        style2_round_bg          =   $('#ywcca_style2_back_round_count'),
        style2_round_bd          =   $('#ywcca_style2_border_round_count'),

        style3_count_select      =   $('#ywcca_style_3_count'),
        style3_rect_bg           =   $('#ywcca_style3_back_rect_count'),
        style3_rect_bd           =   $('#ywcca_style3_border_rect_count'),
        style3_round_bg          =   $('#ywcca_style3_back_round_count'),
        style3_round_bd          =   $('#ywcca_style3_border_round_count'),

        style4_count_select      =   $('#ywcca_style_4_count'),
        style4_rect_bg           =   $('#ywcca_style4_back_rect_count'),
        style4_rect_bd           =   $('#ywcca_style4_border_rect_count'),
        style4_round_bg          =   $('#ywcca_style4_back_round_count'),
        style4_round_bd          =   $('#ywcca_style4_border_round_count');

        style1_rect_bg.parents( 'tr' ).hide();
        style1_rect_bd.parents( 'tr' ).hide();
        style1_round_bg.parents( 'tr').hide();
        style1_round_bd.parents( 'tr').hide();

        style2_rect_bg.parents( 'tr' ).hide();
        style2_rect_bd.parents( 'tr' ).hide();
        style2_round_bg.parents( 'tr').hide();
        style2_round_bd.parents( 'tr').hide();

        style3_rect_bg.parents( 'tr' ).hide();
        style3_rect_bd.parents( 'tr' ).hide();
        style3_round_bg.parents( 'tr').hide();
        style3_round_bd.parents( 'tr').hide();

        style4_rect_bg.parents( 'tr' ).hide();
        style4_rect_bd.parents( 'tr' ).hide();
        style4_round_bg.parents( 'tr').hide();
        style4_round_bd.parents( 'tr').hide();


        if( style1_count_select.val() == 'rect' ){
            style1_rect_bg.parents( 'tr' ).show();
            style1_rect_bd.parents( 'tr' ).show();
        }
        else if( style1_count_select.val() == 'round' ){
            style1_round_bg.parents( 'tr').show();
            style1_round_bd.parents( 'tr').show();
        }

        if( style2_count_select.val() == 'rect' ){
            style2_rect_bg.parents( 'tr' ).show();
            style2_rect_bd.parents( 'tr' ).show();
        }
        else if( style2_count_select.val() == 'round' ){
            style2_round_bg.parents( 'tr').show();
            style2_round_bd.parents( 'tr').show();
        }

        if( style3_count_select.val() == 'rect' ){
            style3_rect_bg.parents( 'tr' ).show();
            style3_rect_bd.parents( 'tr' ).show();
        }
        else if( style3_count_select.val() == 'round' ){
            style3_round_bg.parents( 'tr').show();
            style3_round_bd.parents( 'tr').show();
        }
        if( style4_count_select.val() == 'rect' ){
            style4_rect_bg.parents( 'tr' ).show();
            style4_rect_bd.parents( 'tr' ).show();
        }
        else if( style4_count_select.val() == 'round' ){
            style4_round_bg.parents( 'tr').show();
            style4_round_bd.parents( 'tr').show();
        }


        style1_count_select.on( 'change', function(){

            var t=  $(this);

            if( t.val() =='rect'){
                style1_rect_bg.parents( 'tr' ).show();
                style1_rect_bd.parents( 'tr' ).show();
                style1_round_bg.parents( 'tr').hide();
                style1_round_bd.parents( 'tr').hide();
            }
            else if (t.val() == 'round' ){
                style1_rect_bg.parents( 'tr' ).hide();
                style1_rect_bd.parents( 'tr' ).hide();
                style1_round_bg.parents( 'tr').show();
                style1_round_bd.parents( 'tr').show();
            }
            else
            {
                style1_rect_bg.parents( 'tr' ).hide();
                style1_rect_bd.parents( 'tr' ).hide();
                style1_round_bg.parents( 'tr').hide();
                style1_round_bd.parents( 'tr').hide();
            }

        } );

        style2_count_select.on( 'change', function(){

            var t=  $(this);

            if( t.val() =='rect'){
                style2_rect_bg.parents( 'tr' ).show();
                style2_rect_bd.parents( 'tr' ).show();
                style2_round_bg.parents( 'tr').hide();
                style2_round_bd.parents( 'tr').hide();
            }
            else if (t.val() == 'round' ){
                style2_rect_bg.parents( 'tr' ).hide();
                style2_rect_bd.parents( 'tr' ).hide();
                style2_round_bg.parents( 'tr').show();
                style2_round_bd.parents( 'tr').show();
            }
            else
            {
                style2_rect_bg.parents( 'tr' ).hide();
                style2_rect_bd.parents( 'tr' ).hide();
                style2_round_bg.parents( 'tr').hide();
                style2_round_bd.parents( 'tr').hide();
            }

        } );

        style3_count_select.on( 'change', function(){

            var t=  $(this);

            if( t.val() =='rect'){
                style3_rect_bg.parents( 'tr' ).show();
                style3_rect_bd.parents( 'tr' ).show();
                style3_round_bg.parents( 'tr').hide();
                style3_round_bd.parents( 'tr').hide();
            }
            else if (t.val() == 'round' ){
                style3_rect_bg.parents( 'tr' ).hide();
                style3_rect_bd.parents( 'tr' ).hide();
                style3_round_bg.parents( 'tr').show();
                style3_round_bd.parents( 'tr').show();
            }
            else
            {
                style3_rect_bg.parents( 'tr' ).hide();
                style3_rect_bd.parents( 'tr' ).hide();
                style3_round_bg.parents( 'tr').hide();
                style3_round_bd.parents( 'tr').hide();
            }

        } );

        style4_count_select.on( 'change', function(){

            var t=  $(this);

            if( t.val() =='rect'){
                style4_rect_bg.parents( 'tr' ).show();
                style4_rect_bd.parents( 'tr' ).show();
                style4_round_bg.parents( 'tr').hide();
                style4_round_bd.parents( 'tr').hide();
            }
            else if (t.val() == 'round' ){
                style4_rect_bg.parents( 'tr' ).hide();
                style4_rect_bd.parents( 'tr' ).hide();
                style4_round_bg.parents( 'tr').show();
                style4_round_bd.parents( 'tr').show();
            }
            else
            {
                style4_rect_bg.parents( 'tr' ).hide();
                style4_rect_bd.parents( 'tr' ).hide();
                style4_round_bg.parents( 'tr').hide();
                style4_round_bd.parents( 'tr').hide();
            }

        } );

});
