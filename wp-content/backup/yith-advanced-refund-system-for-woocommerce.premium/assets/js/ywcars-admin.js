jQuery( function( $ ) {

    $( document ).ready( function ( $ ) {

        var ywcars_items_metabox = {
            init: function () {
                $( document.body ).on( 'click', 'button.ywcars_accept_button', this.do_refund );
                $( document.body ).on( 'click', 'button#ywcars_offer_coupon_button', this.create_coupon );
                $( document.body ).on( 'click', 'button#ywcars_reject_request_button', this.reject );
                $( document.body ).on( 'click', 'button#ywcars_processing_request_button', this.processing );
                $( document.body ).on( 'click', 'button#ywcars_on_hold_request_button', this.on_hold );
                $( document.body ).on( 'click', 'button#ywcars_close_request_button', this.close_request );
                $( document.body ).on( 'click', '#ywcars_submit_message', this.click_submit_message_button );
                $( document.body ).on( 'submit', '#post', this.submit_message );
                $( document.body ).on( 'click', 'a.ywcars_update_messages', this.update_messages );
                $( document.body ).on( 'click', '.ywcars_close_alert', function () { $( this ).closest( 'div.ywcars_alert' ).hide(); } );
                this.check_disabled_buttons();
                this.update_table_refund_total();
                this.update_table_refund_subtotal();
                this.custom_refund();
            },
            update_table_refund_total: function () {
                $( document.body ).on( 'update_refund_total', function () {
                    var $refund_total = $( 'td.ywcars_refund_total_data' );
                    var $price_amount_on_buttons = $( 'button span.amount' );
                    var refund_total  = 0;
                    $( 'input.ywcars_item_total' ).each( function (i) {
                        refund_total += parseFloat( $( this ).val() );
                    } );
                    refund_total = accounting.formatNumber( refund_total, ywcars_params.currency_format_num_decimals, '' );
                    $( '#ywcars_refund_amount' ).val( refund_total );

                    $( this ).trigger( 'check_disabled_buttons', refund_total );

                    refund_total = accounting.formatMoney( refund_total, {
                        symbol:    ywcars_params.currency_format_symbol,
                        decimal:   ywcars_params.currency_format_decimal_sep,
                        thousand:  ywcars_params.currency_format_thousand_sep,
                        precision: ywcars_params.currency_format_num_decimals,
                        format:    ywcars_params.currency_format
                    } );
                    $refund_total.text( refund_total );
                    $price_amount_on_buttons.text( refund_total );
                } );
            },
            update_table_refund_subtotal: function () {
                var $input_number = $( 'input[type="number"]' );
                var $input_cb = $( 'input.ywcars_non_line_item_cb' );
                $input_number.change( function () {
                    $row = $( this ).closest( 'tr' );
                    if ( $( this ).val() > 0 ) {
                        $row.addClass( 'ywcars_items_table_highlight_single' );
                    } else {
                        $row.removeClass( 'ywcars_items_table_highlight_single' );
                    }
                    // If there aren't quantities selected, disable Restock items checkbox
                    var $restock_items_checkbox = $( 'input#ywcars_restock_items' );
                    var $label = $restock_items_checkbox.closest( 'span' ).find( 'label' );
                    if ( ! $( '.ywcars_items_table_highlight_single' ).length ) {
                        $restock_items_checkbox.prop( 'checked', false );
                        $restock_items_checkbox.prop( 'disabled', true );
                        $label.css( 'color', 'gainsboro' );
                    } else {
                        $restock_items_checkbox.prop( 'disabled', false );
                        $label.css( 'color', '#444' );
                    }

                    var $refund_data = $row.find( 'td.ywcars_item_data' );
                    var taxes_enabled = $( '#ywcars_taxes_enabled' ).val();
                    var item_value = parseFloat( $refund_data.find( 'input.ywcars_item_value' ).val() );
                    var tax_value = parseFloat( $refund_data.find( 'input.ywcars_item_tax_value' ).val() );
                    var item_qty = parseInt( $refund_data.find( 'input.ywcars_item_qty' ).val() );
                    var refund_subtotal = taxes_enabled ? ( item_value + tax_value ) * item_qty : item_value * item_qty;
                    $refund_data.find( 'input.ywcars_item_total' ).val( refund_subtotal );

                    var $refund_subtotal = $row.find( 'td.ywcars_refund_subtotal_data' );
                    $refund_subtotal.text( accounting.formatMoney( refund_subtotal, {
                        symbol:    ywcars_params.currency_format_symbol,
                        decimal:   ywcars_params.currency_format_decimal_sep,
                        thousand:  ywcars_params.currency_format_thousand_sep,
                        precision: ywcars_params.currency_format_num_decimals,
                        format:    ywcars_params.currency_format
                    } ) );

                    $( this ).trigger( 'update_refund_total' );

                } ).change();

                $input_cb.change( function() {
                    $row = $( this ).closest( 'tr' );

                    var $refund_data     = $row.find( 'td.ywcars_non_line_item_data' );
                    var taxes_enabled    = $( '#ywcars_taxes_enabled' ).val();
                    var item_value       = parseFloat( $refund_data.find( 'input.ywcars_item_value' ).val() );
                    var tax_value        = parseFloat( $refund_data.find( 'input.ywcars_item_tax_value' ).val() );
                    var $refund_subtotal = $row.find( 'td.ywcars_refund_subtotal_data' );
                    var refund_subtotal  = 0;

                    if ( $( this ).is( ':checked' ) ) {
                        $row.addClass( 'ywcars_items_table_highlight_single' );
                        refund_subtotal  = taxes_enabled ? ( item_value + tax_value ) : item_value;
                        $refund_subtotal.text( accounting.formatMoney( refund_subtotal, {
                            symbol:    ywcars_params.currency_format_symbol,
                            decimal:   ywcars_params.currency_format_decimal_sep,
                            thousand:  ywcars_params.currency_format_thousand_sep,
                            precision: ywcars_params.currency_format_num_decimals,
                            format:    ywcars_params.currency_format
                        } ) );
                    } else {
                        $row.removeClass( 'ywcars_items_table_highlight_single' );
                        refund_subtotal = 0;
                        $refund_subtotal.text( '' );
                    }
                    $refund_data.find( 'input.ywcars_item_total' ).val( refund_subtotal );

                    $( this ).trigger( 'update_refund_total' );

                } ).change();

                var $restock_items_checkbox = $( 'input#ywcars_restock_items' );
                $restock_items_checkbox.prop( 'checked', true );
            },
            custom_refund: function () {
                var $custom_refund_text = $( '.ywcars_custom_refund_amount' );
                var $price_amount_on_buttons = $( 'button span.amount' );
                $custom_refund_text.on( 'keyup', function ( e ) {
                    var $refund_total = $( 'td.ywcars_refund_total_data' );
                    var refund_total = accounting.unformat( $custom_refund_text.val(), ywcars_params.mon_decimal_point );
                    refund_total = accounting.formatNumber( refund_total, ywcars_params.currency_format_num_decimals, '' );
                    $( this ).trigger( 'check_disabled_buttons', refund_total );
                    $( '#ywcars_refund_amount' ).val( refund_total );

                    var money_formatted = accounting.formatMoney( refund_total, {
                        symbol:    ywcars_params.currency_format_symbol,
                        decimal:   ywcars_params.currency_format_decimal_sep,
                        thousand:  ywcars_params.currency_format_thousand_sep,
                        precision: ywcars_params.currency_format_num_decimals,
                        format:    ywcars_params.currency_format
                    } );

                    $price_amount_on_buttons.text( money_formatted );
                    $refund_total.text( money_formatted );
                } );
            },
            check_disabled_buttons: function () {
                $( document.body ).on( 'check_disabled_buttons', function ( event, refund_total ) {
                    var $action_buttons    = $( 'button.ywcars_request_action_button' );
                    var $api_button        = $( '#ywcars_api_refund_button' );
                    var $coupon_button     = $( '#ywcars_offer_coupon_button' );
                    var $reject_button     = $( 'button#ywcars_reject_request_button' );
                    var $processing_button = $( 'button#ywcars_processing_request_button' );
                    var $on_hold_button    = $( 'button#ywcars_on_hold_request_button' );
                    var request_is_closed  = !! $( 'input#ywcars_request_is_closed' ).val();
                    var request_status     = $( '#ywcars_request_status' ).val();
                    var order_total        = parseFloat( $( 'input#ywcars_order_total' ).val() );

                    if ( request_is_closed || refund_total <= 0 || refund_total > order_total ) {
                        $action_buttons.prop( 'disabled', true );
                    } else {
                        $action_buttons.prop( 'disabled', false );
                        if( $api_button.hasClass( 'disabled' ) ) {
                            $api_button.prop( 'disabled', true );
                        }
                    }
                    if ( 'ywcars-coupon' == request_status ) {
                        $coupon_button.prop( 'disabled', true );
                    }
                    if ( 'ywcars-rejected' == request_status ) {
                        $reject_button.prop( 'disabled', true );
                    }
                    if ( 'ywcars-processing' == request_status ) {
                        $processing_button.prop( 'disabled', true );
                    }
                    if ( 'ywcars-on-hold' == request_status ) {
                        $on_hold_button.prop( 'disabled', true );
                    }
                } );

                $( document.body ).trigger( 'check_disabled_buttons' );
            },
            do_refund: function( e ) {
                e.preventDefault();
                ywcars_items_metabox.block();

                if ( window.confirm( ywcars_params.i18n_do_refund ) ) {
                    var request_id      = $( 'input#ywcars_request_id' ).val();
                    var order_id        = $( 'input#ywcars_order_id' ).val();
                    var refund_amount   = $( 'input#ywcars_refund_amount' ).val();
                    var refunded_amount = $( 'input#refunded_amount' ).val();

                    // Get line item refunds
                    var line_item_qtys       = {};
                    var line_item_totals     = {};
                    var line_item_tax_totals = {};

                    $( 'tr.ywcars_items_table_highlight_single td.ywcars_item_data, tr.ywcars_items_table_highlight_single td.ywcars_non_line_item_data' ).each( function( index, item ) {
                        item_id    = $( item ).find( '.ywcars_item_id' ).val();
                        item_qty   = parseInt( $( item ).find( '.ywcars_item_qty' ).val() );
                        item_value = parseFloat( $( item ).find( '.ywcars_item_value' ).val() );
                        item_total = item_qty ? item_value * item_qty : item_value;

                        if ( item_qty ) {
                            line_item_qtys[ item_id ] = item_qty;
                        }
                        line_item_totals[ item_id ]      = accounting.unformat( item_total, ywcars_params.mon_decimal_point );
                        line_item_tax_totals [ item_id ] = {};

                        item_taxes = $( item ).find( '.ywcars_item_tax' ).each( function ( index, tax ) {
                            var tax_id = $( tax ).data( 'tax_id' );
                            var selected_qty_tax_value = item_qty ? parseFloat( tax.value ) * item_qty : parseFloat( tax.value );
                            line_item_tax_totals [ item_id ][ tax_id ] = accounting.unformat( selected_qty_tax_value, ywcars_params.mon_decimal_point );
                        } );

                    });

                    var data = {
                        action                : 'woocommerce_refund_line_items',
                        ywcars_request_id     : request_id,
                        order_id              : order_id,
                        refund_amount         : refund_amount,
                        refunded_amount       : refunded_amount,
                        refund_reason         : '',
                        line_item_qtys        : JSON.stringify( line_item_qtys, null, '' ),
                        line_item_totals      : JSON.stringify( line_item_totals, null, '' ),
                        line_item_tax_totals  : JSON.stringify( line_item_tax_totals, null, '' ),
                        api_refund            : $( this ).is( '.do-api-refund' ),
                        restock_refunded_items: $( '#ywcars_restock_items:checked' ).length ? 'true' : 'false',
                        security              : ywcars_params.order_item_nonce
                    };

                    $.post( ywcars_params.ajax_url, data, function( response ) {
                        if ( true === response.success ) {
                            ywcars_items_metabox.unblock();
                            window.location.reload();
                        } else {
                            window.alert( response.data.error );
                            ywcars_items_metabox.unblock();
                        }
                    });
                } else {
                    ywcars_items_metabox.unblock();
                }
            },
            create_coupon: function ( e ) {
                e.preventDefault();
                ywcars_items_metabox.block();

                var request_id = $( 'input#ywcars_request_id' ).val();
                var amount = $( 'input#ywcars_refund_amount' ).val();
                var money_formatted = accounting.formatMoney( amount, {
                    symbol:    ywcars_params.currency_format_symbol,
                    decimal:   ywcars_params.currency_format_decimal_sep,
                    thousand:  ywcars_params.currency_format_thousand_sep,
                    precision: ywcars_params.currency_format_num_decimals,
                    format:    ywcars_params.currency_format
                } );

                if ( window.confirm( ywcars_params.create_coupon + ' ' + money_formatted ) ) {
                    var data = {
                        action:            'ywcars_create_coupon',
                        ywcars_request_id: request_id,
                        amount:            amount,
                        security:          ywcars_params.create_coupon_nonce
                    };

                    $.post( ywcars_params.ajax_url, data, function( response ) {
                        if ( true === response.success ) {
                            ywcars_items_metabox.unblock();
                            window.location.href = window.location.href;
                        } else {
                            window.alert( response.data.error );
                            ywcars_items_metabox.unblock();
                        }
                    });
                } else {
                    ywcars_items_metabox.unblock();
                }
            },
            reject: function ( e ) {
                e.preventDefault();
                ywcars_items_metabox.block();

                var request_id = $( 'input#ywcars_request_id' ).val();

                if ( window.confirm( ywcars_params.reject ) ) {
                    var data = {
                        action:            'ywcars_change_status',
                        ywcars_request_id: request_id,
                        ywcars_status:     'ywcars-rejected',
                        security:          ywcars_params.change_status_nonce
                    };

                    $.post( ywcars_params.ajax_url, data, function( response ) {
                        if ( true === response.success ) {
                            ywcars_items_metabox.unblock();
                            window.location.href = window.location.href;
                        } else {
                            window.alert( 'Error: ' + response.data.error );
                            ywcars_items_metabox.unblock();
                        }
                    });
                } else {
                    ywcars_items_metabox.unblock();
                }
            },
            processing: function ( e ) {
                e.preventDefault();
                ywcars_items_metabox.block();

                var request_id = $( 'input#ywcars_request_id' ).val();

                var data = {
                    action:            'ywcars_change_status',
                    ywcars_request_id: request_id,
                    ywcars_status:     'ywcars-processing',
                    security:          ywcars_params.change_status_nonce
                };

                $.post( ywcars_params.ajax_url, data, function( response ) {
                    if ( true === response.success ) {
                        ywcars_items_metabox.unblock();
                        window.location.href = window.location.href;
                    } else {
                        window.alert( 'Error: ' + response.data.error );
                        ywcars_items_metabox.unblock();
                    }
                });
            },
            on_hold: function ( e ) {
                e.preventDefault();
                ywcars_items_metabox.block();

                var request_id = $( 'input#ywcars_request_id' ).val();

                var data = {
                    action:            'ywcars_change_status',
                    ywcars_request_id: request_id,
                    ywcars_status:     'ywcars-on-hold',
                    security:          ywcars_params.change_status_nonce
                };

                $.post( ywcars_params.ajax_url, data, function( response ) {
                    if ( true === response.success ) {
                        ywcars_items_metabox.unblock();
                        window.location.href = window.location.href;
                    } else {
                        window.alert( 'Error: ' + response.data.error );
                        ywcars_items_metabox.unblock();
                    }
                });
            },
            close_request: function ( e ) {
                e.preventDefault();
                ywcars_items_metabox.block();

                var request_id = $( 'input#ywcars_request_id' ).val();

                if ( window.confirm( ywcars_params.close_request ) ) {
                    var data = {
                        action:            'ywcars_change_status',
                        ywcars_request_id: request_id,
                        ywcars_status:     'ywcars-close-request',
                        security:          ywcars_params.change_status_nonce
                    };

                    $.post( ywcars_params.ajax_url, data, function( response ) {
                        if ( true === response.success ) {
                            ywcars_items_metabox.unblock();
                            window.location.href = window.location.href;
                        } else {
                            window.alert( 'Error: ' + response.data.error );
                            ywcars_items_metabox.unblock();
                        }
                    });
                } else {
                    ywcars_items_metabox.unblock();
                }
            },
            click_submit_message_button: function ( e ) {
                e.preventDefault();
                var ywcars_request = true;
                $( '#post' ).trigger( 'submit', ywcars_request );
            },
            submit_message: function ( e ) {
                var $message = $( 'textarea#ywcars_new_message' );
                var request_id = $( 'input#ywcars_request_id' ).val();

                if ( $message.val() && request_id ) {
                    e.preventDefault();
                    form_data = new FormData( this );
                    form_data.append( 'action', 'ywcars_submit_message' );
                    form_data.append( 'security', ywcars_params.ywcars_submit_message );
                    form_data.append( 'request_id', request_id );

                    var settings = {
                        type: 'POST',
                        url: ywcars_params.ajax_url,
                        data: form_data,
                        success: function ( data, textStatus, jqXHR ) {
                            if ( data.success && data.data == 'ywcars_message_submitted_correctly' ) {
                                $( 'div.ywcars_success_alert' ).show().find( 'span.ywcars_alert_content' ).text( ywcars_params.success_message );
                                $message.val('');
                            } else {
                                $( 'div.ywcars_error_alert' ).show().find( 'span.ywcars_alert_content' ).text( data.data );
                            }
                        },
                        error: function ( jqXHR, text_status, error_thrown ) {
                            $( 'div.ywcars_error_alert' ).show().find( 'span.ywcars_alert_content' ).text( error_thrown );
                        },
                        complete: function () {
                            ywcars_items_metabox.unblock_messages();
                            // Update messages list
                            ywcars_items_metabox.update_messages( e );
                        },
                        cache: false,
                        contentType: false,
                        processData: false
                    };

                    ywcars_items_metabox.block_messages( { message: null, overlayCSS:{ background: "#fff", opacity: .6 } } );
                    $.ajax( settings );
                } else {
                    $( 'div.ywcars_error_alert' ).show().find( 'span.ywcars_alert_content' ).text( ywcars_params.fill_fields );
                }
            },
            update_messages: function ( e ) {
                e.preventDefault();
                var request_id = $( 'input#ywcars_request_id' ).val();
                var $ywcars_messages_history_frame = $( 'div.ywcars_messages_history_frame' );
                if ( request_id ) {
                    var data = {
                        action: 'ywcars_update_messages',
                        security: ywcars_params.ywcars_update_messages,
                        request_id: request_id
                    };
                    ywcars_items_metabox.block_messages( { message: null, overlayCSS:{ background: "#fff", opacity: .6 } } );
                    $.post( ywcars_params.ajax_url, data, function ( resp ) {
                        $ywcars_messages_history_frame.empty();
                        $ywcars_messages_history_frame.prepend( resp );
                        ywcars_items_metabox.unblock_messages();
                    } );
                }
            },
            block: function() {
                $( '#ywcars-items-metabox' ).block({
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });
            },
            unblock: function() {
                $( '#ywcars-items-metabox' ).unblock();
            },
            block_messages: function() {
                $( '#ywcars-messages-metabox' ).block({
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });
            },
            unblock_messages: function() {
                $( '#ywcars-messages-metabox' ).unblock();
            }
        };

        var ywcars_product_data = {
            init: function () {
                $( document.body ).on( 'woocommerce_variations_loaded', this.on_variations );
                $( 'input.ywcars_ndays_radio' ).change( { selector : '._ywcars_ndays_refund_field' } , ywcars_product_data.hide_fields_on_radio_group ).change();
                $( 'input.ywcars_message_radio' ).change( { selector : '._ywcars_message_field' } , ywcars_product_data.hide_fields_on_radio_group ).change();
            },
            on_variations: function () {
                var $variations_data = $( '.ywcars_refunds_product_variations_data' );
                $variations_data.each( function ( index, element ) {
                    $( element ).find( 'input.ywcars_ndays_radio' ).change( { selector : '._ywcars_ndays_refund_variation_field' }, ywcars_product_data.hide_fields_on_radio_group );
                    $( element ).find( 'input.ywcars_message_radio' ).change( { selector : '._ywcars_message_variation_field' }, ywcars_product_data.hide_fields_on_radio_group );
                } );
            },
            hide_fields_on_radio_group: function ( event ) {
                var $custom_field = $( event.data.selector );
                if ( 'custom' == $( this ).filter( ':checked' ).val() ) {
                    $custom_field.show();
                } else {
                    $custom_field.hide();
                }
            }
        };

        var ywcars_orders_table = {
            show_more_requests: function () {
                $( '.ywcars_requests_toggle_button' ).on( 'click', function ( e ) {
                    e.preventDefault();
                    var hidden_requests = $( this ).closest( '.ywcars_requests_wrapper' ).find( '.ywcars_single_request' ).not( '.first' );
                    hidden_requests.toggle( 400 );
                } );
            }
        };

        ywcars_items_metabox.init();
        ywcars_product_data.init();
        ywcars_orders_table.show_more_requests();


    } );
});