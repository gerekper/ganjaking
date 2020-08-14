jQuery( function( $ ) {

    $( document ).ready( function ( $ ) {

        $( document.body ).on( 'ywcmas_init_pp', function ( event, location ) {
            $( '.ywcmas_shipping_address_button_new, .ywcmas_shipping_address_button_edit' ).each( function ( index, element ) {
                $( element ).prettyPhoto( {
                    hook: 'data-rel',
                    social_tools: false,
                    theme: 'pp_woocommerce',
                    horizontal_padding: 20,
                    opacity: 0.8,
                    deeplinking: false,
                    modal: true,
                    keyboard_shortcuts: false,
                    changepicturecallback: function() {
                        $( document.body ).trigger( 'ywcmas_shipping_address_form_created', location );
                    }
                } );
            } );

            $( '.ywcmas_shipping_address_button_delete' ).each( function ( index, element ) {
                $( element ).prettyPhoto( {
                    hook: 'data-rel',
                    social_tools: false,
                    theme: 'pp_woocommerce',
                    horizontal_padding: 20,
                    opacity: 0.8,
                    default_height: 100,
                    deeplinking: false,
                    modal: true,
                    keyboard_shortcuts: false,
                    changepicturecallback: function() {
                        $( document.body ).trigger( 'ywcmas_delete_shipping_address_window_created', location );
                    }
                } );
            } );
        } );

        $( document.body ).on( 'ywcmas_shipping_address_form_created', function ( event, location ) {
            $( document.body ).trigger( 'scroll.prettyphoto' );

            var $window_content = $( 'div.pp_content_container' );
            var $close_button   = $( 'a.pp_close' );

            var $form = $( 'div.pp_inline div.woocommerce form' );
            var $wrapper = $form.find( '.woocommerce-address-fields__field-wrapper' );
            var country  = $form.find( '#shipping_country' ).val();
            $( document.body ).trigger( 'country_to_state_changing', [country, $wrapper ] );
            $( document.body ).trigger( 'country_to_state_changed' );

            // Fix for allowing scrolling on popup. The scroll was getting blocked after selecting a country.
            $form.find( '#shipping_country' ).on( 'select2:select', function () {
                $(this).selectWoo('open');
                $(this).selectWoo('close');
            } );
            ////////////////////////////////////////////////////////////////////////////////////////////////////////////
            $form.on( 'submit', function ( e ) {
                e.preventDefault();

                var address_id = $( this ).find( '#address_id' ).val();
                var nonce      = $( this ).find( '#_wpnonce' ).val();
                var current_address_id = $( this ).find( '#ywcmas_current_address_id' ).val();

                var $required_fields = $( 'div.woocommerce-address-fields p.validate-required :input' ).not( '.hidden' );
                var empty_fields = false;
                $required_fields.each( function ( index, element ) {
                    if ( '' === $( element ).val() )
                        empty_fields = true;
                } );

                if ( ! empty_fields ) {
                    var form = $( this ).serializeArray();

                    form.push(
                        { name: "action",                value: 'ywcmas_save_address' },
                        { name: "security",              value: nonce },
                        { name: "location",              value: location },
                        { name: "address_id",            value: address_id },
                        { name: "current_address_id",    value: current_address_id }
                    );

                    var settings = {
                        url: ywcmas_frontend_params.ajax_url,
                        data: form,
                        complete: function () {
                            $window_content.unblock();
                            $close_button.show();
                            if ( 'my-account' === location ) {
                                window.location.href = window.location.href;
                            }
                            if ( 'checkout' === location ) {
                                $close_button.click();
                                $( 'select.ywcmas_addresses_manager_address_select' ).trigger( 'change', address_id );
                                $( '#ywcmas_manage_addresses_cb' ).trigger( 'change' );
                            }
                        }
                    };
                    $window_content.block( { message: null, overlayCSS: { background: "#f1f1f1", opacity: .7 } } );
                    $close_button.hide();
                    $.post( settings );
                } else {
                    alert( ywcmas_frontend_params.fill_fields );
                }

            } );
        } );

        $( document.body ).on( 'ywcmas_delete_shipping_address_window_created', function ( event, location ) {
            var $window_content = $( 'div.pp_content_container' );
            var $close_button   = $( 'a.pp_close' );
            var $button_yes     = $window_content.find( '#ywcmas_delete_address_yes' );
            var $button_no      = $window_content.find( '#ywcmas_delete_address_no' );
            $button_yes.on( 'click', function ( e ) {
                e.preventDefault();
                var current_address_id = $( '#ywcmas_current_address_id' ).val();
                var delete_all         = $( '#ywcmas_delete_all' ).val();
                if ( current_address_id || delete_all ) {
                    var settings = {
                        url: ywcmas_frontend_params.ajax_url,
                        data: {
                            action: 'ywcmas_delete_shipping_address',
                            security: ywcmas_frontend_params.delete_shipping_address,
                            current_address_id: current_address_id,
                            delete_all: delete_all
                        },
                        complete: function () {
                            $window_content.unblock();
                            $close_button.show();
                            if ( 'my-account' === location ) {
                                window.location.href = window.location.href;
                            }
                            if ( 'checkout' === location ) {
                                $close_button.click();
                                $( 'select.ywcmas_addresses_manager_address_select' ).trigger( 'change', '_no-address' );
                                $( '#ywcmas_manage_addresses_cb' ).trigger( 'change' );
                            }
                        }
                    };
                    $window_content.block( { message: null, overlayCSS: { background: "#f1f1f1", opacity: .7 } } );
                    $close_button.hide();
                    $.post( settings );
                }
            } );
            $button_no.on( 'click', function ( e ) {
                e.preventDefault();
                $close_button.click();
            } );
        } );

    } );
} );