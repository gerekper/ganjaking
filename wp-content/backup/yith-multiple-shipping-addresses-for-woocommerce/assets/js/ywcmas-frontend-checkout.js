jQuery( function( $ ) {

    $( document ).ready( function ( $ ) {

        ///// INIT VARIABLES /////

        // Ship to a different address fields
        var $shipping_fields   = $( 'div.woocommerce-shipping-fields' );
        var $addresses_manager = $( 'div.ywcmas_multiple_addresses_manager' );
        var $addresses_viewer  = $( 'div.ywcmas_manage_addresses_viewer_container' );
        var $addresses_tables  = $( 'div.ywcmas_manage_addresses_tables_container' );



        ///// CHECKOUT OBJECT /////

        var checkout = {
            init: function () {
                this.change_events();
                this.click_events();
                this.load_pp_buttons();
            },
            after_update_multi_shipping_data: function () {
                $( 'input.ywcmas_addresses_manager_table_qty' ).trigger( 'change' );

                $( 'table.ywcmas_addresses_manager_table' ).each( function ( i, elem ) {
                    var shipping_selectors = $( elem ).find( 'tr.ywcmas_addresses_manager_table_shipping_selection_row' ).length;
                    var different_addresses_limit = parseInt( $( elem ).find( '.ywcmas_different_addresses_limit' ).val() );
                    var any_qty_major_than_one = false;
                    $( elem ).find( '.ywcmas_addresses_manager_table_qty' ).each( function ( index, element ) {
                        if ( $( element ).val() > 1 ) {
                            any_qty_major_than_one = true;
                        }
                    } );
                    var $first_shipping_selector          = $( elem ).find( '.ywcmas_addresses_manager_table_shipping_selector_id' ).first();
                    var $excluded_item_alert              = $( elem ).find( '.ywcmas_excluded_item' );
                    var $no_more_shipping_selectors_alert = $( elem ).find( '.ywcmas_no_more_shipping_selectors_alert' );
                    var $increase_qty_alert               = $( elem ).find( '.ywcmas_increase_qty_alert' );
                    var $new_shipping_selector_button     = $( elem ).find( 'a.ywcmas_new_shipping_selector_button' );

                    if ( 'excluded_item' === $first_shipping_selector.val() ) {
                        $excluded_item_alert.show();
                    } else if ( shipping_selectors >= different_addresses_limit ) {
                        $no_more_shipping_selectors_alert.show();
                    } else if ( any_qty_major_than_one ) {
                        $new_shipping_selector_button.show();
                    } else {
                        $increase_qty_alert.show();
                    }
                } );

                $( document.body ).trigger( 'update_checkout' );
            },
            change_events: function () {
                // "Do you want to ship to multiple addresses?" checkbox
                $( document.body ).on( 'change', '#ywcmas_manage_addresses_cb', function ( event ) {
                    var value = $( this ).prop( 'checked' );
                    if ( value ) {
                        $shipping_fields.hide();
                        // checkout.load_addresses_manager();
                        $addresses_manager.slideDown();
                        checkout.block_addresses_manager();
                    } else {
                        $shipping_fields.show();
                        $addresses_manager.hide();
                    }
                    var data = {
                        action: 'ywcmas_update_multi_shipping_data',
                        update_data_nonce: ywcmas_checkout_params.update_data_nonce,
                        multi_shipping_enabled: value
                    };

                    $.post( ywcmas_checkout_params.ajax_url, data, function ( data ) {
                        $addresses_tables.html( data );
                        checkout.unblock_addresses_manager();
                        checkout.after_update_multi_shipping_data();
                    } );
                } );
                $( '#ywcmas_manage_addresses_cb' ).trigger( 'change' );

                // When the address in address viewer is changed on <select>
                $( document.body ).on( 'change', 'select.ywcmas_addresses_manager_address_select', function ( event, address_id ) {
                    var value = address_id ? address_id : $( this ).val();
                    var id_selected = $( '.ywcmas_manage_addresses_viewer_id_selected' );
                    id_selected.val( value );
                    checkout.block_addresses_manager();
                    $.post( ywcmas_checkout_params.ajax_url, { action: 'ywcmas_print_address', ywcmas_address_id: value }, function ( data ) {
                        $addresses_viewer.html( data );
                        checkout.unblock_addresses_manager();
                        checkout.load_pp_buttons();
                    } );
                } );

                // When the input number for quantity changes...
                $( document.body ).on( 'keyup mouseup', 'input.ywcmas_addresses_manager_table_qty', function ( event ) {
                    // Get the parent <td> for search the elements
                    var $td = $( this ).closest( 'td' );
                    // The new qty is selected on input type number
                    var new_qty = $( this ).val();
                    // The current qty on cart taken from hidden input .ywcmas_addresses_manager_table_current_qty
                    var current_qty = $td.find( '.ywcmas_addresses_manager_table_current_qty' ).val();
                    // The Update button on current row
                    var $update_button = $td.find( 'a.ywcmas_addresses_manager_table_update_qty_button' );
                    // Show or hide the Update button
                    if ( new_qty > 0 && new_qty != current_qty ) {
                        $update_button.css( 'display', 'table-row' );
                    } else {
                        $update_button.hide();
                    }
                } );
                $( 'input.ywcmas_addresses_manager_table_qty' ).trigger( 'change' );

                $( document.body ).on( 'change', 'select.ywcmas_addresses_manager_table_shipping_address_select', function ( event ) {

                    var $td = $( this ).closest( 'td.ywcmas_addresses_manager_table_qty_td' );
                    var new_shipping_address = $( this ).val();
                    var shipping_selector_id = $td.find( 'input.ywcmas_addresses_manager_table_shipping_selector_id' ).val();
                    var item_cart_id = $td.find( 'input.ywcmas_addresses_manager_table_item_cart_id' ).val();


                    checkout.block_addresses_manager();
                    var data = {
                        action: 'ywcmas_update_multi_shipping_data',
                        update_data_nonce: ywcmas_checkout_params.update_data_nonce,
                        update_data_action: 'ywcmas_update_shipping_address',
                        new_shipping_address: new_shipping_address,
                        shipping_selector_id: shipping_selector_id,
                        item_cart_id: item_cart_id
                    };

                    $.post( ywcmas_checkout_params.ajax_url, data, function ( data ) {
                        $addresses_tables.html( data );
                        checkout.unblock_addresses_manager();
                        checkout.after_update_multi_shipping_data();
                    } );
                } );

            },
            click_events: function () {
                // Update button click
                $( document.body ).on( 'click', 'a.ywcmas_addresses_manager_table_update_qty_button', function ( event ) {
                    event.preventDefault();

                    // Get the parent <div> for taking the values later from this
                    var $ywcmas_qty = $( this ).closest( 'div.ywcmas_qty' );
                    var new_qty = $ywcmas_qty.find( 'input.ywcmas_addresses_manager_table_qty' ).val();
                    var current_qty = $ywcmas_qty.find( 'input.ywcmas_addresses_manager_table_current_qty' ).val();
                    var shipping_selector_id = $ywcmas_qty.find( 'input.ywcmas_addresses_manager_table_shipping_selector_id' ).val();
                    var item_cart_id = $ywcmas_qty.find( 'input.ywcmas_addresses_manager_table_item_cart_id' ).val();

                    if ( new_qty > 0 && new_qty != current_qty ) {
                        checkout.block_addresses_manager();
                        var data = {
                            action: 'ywcmas_update_multi_shipping_data',
                            update_data_nonce: ywcmas_checkout_params.update_data_nonce,
                            update_data_action: 'ywcmas_update_qty',
                            new_qty: new_qty,
                            shipping_selector_id: shipping_selector_id,
                            item_cart_id: item_cart_id
                        };

                        $.post( ywcmas_checkout_params.ajax_url, data, function ( data ) {
                            $addresses_tables.html( data );
                            checkout.unblock_addresses_manager();
                            checkout.after_update_multi_shipping_data();
                        } );
                    }
                } );

                // "Ship this item to other addresses" button / split quantity button
                $( document.body ).on( 'click', 'a.ywcmas_new_shipping_selector_button', function ( event ) {
                    event.preventDefault();
                    var $table = $( this ).closest( 'table.ywcmas_addresses_manager_table' );

                    var any_qty_major_than_one = false;
                    $table.find( 'input.ywcmas_addresses_manager_table_current_qty' ).each( function ( index, element ) {
                        if ( $( element ).val() > 1 ) {
                            any_qty_major_than_one = true;
                        }
                    } );
                    if ( any_qty_major_than_one ) {
                        var item_cart_id = $table.find( 'input.ywcmas_addresses_manager_table_item_cart_id' ).val();
                        checkout.block_addresses_manager();

                        var data = {
                            action: 'ywcmas_update_multi_shipping_data',
                            update_data_nonce: ywcmas_checkout_params.update_data_nonce,
                            update_data_action: 'ywcmas_new_shipping_selector',
                            // shipping_selector_id: shipping_selector_id,
                            item_cart_id: item_cart_id
                        };

                        $.post( ywcmas_checkout_params.ajax_url, data, function ( data ) {
                            $addresses_tables.html( data );
                            checkout.unblock_addresses_manager();
                            checkout.after_update_multi_shipping_data();
                        } );
                    }
                } );
                $( document.body ).on( 'click', 'div.ywcmas_addresses_manager_table_remove_button', function ( event ) {
                    event.preventDefault();
                    var $td = $( this ).closest( 'td.ywcmas_addresses_manager_table_qty_td' );
                    var current_qty = $td.find( 'input.ywcmas_addresses_manager_table_current_qty' ).val();
                    var item_cart_id = $td.find( 'input.ywcmas_addresses_manager_table_item_cart_id' ).val();
                    var shipping_selector_id = $td.find( 'input.ywcmas_addresses_manager_table_shipping_selector_id' ).val();

                    checkout.block_addresses_manager();

                    var data = {
                        action: 'ywcmas_update_multi_shipping_data',
                        update_data_nonce: ywcmas_checkout_params.update_data_nonce,
                        update_data_action: 'ywcmas_delete_shipping_selector',
                        current_qty: current_qty,
                        item_cart_id: item_cart_id,
                        shipping_selector_id: shipping_selector_id
                    };

                    $.post( ywcmas_checkout_params.ajax_url, data, function ( data ) {
                        $addresses_tables.html( data );
                        checkout.unblock_addresses_manager();
                        checkout.after_update_multi_shipping_data();
                    } );
                } );
            },
            load_pp_buttons: function () {
                $( document.body ).trigger( 'ywcmas_init_pp', 'checkout' );
            },
            block_addresses_manager: function() {
                $addresses_manager.block({
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });
            },
            unblock_addresses_manager: function() {
                $addresses_manager.unblock();
            }

        };

        checkout.init();

    } );
} );