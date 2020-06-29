/* global yith_wcbm_fp_params */
jQuery( function ( $ ) {
    var $container = $( document );
    if ( 'yes' === yith_wcbm_fp_params.is_product && 'single-product-image' === yith_wcbm_fp_params.force_positioning ) {
        // get the product container
        if ( yith_wcbm_fp_params.product_id ) {
            $container = $( '#product-' + yith_wcbm_fp_params.product_id ).first();
        } else if ( yith_wcbm_fp_params.post_id ) {
            $container = $( '#product-' + yith_wcbm_fp_params.post_id ).first();
            if ( !$container.length ) {
                $container = $( '.post-' + yith_wcbm_fp_params.post_id ).first();
            }
        }

        if ( !$container.length ) {
            $container = $( '.product' ).first();
        }

        // get images container
        if ( $container.length ) {
            $container = $container.find( '.images' ).first();
        }

        if ( !$container.length ) {
            return;
        }
    }

    var $clonedBadgeContainer        = $( '#yith-wcbm-cloned-badges' ),
        $badges                      = $container.find( '.yith-wcbm-badge:not(.yith-wcbm-badge-clone):visible' ),
        $body                        = $( 'body' ),
        force_badge_positioning      = function () {
            if ( $clonedBadgeContainer.length < 1 ) {
                $clonedBadgeContainer = $( '<div id="yith-wcbm-cloned-badges"></div>' );
                $( 'body' ).append( $clonedBadgeContainer );
            } else {
                $clonedBadgeContainer.html( '' );
            }

            $badges.show();

            $badges.each( function () {
                var $badge        = $( this ),
                    $badgeClone   = $badge.clone().addClass( 'yith-wcbm-badge-clone' ),
                    relative_body = $body.css( 'position' ) !== 'static',
                    top_offset    = relative_body ? ( $badge.offset().top - $body.position().top ) + 'px' : $badge.offset().top + 'px',
                    left_offset   = relative_body ? ( $badge.offset().left - $body.position().left ) + 'px' : $badge.offset().left + 'px',
                    cssOptions    = {
                        top   : top_offset,
                        left  : left_offset,
                        bottom: 'auto',
                        right : 'auto'
                    };

                $badgeClone.css( cssOptions );
                $badge.hide();

                $clonedBadgeContainer.append( $badgeClone );
                $badgeClone.show();
            } );
        },
        force_badge_positioning_safe = function () {
            force_badge_positioning();
            // to be sure, fire again the force_positioning after some milliseconds
            setTimeout( force_badge_positioning, 50 );
        };

    setTimeout( force_badge_positioning, yith_wcbm_fp_params.timeout );

    $( window ).resize( force_badge_positioning );

    if ( yith_wcbm_fp_params.is_mobile === 'yes' && yith_wcbm_fp_params.on_scroll_mobile ) {
        $( window ).scroll( force_badge_positioning );
    }

    // Single product tabs click
    $( document ).on( 'click', '.wc-tabs li', force_badge_positioning_safe );

    // allow forcing badge positioning by third-party plugins
    $( document ).on( 'yith_wcbm_force_badge_positioning', force_badge_positioning );
    $( document ).on( 'yith_wcbm_force_badge_positioning_safe', force_badge_positioning_safe );
} );