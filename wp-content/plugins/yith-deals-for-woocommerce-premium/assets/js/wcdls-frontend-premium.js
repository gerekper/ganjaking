jQuery( document ).ready( function ( $ ) {

    var ywcdls_rule_metabox = {
        init                  : function () {
            $( document ).on( 'click', '.yith-wcdls-accept', this.accept_condition );
            $( document ).on( 'click', '.yith-wcdls-decline', this.decline_condition );
            $( window ).on( 'resize yith_wcdls_popup_changed', this.center_popup );
            this.show_offer();
        },

        decline_condition    : function ( event ) {
            offer_id = $( this ).closest( '.yith-wcdls-content' ).data('offer_id');
            event.preventDefault();
            var post_data = {
                id : offer_id,
                action: 'yith_wcdls_decline_offer'
            };
            $.ajax( {
                type   : "POST",
                data   : post_data,
                url    : yith_wcdls.ajaxurl,
                success: function ( response ) {
                    ywcdls_rule_metabox.close_popup($( '.yith-wcdls-popup' ));
                    if(response['offer']) {

                        $('.yith-wcdls-deals-offer').empty();
                        $('.yith-wcdls-deals-offer').html(response['offer']);
                        ywcdls_rule_metabox.show_offer();

                    }
                }
            } );
        },

        accept_condition     : function ( event ) {
            offer_id = $( this ).closest( '.yith-wcdls-content' ).data('offer_id');
            event.preventDefault();
            var post_data = {
                id : offer_id,
                action: 'yith_wcdls_accept_offer'
            };
            $.ajax( {
                type   : "POST",
                data   : post_data,
                url    : yith_wcdls.ajaxurl,
                success: function ( response ) {

                    $( document.body ).trigger( 'update_checkout' );
                    ywcdls_rule_metabox.close_popup($( '.yith-wcdls-popup' ));

                    //Compatibility with Klarna gateway
                    //$('table#kco-totals #kco-page-shipping input[type="radio"], .woocommerce-checkout-review-order-table #shipping_method input[type="radio"]').trigger('change');
                    $('td.product-quantity input[type=number]').trigger('change');
                    $('.pt-right').trigger('click');
                    $( document.body ).trigger( 'yith_ywcdls_after_click_accept_condition' );
                }
            } );
        },

        show_offer : function( event,offer_id ) {
            if (typeof(offer_id) === 'undefined') {
                offer_id = $('.yith-wcdls-content').data('offer_id');
            }
            if(offer_id) {
                var animation = $('.yith-wcdls-content').data('animation');

                if (typeof (animation) === 'undefined') {
                    var post_data = {
                        id: offer_id,
                        action: 'yith_wcdls_show_offer'
                    };
                    $.ajax({
                        type: "POST",
                        data: post_data,
                        url: yith_wcdls.ajaxurl,
                        success: function (response) {
                            animation = response.layout_type;
                        }
                    });
                }
                switch (animation) {

                    case 'inline':
                        content = $('.yith-wcdls-popup');
                        content.addClass('yith-wcdls-popup-show');
                        content.removeClass('yith-wcdls-style');
                        //$('.entry-footer').append(content);
                        break;
                    case 'modal':
                        var popup = $('.yith-wcdls-popup'),
                            overlay = popup.find('.yith-wcdls-overlay'),
                            close = popup.find('.yith-wcdls-close');
                        this.init_popup(popup);
                        break;
                    case 'popover':
                        var popup = $('.yith-wcdls-popup'),
                            overlay = popup.find('.yith-wcdls-overlay'),
                            close = popup.find('.yith-wcdls-close');
                        this.init_popup(popup);
                        break;
                    default:
                }
            }
        },

        close_popup : function( popup ){
            // remove class to html
            $('html').removeClass( 'yith_wcdls_open' );
            // remove class open
            popup.removeClass( 'open' );
            popup.remove();

            $(document).trigger( 'yith_wcdls_popup_after_closing' );
        },
        // center popup function
        center_popup : function ( popup ) {

            popup = $( '.yith-wcdls-popup' ); //prevent error when it's an event
            var t = $('.yith-wcdls-wrapper');
                animation = popup.find('.yith-wcdls-content').data('animation');
                window_w = $(window).width(),
                window_h = $(window).height(),
                width    = ( 'popover' == animation ) ? window_w : ywcdls_rule_metabox.yith_wcdls_size_popup('width')
                height   = ( 'popover' == animation  ) ? window_h : ywcdls_rule_metabox.yith_wcdls_size_popup('height');

            t.css({
                'left' : (( window_w/2 ) - ( width/2 )),
                'top' : ( window_w > 768 ) ?  (( window_h/2 ) - ( height/2 )) : 50,
                'width'     : width + 'px',
                //'height'    : ( window_w > 1366 ) ? height + 'px' : 500 +'px',
                //'height' : 500+'px',
            });
        },

        yith_wcdls_size_popup: function(type) {
            if('width' == type) {

                if( window_h > 700 ) {
                    return ( (window_w/2) > yith_wcdls.popup_size.width ) ? yith_wcdls.popup_size.width : (window_w/2);

                }else {
                    return window_h * 0.70;
                }


            } else {

                return ( ( window_h - 120 ) > yith_wcdls.popup_size.height ) ? yith_wcdls.popup_size.height : ( window_h /2 );
            }
        },

        init_popup : function(popup) {
            $(document).trigger( 'yith_wcdls_popup_before_opening', [ popup ] );

            // position popup
            this.center_popup(popup);

            //scroll
            if( typeof $.fn.perfectScrollbar != 'undefined' ) {
                popup.find('.yith-wcdls-content').perfectScrollbar({
                    suppressScrollX : true
                });
            }

            /*if( yith_wacp.is_mobile ) {
                // add class to html for prevent page scroll on mobile device
                $('html').addClass( 'yith_wcdls_open' );
            }*/
            popup.addClass('open');

            $(document).trigger( 'yith_wacp_popup_after_opening', [ popup ] );
        },

    };


    ywcdls_rule_metabox.init();
    $(document).on('click','.yith-wcdls-overlay',function(){
        ywcdls_rule_metabox.close_popup($( '.yith-wcdls-popup' ));
    });
    $(document).on('click','.yith-wcdls-close',function(){
        $('.yith-wcdls-decline').trigger('click');
        ywcdls_rule_metabox.close_popup($( '.yith-wcdls-popup' ));
    });

} );
