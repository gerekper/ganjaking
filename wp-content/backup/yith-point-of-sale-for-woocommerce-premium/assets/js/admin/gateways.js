/* global woocommerce_admin */
( function ( $ ) {

    var gatewayTable = $( '.wc_gateways' );

    if ( gatewayTable.length > 0 ) {
        var $toggle = gatewayTable.find( '.yith-disabled' );

        if ( $toggle.length ) {
            $toggle.each( function () {
                var $t = $( this );
                if ( $t.hasClass( 'yith-disabled' ) ) {
                    $t.css( { 'opacity': '.5' } );
                }
            } );
        }

    }

    $( '.wc_gateways' ).on( 'click', '.yith_pos_gateway_toggle_enable', function ( e ) {
        e.preventDefault();
        var $link   = $( this ),
            $row    = $link.closest( 'tr' ),
            $toggle = $link.find( '.woocommerce-input-toggle' );


        var data = {
            action    : 'yith_pos_gateway_toggle_enable',
            security  : woocommerce_admin.nonces.gateway_toggle,
            gateway_id: $row.data( 'gateway_id' )
        };

        $toggle.addClass( 'woocommerce-input-toggle--loading' );

        $.ajax( {
                    url     : woocommerce_admin.ajax_url,
                    data    : data,
                    dataType: 'json',
                    type    : 'POST',
                    success : function ( response ) {
                        if ( true === response.data ) {
                            $toggle.removeClass( 'woocommerce-input-toggle--enabled, woocommerce-input-toggle--disabled' );
                            $toggle.addClass( 'woocommerce-input-toggle--enabled' );
                            $toggle.removeClass( 'woocommerce-input-toggle--loading' );
                        } else if ( false === response.data ) {
                            $toggle.removeClass( 'woocommerce-input-toggle--enabled, woocommerce-input-toggle--disabled' );
                            $toggle.addClass( 'woocommerce-input-toggle--disabled' );
                            $toggle.removeClass( 'woocommerce-input-toggle--loading' );
                        } else if ( 'needs_setup' === response.data ) {
                            window.location.href = $link.attr( 'href' );
                        }
                    }
                } );

        return false;
    } );

} )( jQuery );