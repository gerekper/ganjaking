/**
 * General admin panel handling
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Mailchimp
 * @version 1.0.0
 */

jQuery( document ).ready( function( $ ){
    var list_select = $( '#yith_wcmc_mailchimp_list, #yith_wcmc_shortcode_mailchimp_list, #yith_wcmc_widget_mailchimp_list, #yith_wcmc_export_list, #yith_wcmc_ecommerce360_list, #yith_wcmc_register_mailchimp_list' ),
        group_select = $( '#yith_wcmc_mailchimp_groups, #yith_wcmc_shortcode_mailchimp_groups, #yith_wcmc_shortcode_mailchimp_groups_selectable, #yith_wcmc_widget_mailchimp_groups, #yith_wcmc_widget_mailchimp_groups_selectable, #yith_wcmc_register_mailchimp_groups' ),
        field_select = $( '#yith_wcmc_export_field_waiting_products' );

    // add updater button
    list_select.parent().find('.description').before( $( '<a/>', { class: 'button button-secondary ajax-mailchimp-updater ajax-mailchimp-updater-list', id: 'yith_wmcm_mailchimp_list_updater', href: '#', text: yith_wcmc.labels.update_list_button } ) );
    group_select.parent().find('.description').before( $( '<a/>', { class: 'button button-secondary ajax-mailchimp-updater ajax-mailchimp-updater-group', id: 'yith_wcmc_mailchimp_group_updater', href: '#', text: yith_wcmc.labels.update_group_button } ) );
    field_select.parent().find('.description').before( $( '<a/>', { class: 'button button-secondary ajax-mailchimp-updater ajax-mailchimp-updater-field', id: 'yith_wcmc_mailchimp_field_updater', href: '#', text: yith_wcmc.labels.update_field_button } ) );

    var handle_lists = function( ev ){
            var t = $(this),
                list = t.prev( 'select'),
                selected_option = list.find( 'option:selected' ).val();

            ev.preventDefault();

            $.ajax({
                beforeSend: function(){
                    t.block({
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }
                    });


                },
                complete: function(){
                    t.unblock();
                },
                data: {
                    force_update: true,
                    action: yith_wcmc.actions.retrieve_lists_via_ajax_action,
                    yith_wcmc_ajax_request_nonce: yith_wcmc.ajax_request_nonce
                },
                dataType: 'json',
                method: 'POST',
                success: function( lists ){
                    var new_options = '',
                        i = 0;

                    if( lists.length != 0 ){
                        for( i in lists ){
                            new_options += '<option value="' + i + '" ' + ( ( selected_option == i ) ? 'selected="selected"' : '' ) + ' >' + lists[i] + '</option>';
                        }
                    }

                    list.html( new_options );

                    if( new_options.length == 0 ){
                        list.attr( 'disabled', 'disabled' );
                    }
                    else{
                        list.removeAttr( 'disabled' );
                    }

                },
                url: ajaxurl
            });
        },
        handle_groups = function( ev ){
            var t = $( this).hasClass( 'ajax-mailchimp-updater-group' ) ? $(this).parent().find( 'select' ) : $(this).parents('tr').next().find('select'),
                row = t.closest( 'td'),
                list_id = t.closest('tr').siblings().find('.list-select').find( 'option:selected' ).val(),
                selected_options_dom = t.find( 'option:selected'),
                selected_options = [];

            selected_options_dom.each( function( i, v ){
                selected_options[i] = $(v).val();
            } );

            ev.preventDefault();

            if( typeof list_id != 'undefined' && list_id.length == 0 ){
                t.prop( 'disabled' );
            }
            else{
                t.removeProp( 'disabled' );
            }

            $.ajax({
                beforeSend: function(){
                    row.block({
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }
                    });
                },
                complete: function(){
                    row.unblock();
                },
                data: {
                    list: list_id,
                    force_update: true,
                    action: yith_wcmc.actions.retrieve_groups_via_ajax_action,
                    yith_wcmc_ajax_request_nonce: yith_wcmc.ajax_request_nonce
                },
                dataType: 'json',
                method: 'POST',
                success: function( groups ){
                    var new_options = '',
                        i = 0;

                    if( groups.length != 0 ){
                        for( i in groups ){
                            new_options += '<option value="' + i + '" ' + ( ( $.inArray( i, selected_options ) > -1 ) ? 'selected="selected"' : '' ) + ' >' + groups[i] + '</option>';
                        }
                    }

                    t.html( new_options );

                    if( new_options.length == 0 ){
                        t.attr( 'disabled', 'disabled' );
                    }
                    else{
                        t.removeAttr( 'disabled', 'disabled' );
                    }

                    t.select2();
                },
                url: ajaxurl
            });
        },
        handle_fields = function( ev ){
            var t = $( this).hasClass( 'ajax-mailchimp-updater-field' ) ? $(this).parent().find( 'select' ) : $(this).parents('tr').next().find('select'),
                row = t.closest( 'td'),
                list_id = t.closest('tr').siblings().find('.list-select').find( 'option:selected' ).val(),
                selected_options_dom = t.find( 'option:selected'),
                selected_options = [];

            selected_options_dom.each( function( i, v ){
                selected_options[i] = $(v).val();
            } );

            ev.preventDefault();

            if( list_id.length == 0 ){
                t.prop( 'disabled' );
            }
            else{
                t.removeProp( 'disabled' );
            }

            $.ajax({
                beforeSend: function(){
                    row.block({
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }
                    });
                },
                complete: function(){
                    row.unblock();
                },
                data: {
                    list: list_id,
                    force_update: true,
                    action: yith_wcmc.actions.retrieve_fields_via_ajax_action,
                    yith_wcmc_ajax_request_nonce: yith_wcmc.ajax_request_nonce
                },
                dataType: 'json',
                method: 'POST',
                success: function( fields ){
                    var new_options = '',
                        i = 0;

                    if( fields.length != 0 ){
                        for( i in fields ){
                            new_options += '<option value="' + i + '" ' + ( ( $.inArray( i, selected_options ) > -1 ) ? 'selected="selected"' : '' ) + ' >' + fields[i]+ '</option>';
                        }
                    }

                    t.html( new_options );

                    if( new_options.length == 0 ){
                        t.prop( 'disabled' );
                    }
                    else{
                        t.removeProp( 'disabled' );
                    }

                    t.select2();
                },
                url: ajaxurl
            });
        },
        add_updater_functions = function(){
            $( document ).off( 'click', '.ajax-mailchimp-updater-list' );
            $( document ).off( 'click', '.ajax-mailchimp-updater-group' );
            $( document ).off( 'click', '.ajax-mailchimp-updater-field' );
            $( document ).off( 'change', '.list-select' );

            // add updater button handler
            $( document ).on( 'click', '.ajax-mailchimp-updater-list', handle_lists );
            $( document ).on( 'click', '.ajax-mailchimp-updater-group', handle_groups );
            $( document ).on( 'click', '.ajax-mailchimp-updater-field', handle_fields );
            $( document ).on( 'change', '.list-select', function(){
                var t = $(this).parents().find('.ajax-mailchimp-updater-group').click();
                var t = $(this).parents().find('.ajax-mailchimp-updater-field').click();
            } );
        },
        update_save_buttons = function(){
            if( $('#yith_wcmc_store_delete_store').length ){
                $('#plugin-fw-wc-reset').hide();
                $('#plugin-fw-wc').find('input[type="submit"]').hide();
            }

            if( $('#yith_wcmc_store_integration_list').length ){
                $('#plugin-fw-wc-reset').hide();
                $('#plugin-fw-wc').find('input[type="submit"]').val( yith_wcmc.labels.connect_store );
            }
        },
        handle_send_requet_buttons = function(){
            $('#yith_wcmc_panel_store').on( 'click', '.send-request', function(){
                var t = $(this),
                    target = t.data('url');

                console.log( target );

                window.location.href = target;
            } )
        };

    update_save_buttons();
    handle_send_requet_buttons();
    add_updater_functions();
    $( 'body').on( 'add_updater_handler', add_updater_functions );

    // disconnect store button
    $( '#yith_wcmc_store_delete_store' ).on( 'click', function(ev){
        var t = $(this);

        ev.preventDefault();

        if( window.confirm( yith_wcmc.labels.confirm_store_delete ) ){
            $.ajax( {
                beforeSend: function(){
                    t.block({
                        message: null,
                        overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                        }
                    });
                },
                data: {
                    action: yith_wcmc.actions.disconnect_store_via_ajax_action,
                    yith_wcmc_ajax_request_nonce: yith_wcmc.ajax_request_nonce
                },
                success: function(){
                    window.location.reload();
                },
                url: ajaxurl
            } );
        }
    } );

    // add dependencies handler
    $( '#yith_wcmc_checkout_trigger').on( 'change', function(){
        var t = $(this),
            subscription_checkbox = $( '#yith_wcmc_subscription_checkbox'),
            double_optin = $( '#yith_wcmc_double_optin' );

        if( t.val() != 'never' ){
            subscription_checkbox.parents( 'tr' ).show();
            double_optin.parents( 'tr').show();
            $( '#yith_wcmc_email_type').parents( 'tr').show();
            $( '#yith_wcmc_subscription_checkbox_label' ).parents( 'tr' ).show();
            $( '#yith_wcmc_subscription_checkbox_position' ).parents( 'tr' ).show();
            $( '#yith_wcmc_subscription_checkbox_default' ).parents( 'tr' ).show();
            $( '#yith_wcmc_update_existing').parents( 'tr').show();
            $( '#yith_wcmc_replace_interests').parents( 'tr').show();
            $( '#yith_wcmc_send_welcome').parents( 'tr').show();

            subscription_checkbox.change();
            double_optin.change();
        }
        else{
            subscription_checkbox.parents( 'tr' ).hide();
            double_optin.parents( 'tr').hide();
            $( '#yith_wcmc_email_type').parents( 'tr').hide();
            $( '#yith_wcmc_subscription_checkbox_label' ).parents( 'tr' ).hide();
            $( '#yith_wcmc_subscription_checkbox_position' ).parents( 'tr' ).hide();
            $( '#yith_wcmc_subscription_checkbox_default' ).parents( 'tr' ).hide();
            $( '#yith_wcmc_update_existing').parents( 'tr').hide();
            $( '#yith_wcmc_replace_interests').parents( 'tr').hide();
            $( '#yith_wcmc_send_welcome').parents( 'tr').hide();
        }
    }).change();
    $( '#yith_wcmc_ecommerce360_enable').on( 'change', function(){
        var t = $(this),
            cookie_lifetime = $( '#yith_wcmc_ecommerce360_cookie_lifetime'),
            list = $( '#yith_wcmc_ecommerce360_list');

        if( t.is(':checked') ){
            cookie_lifetime.parents( 'tr').show();
            list.parents( 'tr').show();
        }
        else{
            cookie_lifetime.parents( 'tr').hide();
            list.parents( 'tr').hide();
        }
    }).change();

    $( '#yith_wcmc_subscription_checkbox' ).on( 'change', function(){
        var t = $(this);

        if( ! t.is(':visible') ){
            return;
        }

        if( t.is( ':checked' ) ){
            $( '#yith_wcmc_subscription_checkbox_label' ).parents( 'tr' ).show();
            $( '#yith_wcmc_subscription_checkbox_position' ).parents( 'tr' ).show();
            $( '#yith_wcmc_subscription_checkbox_default' ).parents( 'tr' ).show();
        }
        else{
            $( '#yith_wcmc_subscription_checkbox_label' ).parents( 'tr' ).hide();
            $( '#yith_wcmc_subscription_checkbox_position' ).parents( 'tr' ).hide();
            $( '#yith_wcmc_subscription_checkbox_default' ).parents( 'tr' ).hide();
        }
    }).change();
    $( '#yith_wcmc_double_optin').on( 'change', function(){
        var t = $(this);

        if( ! t.is(':visible') ){
            return;
        }

        if( t.is( ':checked' ) ) {
            $( '#yith_wcmc_send_welcome').parents( 'tr').hide();
        }
        else{
            $( '#yith_wcmc_send_welcome').parents( 'tr').show();
        }
    }).change();
    $( '#yith_wcmc_shortcode_double_optin').on( 'change', function(){
        var t = $(this);

        if( ! t.is(':visible') ){
            return;
        }

        if( t.is( ':checked' ) ) {
            $( '#yith_wcmc_shortcode_send_welcome').parents( 'tr').hide();
        }
        else{
            $( '#yith_wcmc_shortcode_send_welcome').parents( 'tr').show();
        }
    }).change();
    $( '#yith_wcmc_widget_double_optin').on( 'change', function(){
        var t = $(this);

        if( ! t.is(':visible') ){
            return;
        }

        if( t.is( ':checked' ) ) {
            $( '#yith_wcmc_widget_send_welcome').parents( 'tr').hide();
        }
        else{
            $( '#yith_wcmc_widget_send_welcome').parents( 'tr').show();
        }
    }).change();
} );