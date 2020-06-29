/**
 * admin.js
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Frequently Bought Together Premium
 * @version 1.0.0
 */

jQuery(document).ready(function($) {
    "use strict";

    $( '.yith-wfbt_options a' ).on( 'click', function(){

        var select      = $('#yith_wfbt_default_variation'),
            select_wrap = select.parents('p');

        select_wrap.block({
            message: null,
            overlayCSS: {
                background: '#fff',
                opacity: 0.6
        }});


        $.ajax({
            type: 'POST',
            url: yith_wfbt.ajaxurl,
            data: {
                action     : 'yith_update_variation_list',
                'productID': yith_wfbt.postID
            },
            dataType: 'html',
            success: function( res ){

                // add new content
                select.html( res );
                select_wrap.unblock();
            }
        })
    });

    $('#yith_wfbt_data_option input, #yith_wfbt_data_option select').on('change', function () {
        var t    = $(this),
            deps = $(document).find('[data-deps="'+ this.name +'"]'),
            value;

        if( t.is(':radio' ) && ! t.is(':checked') ) {
            return;
        }

        if( t.is(':checkbox') ){
            value = t.is(':checked') ? 'yes' : 'no';
        } else {
            value = t.val();
        }

        $.each(deps, function(){
            ( $(this).data('value') === value ) ? $(this).show() : $(this).hide();
        })
    }).change();

    $(document).on( 'click', 'input[type="submit"]', function(){
        $( '.yith-wfbt_options a' ).click();
    });
});