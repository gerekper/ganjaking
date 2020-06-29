/**
 * wacp-frontend.js
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Added to Cart Popup
 * @version 1.2.1
 */

jQuery(document).ready(function($) {
    "use strict";

    if( typeof yith_wacp == 'undefined' )
        return;

    var PS_instance,
        xhr,
        popup               = $('#yith-wacp-popup'),
        overlay             = popup.find( '.yith-wacp-overlay'),
        close               = popup.find( '.yith-wacp-close'),
        mini_cart_content   = undefined,
        close_popup         = function(){
            // remove class to html
            $('html').removeClass( 'yith_wacp_open' );
            // remove class open
            popup.removeClass( 'open mini_cart' );
            // after 2 sec remove content
            setTimeout(function () {
                PS_instance.destroy();
            }, 1000);

            $(document).trigger( 'yith_wacp_popup_after_closing' );
        },
        // center popup function
        center_popup        = function() {
            var t = popup.find( '.yith-wacp-wrapper'),
                window_w = $(window).width(),
                window_h = $(window).height(),
                width    = ( ( window_w - 60 ) > yith_wacp.popup_size.width ) ? yith_wacp.popup_size.width : ( window_w - 60 ),
                height   = ( ( window_h - 120 ) > yith_wacp.popup_size.height ) ? yith_wacp.popup_size.height : ( window_h - 120 );

            t.css({
                'left' : (( window_w/2 ) - ( width/2 )),
                'top' : (( window_h/2 ) - ( height/2 )),
                'width'     : width + 'px',
                'height'    : height + 'px'
            });
        },
        position_mini_cart  = function(){
            var t           = $('#yith-wacp-mini-cart'),
                window_w    = $(window).width(),
                window_h    = $(window).height(),
                top         = ( ( window_h * ( yith_wacp.mini_cart_position.top / 100 ) ) - t.innerHeight() ),
                left        = ( ( window_w * ( yith_wacp.mini_cart_position.left / 100 ) ) - t.innerWidth() );

                t.css({
                    'left'  : left,
                    'top'   : top
                }).show();
        },
        // function that handle the popup opening
        handle_popup_open   = function( data ) {
            // add content
            var popup_content = popup.find('.yith-wacp-content');
            if( typeof data != 'undefined' )
                popup_content.html( data ); // update content

            // check if popup is still open, if yes, update it.
            if( popup.hasClass('open') ) {

                // update scroll
                if( typeof PerfectScrollbar != 'undefined' ) {
                    PS_instance.update();
                }
                // then scroll to Top
                popup_content.scrollTop(0);
                $(document).trigger( 'yith_wacp_popup_changed', [ popup ] );
            }
            else {
                $(document).trigger( 'yith_wacp_popup_before_opening', [ popup ] );
                // position popup
                center_popup();
                //scroll
                if( typeof PerfectScrollbar != 'undefined' ) {
                    PS_instance = new PerfectScrollbar( '.yith-wacp-content', {
                        suppressScrollX : true
                    });
                }

                if( yith_wacp.is_mobile ) {
                    // add class to html for prevent page scroll on mobile device
                    $('html').addClass( 'yith_wacp_open' );
                }
                popup.addClass('open');

                $(document).trigger( 'yith_wacp_popup_after_opening', [ popup ] );
            }

            return false;
        },
        // function that handle float cart open
        handle_mini_cart_open = function(){
            if( typeof mini_cart_content != 'undefined' ) {
                popup.addClass( 'mini_cart' );
                handle_popup_open( mini_cart_content );
            }
        },
        update_cart_count   = function( num ){
            num > 0 ? $( '#yith-wacp-mini-cart' ).removeClass( 'empty' ) : $( '#yith-wacp-mini-cart' ).addClass( 'empty' );
            if( $('.yith-wacp-mini-cart-count').length ) {
                $('.yith-wacp-mini-cart-count').html(num);
            }
            $(document).trigger('yith_wacp_cart_counter_updated');
        },
        // function to get param from url
        getUrlParameter     = function( sURL, sParam ) {
            var sURLVariables = sURL.split('&'),
                sParameterName,
                i;

            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');

                if (sParameterName[0] === sParam) {
                    return sParameterName[1] === undefined ? true : sParameterName[1];
                }
            }
        },
        waiting_ajax       = function( elem ) {
            elem.block({
                message   : null,
                overlayCSS: {
                    background: '#fff url(' + yith_wacp.loader + ') no-repeat center',
                    opacity   : 0.5,
                    cursor    : 'none'
                }
            });
        },
        handle_add_to_cart_single = function( data, form, button ) {
            $(document).trigger( 'yith_wacp_adding_cart_single' );

            button.addClass('loading')
                .removeClass('added');

            $.ajax({
                url: window.location,
                data: data,
                contentType: false,
                processData: false,
                dataType: 'json',
                type: 'POST',
                success: function( res ) {

                    // add error notice if any
                    if( res.error.length ) {
                        // add mess and scroll to Top
                        form.parents( 'div.product' ).before( res.error );
                        $('body, html').animate({
                            scrollTop: 0
                        }, 500);
                        // gravity form error handler
                        form.find( '.gfield input, .gfield textarea, .gfield select ' ).each( function(){
                            if( ! $(this).val() ) {
                                $(this).closest( '.gfield' ).addClass( 'gfield_error' );
                            }
                        });
                    }
                    else if( res.html ) {

                        handle_popup_open( res.html );
                        mini_cart_content = res.cart_html;
                        $( document.body ).trigger( 'wc_fragment_refresh' ).trigger( 'wacp_single_added_to_cart' );
                        update_cart_count( res.items );
                    };
                },
                complete: function(){
                    // remove disabled from submit button
                    button.removeAttr( 'disabled')
                        .removeClass( 'loading')
                        .addClass('added');
                }
            });
        };

    // ADD TO CART LOOP AND SINGLE PRODUCT
    $('body').on( 'added_to_cart cleverswatch_after_add_to_cart', function( ev, fragmentsJSON, cart_hash, button ){

        if( typeof fragmentsJSON == 'undefined' )
            fragmentsJSON = $.parseJSON( sessionStorage.getItem( wc_cart_fragments_params.fragment_name ) );

        if( typeof fragmentsJSON.yith_wacp_message != 'undefined' ) {
            if( yith_wacp.allow_automatic_popup ){
                handle_popup_open( popup.hasClass( 'mini_cart' ) ? fragmentsJSON.yith_wacp_message_cart : fragmentsJSON.yith_wacp_message );
            }
            mini_cart_content = fragmentsJSON.yith_wacp_message_cart;
            update_cart_count( fragmentsJSON.yith_wacp_cart_items );
        }
    });
    // REQUEST A QUOTE
    $(document).on( 'yith_wwraq_added_successfully', function( ev, response ) {
        if( typeof response.yith_wacp_raq != 'undefined' )
            handle_popup_open( response.yith_wacp_raq );
    });

    // ACTIONS
    // remove from cart ajax
    popup.on( 'click', '.yith-wacp-remove-cart, .item-remove a.remove', function(ev) {
        ev.preventDefault();

        var t     = $(this),
            item_key = t.data('item_key') ? t.data('item_key') : getUrlParameter( t.attr('href'), 'remove_item' ),
            data = {
                action: yith_wacp.actionRemove,
                item_key: item_key,
                context: 'frontend'
            };

        waiting_ajax( t.parents('table') );

        $.ajax({
            url: yith_wacp.ajaxurl.toString().replace( '%%endpoint%%', yith_wacp.actionRemove ),
            data: data,
            dataType: 'json',
            success: function( res ) {

                if( res.html != '' ) {
                    popup.find('.yith-wacp-content').html( res.html );

                    $(document).trigger( 'yith_wacp_popup_changed', [ popup ] );
                    $( document.body ).trigger( 'wc_fragment_refresh' );
                }
                else {
                    $( document.body ).trigger( 'wc_fragment_refresh' );
                    mini_cart_content = undefined;
                    close_popup();
                }
                update_cart_count( res.items );
                mini_cart_content = res.html; // as in this case html is always the cart
            }
        });
    });

    $(document).keydown(function(e) {
        if (e.keyCode == 27) {
            close_popup();
        }
    });


    // update from cart ajax
    popup.on( 'change', 'table.cart-list input.qty', function(ev) {
        ev.preventDefault();

        var t     = $(this),
            data = {
                action: yith_wacp.actionUpdate,
                item_key: t.attr('name').replace( /\[|\]|qty/gi, ''),
                qty: t.val(),
                context: 'frontend'
            };

        waiting_ajax( t.parents('table') );

        $.ajax({
            url: yith_wacp.ajaxurl.toString().replace( '%%endpoint%%', yith_wacp.actionUpdate ),
            data: data,
            dataType: 'json',
            success: function( res ) {
                if( res.html != '' ) {
                    popup.find('.yith-wacp-content').html( res.html );

                    $(document).trigger( 'yith_wacp_popup_changed', [ popup ] );

                    $( document.body ).trigger( 'wc_fragment_refresh' );
                }

                update_cart_count( res.items );
                mini_cart_content = res.html; // as in this case html is always the cart
            }
        });
    });
    // continue shopping
    popup.on( 'click', 'a.continue-shopping', function (e) {
        if( $(this).attr('href') != '#' ) {
            return;
        }
        e.preventDefault();
        close_popup();
    });
    // update raq quote list
    popup.on( 'submit', '#yith-ywraq-form', function(ev){
        ev.preventDefault();

        var t    = $(this),
            form = t.serializeArray();

        // add action
        form.push({ name: "action", value: yith_wacp.actionUpdateRaq }, { name: "context", value: "frontend" } );

        $.ajax({
            url: yith_wacp.ajaxurl.toString().replace( '%%endpoint%%', yith_wacp.actionUpdateRaq ),
            data: $.param( form ),
            dataType: 'json',
            type: 'POST',
            success: function( res ) {
                handle_popup_open( res.yith_wacp_raq );
            }
        });
    });


    // GENERAL ACTION
    overlay.on( 'click', close_popup );
    close.on( 'click', function(ev){
        ev.preventDefault();
        close_popup();
    });

    $( window ).on( 'resize yith_wacp_popup_changed', center_popup );

    /*######################################
     ADD TO CART AJAX IN SINGLE PRODUCT PAGE
    ########################################*/

    $(document).on( 'submit', yith_wacp.form_selectors, function( ev ) {

        var form            = $(this),
            button          = form.find( 'button[type="submit"]:focus'),
            exclude         = form.find( 'input[name="yith-wacp-is-excluded"]' ),
            is_one_click    = form.find( 'input[name="_yith_wocc_one_click"]' ).val() == 'is_one_click',
            data;

        if( typeof wc_cart_fragments_params === 'undefined' || ! yith_wacp.enable_single || $(this).parents('.product-type-external').length === 1 || button.hasClass( 'wcsatt-add-to-subscription-button' )  ) {
            return;
        }

        // check if excluded
        if( exclude.length || is_one_click )
            return;

        ev.preventDefault();

        // Process Form
        var dataForm = new FormData();
        $.each( form.find( "input[type='file']" ), function( i, tag ) {
            $.each( $(tag)[0].files, function( i, file ) {
                dataForm.append( tag.name, file );
            });
        });


        var has_add_to_cart = false;
        data = form.serializeArray();

        $.each( data, function( i, val ) {
            if( val.name == 'add-to-cart' ) {
                has_add_to_cart = true;
            }
            dataForm.append( val.name, val.value );
        });
        dataForm.append( 'context', 'frontend' );
        dataForm.append( 'action', yith_wacp.actionAdd );
        if( ! has_add_to_cart ) {
            dataForm.append('add-to-cart', form.find( 'button[name="add-to-cart"]').val());
        }

        handle_add_to_cart_single( dataForm, form, button );
    });

    $(document).on( 'yith_wacp_popup_after_opening yith_wacp_popup_changed', function() {
        if( typeof $.yith_wccl != 'undefined' && typeof $.fn.wc_variation_form != 'undefined' ) {
            // not initialized
            $(document).find( '.variations_form:not(.initialized)' ).each( function() {
                $(this).wc_variation_form();
            });
            $.yith_wccl();
        }

        // compatibility with lazyload
        if( typeof thb_lazyload != 'undefined' ) {
            thb_lazyload.update();
        }
    });

    /*##########################################
     ADD TO CART FREQUENTLY BOUGHT
     ##########################################*/

    $(document).on( 'submit', '.yith-wfbt-form', function(ev){

        if( typeof yith_wacp.actionAddFBT == 'undefined' ){
            return;
        }

        ev.preventDefault();

        var form            = $(this),
            button          = form.find( 'button[type="submit"]'),
            data            = new FormData(),
            dataForm        = form.serializeArray();

        // Process Form
        $.each( dataForm, function( i, val ) {
            data.append( val.name, val.value );
        });
        data.append( 'context', 'frontend' );
        data.append( 'actionAjax', yith_wacp.actionAddFBT );
        data.append( 'action', 'yith_bought_together' );
        data.append( '_wpnonce', yith_wacp.nonceFBT );


        handle_add_to_cart_single( data, form, button );
    });

    /*###########################################
      HANDLE MINI CART
     ###########################################*/

    if( typeof yith_wacp.actionUpdateMiniCart != 'undefined' && $('#yith-wacp-mini-cart').length ) {
        var update_mini_cart = function () {
            waiting_ajax( $( '#yith-wacp-mini-cart' ) );
            $.ajax({
                url: yith_wacp.ajaxurl.toString().replace( '%%endpoint%%', yith_wacp.actionUpdateMiniCart ),
                data: {
                    action: yith_wacp.actionUpdateMiniCart,
                    context: 'frontend'
                },
                dataType: 'json',
                success: function( res ) {
                    mini_cart_content = res.html;
                    update_cart_count( res.items );
                    $(document).on('click', yith_wacp.open_popup_selectors , handle_mini_cart_open );
                },
                complete: function () {
                    $( '#yith-wacp-mini-cart' ).unblock();
                }
            });
        };

        $( window ).on( 'resize', position_mini_cart );

        position_mini_cart();
        update_mini_cart();
    }
});