jQuery( document ).ready( function( $ ){
    var cart_redirect_after_add = typeof( wc_add_to_cart_params ) !== 'undefined' ? wc_add_to_cart_params.cart_redirect_after_add : '',
        this_page = window.location.toString();


    $(document).on( 'click', '.add_saveforlater', function( ev ) {
        var t = $( this);

        ev.preventDefault();

        call_ajax_add_to_savelist( t );

        return false;
    } );


    var getUrlParameter = function getUrlParameter(url, sParam) {
        var sPageURL = decodeURIComponent(url.substring(1)),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
    };

    /**
     * Add a product in the wishlist.
     *
     * @param object el
     * @return void
     * @since 1.0.0
     */
    function call_ajax_add_to_savelist( el ) {


        var product_id      =    getUrlParameter( el.prop('search' ),'save_for_later'),
            variation_id    =     getUrlParameter( el.prop('search' ),'variation_id' ),
            el_wrap = $( '.row-' + product_id),
            data = {
                save_for_later  :   product_id,
                variation_id    :   variation_id,
                action: yith_wsfl_l10n.actions.add_to_savelist_action
            };
        

        if( ! is_cookie_enabled() ){
            alert( yith_wsfl_l10n.labels.cookie_disabled );
            return;
        }

        // Block widgets and fragments
        $( '.shop_table.cart, .updating, .cart_totals' ).fadeTo( '400', '0.6' ).block({
            message: null,
            overlayCSS: {
                opacity: 0.6
            }
        });

        $.ajax({
            type: 'POST',
            url: yith_wsfl_l10n.ajax_url,
            data: data,
            dataType: 'json',
            beforeSend: function(){
                el.siblings( '.ajax-loading' ).css( 'visibility', 'visible' );
            },
            complete: function(){
                el.siblings( '.ajax-loading' ).css( 'visibility', 'hidden' );
            },
            success: function( response ) {

                var  response_result     =   response.result,
                    response_message    =   response.message,
                    response_content    =   response.template;

                if( response_result == "true" ) {
                    $( '#yith-wsfl-messages').css('color','green').html( response_message );
                    $('#ywsfl_general_content').replaceWith(response_content);
                    $('body').trigger('added_to_savelist',[product_id,variation_id]);

                }
                else {
                    $('#yith-wsfl-messages').css('color', 'red').html(response_message);
                    $( '.widget_shopping_cart, .updating' ).stop( true ).css( 'opacity', '1' ).unblock();
                    $( '.shop_table.cart' ).load( this_page + ' .shop_table.cart:eq(0) > *', function() {
                        $( '.shop_table.cart' ).stop( true ).css( 'opacity', '1' ).unblock();
                     });

                    $( '.cart_totals' ).load( this_page + ' .cart_totals:eq(0) > *', function() {
                        $( '.cart_totals' ).stop( true ).css( 'opacity', '1' ).unblock();
                    });
                }
            }
        });
    }

    $(document).on('added_to_savelist', 'body', function( ev, product_id, variation_id ){

        ev.preventDefault();
        var data = {
            product_id : product_id,
            variation_id : variation_id,
            action: 'remove_to_cart_after_save_list'
        }

        $.ajax({
            type:   'POST',
            url:    yith_wsfl_l10n.ajax_url,
            data:  data,
            dataType: 'json',
            success: function (response){

                if( response.result )
                {
                    $( '.widget_shopping_cart, .updating' ).stop( true ).css( 'opacity', '1' ).unblock();
                    $( '.shop_table.cart' ).load( this_page + ' .shop_table.cart:eq(0) > *', function() {

                        $( '.shop_table.cart' ).stop( true ).css( 'opacity', '1' ).unblock();

                        $( 'body' ).trigger( 'cart_page_refreshed' );
                    });

                    $( '.cart_totals' ).load( this_page + ' .cart_totals:eq(0) > *', function() {
                        $( '.cart_totals' ).stop( true ).css( 'opacity', '1' ).unblock();
                    });
                }
            }
        })
    } );

    $(document).on( 'adding_to_cart', 'body', function( ev, button, data ){
        var content =   button.closest( '#ywsfl_general_content'),
            row     =   button.closest( 'div.row' );

        if( content.length != 0 ){
            data.remove_to_save_list_after_add_to_cart = row.data( 'row-id' );
            data.variation_id   =   row.data('row-variation-id');
            wc_add_to_cart_params.cart_redirect_after_add = 'yes';

        }

    } );

    $(document).on('added_to_cart', 'body', function(ev, fragments, cart_hash, button) {

        wc_add_to_cart_params.cart_redirect_after_add=cart_redirect_after_add;
        var gen_content= $('#ywsfl_general_content'),
            elements    =   gen_content.data('num-elements');

        if(elements>0)
        {
            var row =    button.closest('div.row'),
                message =   $('#ywsfl_title_save_list h3').html('Saved for later ( '+(elements-1)+' Product )');

            row.remove();

            if( (elements-1)==0)
                gen_content.hide();

        }


    });

    $(document).on( 'click', '.remove_from_savelist', function( ev ){
        var t = $( this );
        ev.preventDefault();


        remove_item_from_savelist( t );

        return false;
    } );

    /**
     * Remove a product from the savelist.
     *
     * @param object el
     * @return void
     * @since 1.0.0
     */
    function remove_item_from_savelist( el ) {
        // Block widgets and fragments


        var product_id      =   el.data( 'product-id' ),
            variation_id    =   el.data( 'variation-id'),
            el_wrap = $( '.row-' + variation_id ),
            data = {
                remove_from_savelist: product_id,
                variation_id    :   variation_id,
                action: 'remove_from_savelist'
            };

        // Block widgets and fragments
        $( '#ywsfl_general_content' ).fadeTo( '400', '0.6' ).block({
            message: null,
            overlayCSS: {
                opacity: 0.6
            }
        });

        if( ! is_cookie_enabled() ){
            alert( yith_wsfl_l10n.labels.cookie_disabled );
            return;
        }


        $.ajax({
            type: 'POST',
            url: yith_wsfl_l10n.ajax_url,
            data: data,
            dataType: 'json',
            success: function( response ) {

                var  response_result     =   response.result,
                    response_message    =   response.message,
                    response_content    =   response.template;

                if( response_result == "true" ) {
                    $( '#yith-wsfl-messages').css('color','green').html( response_message );
                    $( '#ywsfl_general_content' ).stop( true ).css( 'opacity', '1' ).unblock();
                    $('#ywsfl_general_content').replaceWith(response_content);

                }
                else
                    $( '#yith-wsfl-messages').css('color','red').html( response_message );



            }

        });

        $('body').trigger('removed_from_savelist');

    }



    /**
     * Check if cookies are enabled
     *
     * @return bool
     * @since 2.0.0
     */
    function is_cookie_enabled() {
        if (navigator.cookieEnabled) return true;

        // set and read cookie
        document.cookie = "cookietest=1";
        var ret = document.cookie.indexOf("cookietest=") != -1;

        // delete cookie
        document.cookie = "cookietest=1; expires=Thu, 01-Jan-1970 00:00:01 GMT";

        return ret;
    }
});