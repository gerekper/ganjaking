/**
 * admin.js
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Subscription
 * @version 1.0.0
 */
/* global yith_ywsbs_admin */
jQuery(document).ready( function($) {
    'use strict';

    $('#ywsbs_safe_submit_field').val('');
    var  block_loader    = ( typeof yith_ywsbs_admin !== 'undefined' ) ? yith_ywsbs_admin.block_loader : false;

    /* METABOX CONTENT */

    var load_info = function( t, from, to, force ) {
        var message = ( from == to ) ? 'load_'+ from  : 'copy_billing' ;

        if ( true === force || window.confirm( yith_ywsbs_admin[message] ) ) {
            // Get user ID to load data for
            var user_id = $( '#user_id' ).val();

            if ( user_id == 0 ) {
                window.alert( yith_ywsbs_admin.no_customer_selected );
                return false;
            }

            var data = {
                user_id : user_id,
                action  : 'woocommerce_get_customer_details',
                security: yith_ywsbs_admin.get_customer_details_nonce
            };

            $.ajax({
                url: yith_ywsbs_admin.ajaxurl,
                data: data,
                type: 'POST',
                success: function( response ) {
                    if ( response && response[from] ) {
                        $.each( response[from], function( key, data ) {
                          //  $( ':input#_'+to+'_' + key ).val( data ).change();
                            $( '#_'+to+'_' + key ).val( data ).change();
                        });
                    }

                }
            });
        }
        return false;
    };

    $( document ).on( 'click', '.load_customer_info', function(e){
        e.preventDefault();
        var $t = $(this),
            from = $t.data('from'),
            to = $t.data('to');
        load_info( $t, from, to );
    });



    $(document).on('click', 'a.edit_address', function (e) {
        e.preventDefault();
        var $t = $(this),
            $edit_div = $t.closest('.subscription_data_column').find('div.edit_address'),
            $links = $t.closest('.subscription_data_column').find('a'),
            $show_div = $t.closest('.subscription_data_column').find('div.address');
        $show_div.toggle();
        $links.toggle();
        $edit_div.toggle();
    });

    /* METABOX SCHEDULE */
    if( $.fn.datetimepicker !== undefined ) {
        $(document).find('.ywsbs-timepicker').each( function(){
            $(this).datetimepicker({
                timeFormat: 'HH:mm:00',
                defaultDate    : '',
                dateFormat     : 'yy-mm-dd',
                numberOfMonths : 1,
                showButtonPanel: true
            });
        });
    }

    $('#ywsbs_schedule_subscription_button').on('click', function(e){
        e.preventDefault();
        $("#ywsbs_safe_submit_field").val('schedule_subscription');
       $(this).closest('form').submit();
    });


    var ywsbs_product_meta_boxes = {
        init: function() {
            var content = $(document).find( '#woocommerce-order-items' );
            content.on( 'click', 'a.edit-order-item', this.edit_item );
            content.on( 'click', '.save-action', this.save_items );
            content.on( 'click', '.recalculate-action', this.recalculate );

        },
        edit_item : function(){
                $( this ).closest( 'tr' ).find( '.view' ).hide();
                $( this ).closest( 'tr' ).find( '.edit' ).show();
                $( this ).hide();
                $( '.wc-order-add-item').show();
                $( '.wc-order-recalculate').hide();
                $( 'button.cancel-action' ).attr( 'data-reload', true );
                return false;
        },
        save_items: function(){
            var data = {
                subscription_id: $('#post_ID').val(),
                items:    $( 'table.woocommerce_order_items :input[name], .wc-order-totals-items :input[name]' ).serialize(),
                action:   'ywsbs_save_items',
                security: yith_ywsbs_admin.save_item_nonce
            };

            $.ajax({
                url:  yith_ywsbs_admin.ajaxurl,
                data: data,
                type: 'POST',
                beforeSend: function(){
                    $('#woocommerce-order-items').block({
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }
                    });
                },
                success: function( response ) {
                    $( '#ywsbs-product-subscription' ).find( '.inside' ).empty().append( response );
                    $('#woocommerce-order-items').unblock();
                    ywsbs_product_meta_boxes.init();
                }
            });

            return false;
        },
        recalculate: function(){
            var data = {
                subscription_id: $('#post_ID').val(),
                action:   'ywsbs_recalculate',
                security: yith_ywsbs_admin.recalculate_nonce
            };

            $.ajax({
                url:  yith_ywsbs_admin.ajaxurl,
                data: data,
                type: 'POST',
                beforeSend: function(){
                    $('#woocommerce-order-items').block({
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }
                    });
                },
                success: function( response ) {
                    $( '#ywsbs-product-subscription' ).find( '.inside' ).empty().append( response );
                    $('#woocommerce-order-items').unblock();
                    ywsbs_product_meta_boxes.init();
                }
            });
        }
    };

    ywsbs_product_meta_boxes.init();

});
