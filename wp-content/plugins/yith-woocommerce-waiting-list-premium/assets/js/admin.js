/**
 * admin.js
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Waiting List
 * @version 1.0.0
 */

jQuery(document).ready(function($) {
    "use strict";

    // replace email single back url
    $( '.wc-admin-breadcrumb a' ).attr( 'href', yith_wcwtl_admin.email_tab );

    if( typeof $.fn.ajaxChosen != 'undefined' ) {
        $('select.ajax_chosen_select_product').ajaxChosen({
            method: 'GET',
            url: yith_wcwtl_admin.ajaxurl,
            dataType: 'json',
            afterTypeDelay: 100,
            minTermLength: 3,
            data: {
                action: 'woocommerce_json_search_products',
                security: yith_wcwtl_admin.security,
                default: ''
            }
        }, function (data) {

            var terms = {};

            $.each(data, function (i, val) {
                terms[i] = val;
            });

            return terms;
        });
    }

    $( '.wp-list-table .action a.send_mail').on( 'click', function(e){
        e.preventDefault();

        var res = confirm( yith_wcwtl_admin.conf_msg ),
            t   = $(this);

        if ( res == true ) {
            window.location.href = t.attr('href');
        }
    });

    // move success message in edit product
    var message = $( document ).find( '#yith-success-message' ),
        wrap_h2 = message.siblings( '.wrap' ).find( 'h2' );

    if( message.length && wrap_h2.length ) {
        message.remove();
        wrap_h2.after( message );
    }
});