jQuery(document).ready(function($) {
    "use strict";

    function preload_popup() {
        $('body').append( '<div id="yith-wcmap-avatar-form-overlay" class="loading"></div>' );
    }

    function center_popup() {
        var p = $( '#yith-wcmap-avatar-form');

        if( ! p.length ) {
            return false;
        }

        var window_w = $(window).width(),
            window_h = $(window).height(),
            o_width  = p.data('width'),
            o_height = p.data('height'),
            width    = ( ( window_w - 60 ) > o_width ) ? o_width : ( window_w - 60 ),
            height   = ( ( window_h - 120 ) > o_height ) ? o_height : ( window_h - 120 );

        p.css({
            'left' : (( window_w/2 ) - ( width/2 )),
            'top' : (( window_h/2 ) - ( height/2 )),
            'width'     : width + 'px',
            'height'    : height + 'px'
        });
    }

    function close_popup() {
        $( '#yith-wcmap-avatar-form, #yith-wcmap-avatar-form-overlay' ).fadeOut('slow', function(){
            $(this).remove();
        });
    }

    $(window).on( 'resize', center_popup );

    $('#load-avatar').click( function (ev) {
        ev.preventDefault();

        preload_popup();

        $.ajax({
            url: yith_wcmap.ajaxurl.toString().replace( '%%endpoint%%', yith_wcmap.actionPrint ),
            data: {},
            dataType: 'html',
            success: function( res ) {

                $('body').append( res ).find('#yith-wcmap-avatar-form-overlay').removeClass('loading');
                center_popup();

                $('#yith-wcmap-avatar-form-overlay, i.close-form').click(function(){
                    close_popup();
                })
            }
        })
        
    });

    $(document).on( "click", ".group-opener", function(ev){
        ev.preventDefault();

        var container = $(this).closest('li');

        if( container.hasClass( 'is-tab' ) && $(window).width() >= 480 ) {
            container.toggleClass( 'is-hover' );
            return;
        }

        $(this).find('i.opener').toggleClass( 'fa-chevron-down' ).toggleClass( 'fa-chevron-up' );
        $(this).next('.myaccount-submenu').slideToggle();
    })
});