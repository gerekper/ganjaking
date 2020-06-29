jQuery( function ( $ ) {
    $( document ).on( 'click', '.yith-wcpsc-product-size-chart-button, .yith-wcpsc-product-size-chart-list, a[href^="#yith-size-chart?"]', function ( event ) {
        var c_id      = $( this ).data( 'chart-id' ),
            all_popup = $( '.yith-wcpsc-product-size-charts-popup' ),
            style     = $( this ).data( 'chart-style' ),
            effect    = $( this ).data( 'chart-effect' ),
            href      = $( this ).attr( 'href' );

        if ( href && href.length > 0 && href.search( '#yith-size-chart?' ) > -1 ) {
            event.preventDefault();
            href     = href.replace( '#yith-size-chart?', '' );
            var data = href.split( '&' );
            if ( data.length > 0 ) {
                for ( var i = 0; i < data.length; i++ ) {
                    var current_data = data[ i ].split( '=' );
                    if ( current_data.length == 2 ) {
                        switch ( current_data[ 0 ] ) {
                            case 'id':
                                if ( !c_id ) {
                                    c_id = current_data[ 1 ];
                                }
                                break;
                            case 'style':
                                if ( !style ) {
                                    style = current_data[ 1 ];
                                }
                                break;
                            case 'effect':
                                if ( !effect ) {
                                    effect = current_data[ 1 ];
                                }
                                break;
                        }
                    }
                }
            }
        }

        var my_popup = $( '#yith-wcpsc-product-size-charts-popup-' + c_id );

        if ( style && style.length > 0 ) {
            my_popup.removeClass( 'yith-wcpsc-product-size-charts-popup-default' )
                .removeClass( 'yith-wcpsc-product-size-charts-popup-elegant' )
                .removeClass( 'yith-wcpsc-product-size-charts-popup-casual' )
                .removeClass( 'yith-wcpsc-product-size-charts-popup-informal' )
                .addClass( 'yith-wcpsc-product-size-charts-popup-' + style );
        }

        if ( !( effect && effect.length > 0 ) ) {
            effect = ajax_object.popup_effect
        }

        // set max height of table wrapper to allow scrolling
        my_popup.find( '.yith-wcpsc-product-table-wrapper' ).css( 'max-height', ( $( window ).height() - 120 ) + 'px' );

        all_popup.each( function () {
            $( this ).yith_wcpsc_popup( 'close' );
        } );

        my_popup.find( '.yith-wcpsc-product-table-wrapper-tabbed-popup' ).tabs();

        var created_popup = my_popup.yith_wcpsc_popup( {
                                                           position: ajax_object.popup_position,
                                                           effect  : effect
                                                       } );
        created_popup.find( '.yith-wcpsc-product-table-wrapper-tabbed-popup' ).tabs();
        var containersWithShadow = created_popup.find( '.yith-wcpsc-product-table-responsive-container-with-shadow' );
        if ( containersWithShadow.length ) {
            containersWithShadow.each( function () {
                initContainerWithShadow( $( this ) );
            } );
        }
    } );

    // set max height of table wrapper to allow scrolling
    $( '.yith-wcpsc-product-size-charts-popup-container .yith-wcpsc-product-table-wrapper' ).css( 'max-height', ( $( window ).height() - 120 ) + 'px' );


    // shadows on scrolling
    var containersWithShadow    = $( '.yith-wcpsc-product-table-responsive-container-with-shadow' ),
        initContainerWithShadow = function ( containerWithShadow ) {
            var leftShadow     = containerWithShadow.find( '.yith-wcpsc-left-shadow' ),
                rightShadow    = containerWithShadow.find( '.yith-wcpsc-right-shadow' ),
                tableContainer = containerWithShadow.find( '.yith-wcpsc-product-table-responsive-container' ),
                table          = tableContainer.find( 'table' ).first();

            tableContainer.on( 'scroll yith-wcpsc-init-shadows', function () {
                shadowVisibility( tableContainer, table, leftShadow, rightShadow );
            } );
            shadowVisibility( tableContainer, table, leftShadow, rightShadow );
        },
        shadowVisibility        = function ( tableContainer, table, leftShadow, rightShadow ) {
            var gap = 20, op;

            if ( tableContainer.scrollLeft() < gap ) {
                op = tableContainer.scrollLeft() / gap;
                leftShadow.css( { opacity: op } );
            } else {
                leftShadow.css( { opacity: 1 } );
            }

            if ( tableContainer.scrollLeft() > ( table.outerWidth() - tableContainer.width() - gap ) ) {
                op = 1 - ( ( tableContainer.scrollLeft() - table.outerWidth() + tableContainer.width() + gap ) / gap );
                rightShadow.css( { opacity: op } );
            } else {
                rightShadow.css( { opacity: 1 } );
            }
        };
    containersWithShadow.each( function () {
        initContainerWithShadow( $( this ) );
    } );

    $( window ).on( 'resize', function () {
        containersWithShadow.find( '.yith-wcpsc-product-table-responsive-container' ).trigger( 'yith-wcpsc-init-shadows' );
    } );

    // reload shadows in WooCommerce tabs to prevent display issue
    $( document ).on( 'click', '.woocommerce-tabs li a', function () {
        containersWithShadow.find( '.yith-wcpsc-product-table-responsive-container' ).trigger( 'yith-wcpsc-init-shadows' );
    } );
} );
