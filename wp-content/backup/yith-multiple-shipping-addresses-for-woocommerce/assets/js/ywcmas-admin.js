jQuery( function( $ ) {
    $( document ).ready( function ( $ ) {

        var $ywcmas_shipping_address = $( 'table.woocommerce_order_items tbody#order_shipping_line_items tr.shipping td.name div.ywcmas_shipping_address' );
        if ( $ywcmas_shipping_address.length > 0 ) {
            $( 'div.order_data_column_container div.order_data_column:eq( 2 )' ).hide();
        }

        var dialogs_array = [];
        $( '.ywcmas_edit_shipping_item_dialog' ).each( function ( index, element ) {
            // Fixing selectWoo search box. Without this code, the search box is not clickable
            $.ui.dialog.prototype._allowInteraction = function (e) {
                return true;
            };
            //////////////////////////////////////////////////////////////////////////////////
            var dialog = $( element );
            var shipping_id = dialog.data( 'shipping_id' );

            dialog.find( '.js_field-state' ).addClass( 'ywcmas_destination_shipping_state' ); // force adding class to State select2
            dialogs_array[shipping_id] = dialog;
            var initial_address = get_initial_address( dialog );

            var load_address_button = dialog.find( '.ywcmas_edit_shipping_item_load_button' );
            var revert_button = dialog.find( '.ywcmas_edit_shipping_item_revert_button' );

            load_address_button.on( 'click', function ( e ) {
                e.preventDefault();
                var address_id = dialog.find( '.ywcmas_edit_shipping_item_addresses_select' ).val();
                load_address( address_id, shipping_id, dialog );
            } );

            revert_button.on( 'click', function ( e ) {
                e.preventDefault();
                load_initial_address( initial_address, dialog );
            } );

            var dialog_settings = {
                autoOpen: false,
                show: {
                    effect: 'scale',
                    duration: 300
                },
                hide: {
                    effect: 'scale',
                    duration: 300
                },
                modal: true,
                width: 450,
                fluid: true,
                buttons: [
                    {
                        text: ywcmas_admin.save_button,
                        click: function() {
                            save_address( dialog );
                            initial_address = get_initial_address( dialog );
                            $cancel_button = $( '.wc-order-add-item .cancel-action' );
                            $cancel_button.attr( 'data-reload', true );
                            $cancel_button.click();
                        }
                    },
                    {
                        text: ywcmas_admin.cancel_button,
                        click: function() {
                            load_initial_address( initial_address, dialog );
                            $( this ).dialog( 'close' );
                        }
                    }
                ]
            };

            dialog.dialog( dialog_settings );
        } );

        $( '#woocommerce-order-items' ).on( 'click', '.ywcmas_edit_shipping_address_button', function( e ) {
            e.preventDefault();
            e.stopPropagation();
            var shipping_id = $( this ).data( 'shipping_id' );
            dialogs_array[shipping_id].dialog( 'open' );
        } );

        function load_address( address_id, shipping_id, dialog ) {
            if ( address_id && shipping_id ) {
                dialog_window = dialog.closest( 'div.ui-dialog' );
                dialog_window.block( { message: null, overlayCSS: { background: "#f1f1f1", opacity: .7 }, baseZ: 200000 } );
                data = {
                    action: 'ywcmas_admin_get_user_address',
                    address_id: address_id,
                    order_id: $( '#post_ID' ).val()
                };
                $.post( ywcmas_admin.ajax_url, data, function ( values ) {
                    dialog_window.unblock();
                    var address = values['data'] ? values['data'] : '';
                    if ( address ) {
                        var prefix = 'billing_address' == address_id ? 'billing_' : 'shipping_';

                        $( dialog ).find( '.ywcmas_destination_shipping_first_name' ).val( address[prefix + 'first_name'] );
                        $( dialog ).find( '.ywcmas_destination_shipping_last_name' ).val( address[prefix + 'last_name'] );
                        $( dialog ).find( '.ywcmas_destination_shipping_company' ).val( address[prefix + 'company'] );
                        $( dialog ).find( '.ywcmas_destination_shipping_country' ).val( address[prefix + 'country'] ).trigger( 'change' );
                        $( dialog ).find( '.js_field-state' ).val( address[prefix + 'state'] ).trigger( 'change' );
                        $( dialog ).find( '.ywcmas_destination_shipping_address_1' ).val( address[prefix + 'address_1'] );
                        $( dialog ).find( '.ywcmas_destination_shipping_address_2' ).val( address[prefix + 'address_2'] );
                        $( dialog ).find( '.ywcmas_destination_shipping_city' ).val( address[prefix + 'city'] );
                        $( dialog ).find( '.ywcmas_destination_shipping_postcode' ).val( address[prefix + 'postcode'] );
                    }
                } );
            }
        }

        function save_address( dialog ) {
            var dialog_window = $( dialog ).closest( 'div.ui-dialog' );
            var shipping_id = $( dialog ).closest( '.ywcmas_edit_shipping_item_dialog' ).data( 'shipping_id' );
            dialog_window.block( { message: null, overlayCSS: { background: "#f1f1f1", opacity: .7 }, baseZ: 200000 } );
            var data = {
                action: 'ywcmas_save_shipping_item_address',
                shipping_id: shipping_id,
                order_id: $( '#post_ID' ).val(),
                address: {
                    first_name: $( dialog ).find( '.ywcmas_destination_shipping_first_name' ).val(),
                    last_name:  $( dialog ).find( '.ywcmas_destination_shipping_last_name' ).val(),
                    company:    $( dialog ).find( '.ywcmas_destination_shipping_company' ).val(),
                    country:    $( dialog ).find( '.ywcmas_destination_shipping_country' ).val(),
                    address:    $( dialog ).find( '.ywcmas_destination_shipping_address_1' ).val(),
                    address_2:  $( dialog ).find( '.ywcmas_destination_shipping_address_2' ).val(),
                    city:       $( dialog ).find( '.ywcmas_destination_shipping_city' ).val(),
                    state:      $( dialog ).find( '.js_field-state' ).val(),
                    postcode:   $( dialog ).find( '.ywcmas_destination_shipping_postcode' ).val()
                }
            };
            $.post( ywcmas_admin.ajax_url, data, function () {
                dialog_window.unblock();
            } );
        }

        function get_initial_address( dialog ) {
            return {
                first_name: $( dialog ).find( '.ywcmas_destination_shipping_first_name' ).val(),
                last_name:  $( dialog ).find( '.ywcmas_destination_shipping_last_name' ).val(),
                company:    $( dialog ).find( '.ywcmas_destination_shipping_company' ).val(),
                country:    $( dialog ).find( '.ywcmas_destination_shipping_country' ).val(),
                address:    $( dialog ).find( '.ywcmas_destination_shipping_address_1' ).val(),
                address_2:  $( dialog ).find( '.ywcmas_destination_shipping_address_2' ).val(),
                city:       $( dialog ).find( '.ywcmas_destination_shipping_city' ).val(),
                state:      $( dialog ).find( '.js_field-state' ).val(),
                postcode:   $( dialog ).find( '.ywcmas_destination_shipping_postcode' ).val()
            };
        }

        function load_initial_address( initial_address, dialog ) {
            $.each( initial_address, function( key, value ) {
                key = key === 'address' ? 'address_1' : key;
                dialog.find( '.ywcmas_destination_shipping_' + key ).val( value );
            });
            dialog.find( '.ywcmas_destination_shipping_country' ).trigger( 'change' );

        }

    } );
} );
