jQuery( function( $ ) {

    $( document ).ready( function ( $ ) {

        $( document.body ).trigger( 'ywcmas_init_pp', 'my-account' );

        $( document.body ).on( 'ycmas_default_address_init_events', function () {

            var $selector_container = $( '#ywcmas_default_address_selector_container' );
            var toggle = { duration: '400' };

            $( '#ywcmas_default_address_change_button' ).on( 'click', function ( e ) {
                e.preventDefault();
                $selector_container.toggle( toggle );
            } );

            $( '#ywcmas_default_address_update_button' ).on( 'click', function ( e ) {
                e.preventDefault();
                $( '#ywcmas_default_address_viewer' ).block({
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });
                var data = {
                    action: 'ywcmas_update_default_address',
                    default_address: $( '#ywcmas_default_address_selector' ).val(),
                    user_id: $( '#ywcmas_user_id' ).val()
                };

                $.post( ywcmas_my_account_params.ajax_url, data, function ( data ) {
                    $( '#ywcmas_default_address_viewer' ).unblock();
                    $selector_container.toggle( toggle );
                    if ( data['success'] === true ) {
                        $( '#ywcmas_default_address_block' ).html( data['data'] );
                        $( document.body ).trigger( 'ycmas_default_address_init_events' );
                    }
                } );
            } );
        } );
        $( document.body ).trigger( 'ycmas_default_address_init_events' );

    } );
} );