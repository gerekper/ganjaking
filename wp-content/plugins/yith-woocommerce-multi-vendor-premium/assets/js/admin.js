(function ($) {

    /* WooCommerce Options Deps */
    $.fn.yith_wpv_option_deps = function( dep, type, disabled_value, readonly ){

        var main_option = $(this),
            disable     = dep != 'all' ? $(dep).parents('tr') : main_option.parents('table').find('tr:not(:first)'),
            get_value   = function( type ){
                if (type == 'checkbox') {
                    return main_option.attr('checked');
                }

                if (type == 'select') {
                    return main_option.val();
                }
            },
            value = get_value( type );



        var disable_opt = function(){
                disable.css('opacity', '0.3');
                disable.css( 'pointer-events', 'none' );
                if( readonly ){
                    disable.attr( 'readonly', 'readonly' );
                }
                $(document).trigger('yith_wcmv_disable_opt');
            },

            enable_opt = function(){
                disable.css('opacity', '1');
                disable.css( 'pointer-events', 'auto' );
                if( readonly ){
                    disable.removeAttr( 'readonly' );
                }
                $(document).trigger('yith_wcmv_enable_opt');
            };

        if (value == disabled_value) {
            disable_opt();
        }

        main_option.on('change', function () {
            value = get_value( type );
            if (value != disabled_value) {
                enable_opt();
            }

            else {
                disable_opt();
            }
        });

        main_option.add( $(dep)).trigger( 'yith_wcmv_after_option_deps', main_option );
    }

    var button = $('#yith_wpv_vendors_skip_review_for_all'),
         $body = $('body');

    button.on('click', function (e) {
        var accept = confirm( yith_vendors.forceSkipMessage );
        if (accept) {
            $.ajax({
                url       : ajaxurl,
                data      : { action: 'wpv_vendors_force_skip_review_option' },
                beforeSend: function () {
                    $('.spinner').toggleClass('yith-visible');
                },
                success   : function (data) {
                    $('.spinner').toggleClass('yith-visible');
                }
            });
        }
    });

    // commission pay
    if( $('body').hasClass( 'toplevel_page_yith_vendor_commissions' ) ){
        $('table.commissions').on( 'click', '.button.pay', function(){
            var t = $(this),
                gateway_name = t.data('gateway'),
                message = yith_vendors.warnPay;
            message = message.replace("%gateway_name%", gateway_name);
            return confirm( message );
        });
    }

    //PayPal Standard Fix
    var paypal_service = $('#payment_gateway'),
        payment_method = $('#payment_method'),
        paypal_deps = function(){
            if( paypal_service.val() == 'standard' ){
                payment_method.val('manual').trigger('change');
            }
        };

    paypal_service.on( 'change', paypal_deps );

    //Vendors options deps
    var vendor_name_style   = $('#yith_wpv_vendor_name_style'),
        vendor_order = $('#yith_wpv_vendors_option_order_management');

    $('#yith_wpv_enable_product_amount').yith_wpv_option_deps( '#yith_wpv_vendors_product_limit', 'checkbox', undefined, false );
    $('#yith_wpv_report_abuse_link').yith_wpv_option_deps( '#yith_wpv_report_abuse_link_text', 'select', 'none', false );
    vendor_name_style.yith_wpv_option_deps( '#yith_vendors_color_name', 'select', 'theme', true );
    vendor_name_style.yith_wpv_option_deps( '#yith_vendors_color_name_hover', 'select', 'theme', true );
    vendor_order.yith_wpv_option_deps( '#yith_wpv_vendors_option_order_refund_synchronization', 'checkbox', undefined, false );
    vendor_order.yith_wpv_option_deps( '#yith_wpv_vendors_option_order_hide_customer', 'checkbox', undefined, false );
    vendor_order.yith_wpv_option_deps( '#yith_wpv_vendors_option_order_hide_payment', 'checkbox', undefined, false );
    vendor_order.yith_wpv_option_deps( '#yith_wpv_vendors_option_order_prevent_resend_email', 'checkbox', undefined, false );
    vendor_order.yith_wpv_option_deps( '#yith_wpv_vendors_option_order_prevent_edit_custom_fields', 'checkbox', undefined, false );
    vendor_order.yith_wpv_option_deps( '#yith_wpv_vendors_option_order_hide_shipping_billing', 'checkbox', undefined, false );
    $('#yith_vendors_show_gravatar_image').yith_wpv_option_deps( '#yith_vendors_gravatar_image_size', 'select', 'disabled', false );
    $('#yith_wpv_vendors_option_editor_management').yith_wpv_option_deps( '#yith_wpv_vendors_option_editor_media', 'checkbox', undefined, false );
    paypal_service.yith_wpv_option_deps( payment_method, 'select', 'standard', true );
    payment_method.yith_wpv_option_deps( '#payment_minimum_withdrawals', 'select', 'manual', true );
    $('#yith_wpv_show_vendor_tab_in_single').yith_wpv_option_deps( '#yith_wpv_vendor_tab_text_text', 'checkbox', undefined, false );
    $('#yith_wcmv_enable_paypal-masspay_gateway').yith_wpv_option_deps( 'all', 'checkbox', undefined, false );
    $('#yith_vendor_remove_vendor_profile_data').yith_wpv_option_deps( '#yith_vendor_delete_vendor_media_profile_data', 'checkbox', undefined, false );
    $('#yith_wpv_vendors_registration_show_paypal_email').yith_wpv_option_deps( '#yith_wpv_vendors_registration_required_paypal_email', 'checkbox', undefined, false );
    $('#yith_vendors_skin_header').yith_wpv_option_deps( '#yith_vendors_skin_hmtl_header_image_format', 'select', 'double-box', true );
    // Vendor taxonomy table
    var tax_table = $( '#the-list');

    var taxonomy_table_col = function( tax_table ) {
        tax_table.find('tr').each( function () {
                var t = $(this),
                    column_enable_sales = t.find('.column-enable_sales mark');

                if( column_enable_sales.hasClass( 'pending' ) ){
                    t.css( 'background-color', '#fef7f1' );
                    t.find( '.check-column').css( 'border-left', '4px solid #d54e21' );
                }

                if( column_enable_sales.hasClass( 'no-owner' ) ){
                    t.css( 'background-color', '#fffbf2' );
                    t.find( '.check-column').css( 'border-left', '4px solid #ffba00' );
                }
            }
        );
    };

    taxonomy_table_col( tax_table );

    // Vendor taxonomy bulk actions
    if( $body.hasClass( 'taxonomy-yith_shop_vendor' ) && typeof yith_vendors != 'undefined' ){
        var bulk_action_1        = $('#bulk-action-selector-top'),
            bulk_action_2        = $('#bulk-action-selector-bottom'),
            action_approve       = '<option value="approve">' + yith_vendors.approve + '</option>',
            action_enable_sales  = '<option value="enable_sales">' + yith_vendors.enable_sales + '</option>',
            action_disable_sales = '<option value="disable_sales">' + yith_vendors.disable_sales + '</option>',
            actions              = new Array( action_approve, action_enable_sales, action_disable_sales );

        for( var id in actions ){
            bulk_action_1.add( bulk_action_2 ).append( actions[ id ] );
        }
    }

    if( $body.hasClass('vendor_limited_access') ){
        //Remove product reviews from product detail page
        if( yith_vendors_caps.reviews == 'no' ){
            if( $body.hasClass('post-type-product') ){
                $('#commentsdiv').remove();
            }
        }

        //Remove order unable caps
        if( $body.hasClass('post-type-shop_order') && ! $body.hasClass('vendor_quote_management') ){
            $( '.wc-order-edit-line-item' ).remove();
            $( '.wc-order-edit-line-item-actions' ).remove();
            $( 'a.delete-order-tax' ).remove();
        }
    }



    // Quick/Bulk product edit
    var inline_tag = $('.inline-edit-tags');
    inline_tag.each( function(){
        var t = $(this);
        if( t.find( '.tax_input_yith_shop_vendor' ).lenght != 0 ) {
            t.remove();
        }
    });

    if( typeof inlineEditPost != 'undefined' ){
        var $wp_inline_edit = inlineEditPost.edit;

        // and then we overwrite the function with our own code
        inlineEditPost.edit = function ( id ) {

            // "call" the original WP edit function
            // we don't want to leave WordPress hanging
            $wp_inline_edit.apply( this, arguments );

            // now we take care of our business

            // get the post ID
            var $post_id = 0;
            if ( typeof( id ) == 'object' ) {
                $post_id = parseInt( this.getId( id ) );
            }

            if ( $post_id > 0 ) {
                // define the edit row
                var $edit_row = $( '#edit-' + $post_id );
                var $post_row = $( '#post-' + $post_id );

                // get the data
                var $vendor_td  = $( '.column-taxonomy-yith_shop_vendor', $post_row ),
                    vendor_id   = $post_row.find('#vendor-product-' + $post_id).data( 'vendor_id' );

                if( typeof vendor_id == 'undefined' ){
                    vendor_id = 0;
                }

                $edit_row.find('#in-vendor-store-' + vendor_id).prop( 'checked', true );
            }
        };
    }

}(jQuery));
