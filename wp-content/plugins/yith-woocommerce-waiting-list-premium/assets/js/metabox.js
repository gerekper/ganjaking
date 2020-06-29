/**
 * metabox.js
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Waiting List
 * @version 1.0.0
 */

jQuery(document).ready(function($) {
    "use strict";

    var button = $( document ).find( '.yith-waitlist-send-mail' );

    button.on( 'click', function(e) {
        e.preventDefault();

        var t = $(this),
            product_id = t.data('product_id'),
            wrapper = t.parents('.inside');

        wrapper.block({
            message   : null,
            overlayCSS: {
                background: '#fff no-repeat center',
                opacity   : 0.5,
                cursor    : 'none'
            }
        });

        $.ajax({
            url      : yith_wcwtl_meta.ajaxurl,
            dataType : 'json',
            data     : {
                action : 'yith_waitlist_send_mail',
                product: product_id
            },
            success  : function (data) {

                console.log( data );

                if (data.send) {
                    wrapper.html(data.msg);
                }
                else {
                    t.parents( '.inside' ).find( '.response-message' ).html( data.msg );
                }

                wrapper.unblock();

            }
        });
    });
});