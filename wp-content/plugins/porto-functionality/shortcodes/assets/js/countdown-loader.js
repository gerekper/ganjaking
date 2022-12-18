/* Count Down */
jQuery( document ).ready( function( $ ) {
    'use strict';
    function porto_init_countdown( $elements ) {
        if ( typeof $elements == 'undefined' ) {
            $elements = $( 'body' );
        }
        $elements.find( '.porto_countdown-dateAndTime' ).each( function() {
            if ( typeof $( this ).data( 'porto_countdown_initialized' ) != 'undefined' && $( this ).data( 'porto_countdown_initialized' ) ) {
                return;
            }
            var t = new Date( $( this ).attr( 'data-terminal-date' ) ),
                tz = $( this ).attr( 'data-time-zone' ) * 60,
                tfrmt = $( this ).attr( 'data-countformat' ),
                labels_new = $( this ).attr( 'data-labels' ),
                new_labels = labels_new.split( "," ),
                labels_new_2 = $( this ).attr( 'data-labels2' ),
                new_labels_2 = labels_new_2.split( "," ),
                server_time = function() {
                    return new Date( $( this ).attr( 'data-time-now' ) );
                };

            var ticked = function( a ) {
                var count_amount = $( this ).find( '.porto_countdown-amount' ),
                    count_period = $( this ).find( '.porto_countdown-period' ),
                    tick_fontfamily = $( this ).attr( 'data-tick-font-family' ),
                    count_amount_css = '',
                    count_amount_font = '',
                    tick_br_color = $( this ).attr( 'data-br-color' ),
                    tick_br_size = $( this ).attr( 'data-br-size' ),
                    tick_br_style = $( this ).attr( 'data-br-style' ),
                    tick_br_radius = $( this ).attr( 'data-br-radius' ),
                    tick_bg_color = $( this ).attr( 'data-bg-color' ),
                    tick_padd = $( this ).attr( 'data-padd' );

                // Applied CSS for Count Amount & Period
                count_amount.css( {
                    // 'color'         : tick_color,
                    'font-family': tick_fontfamily,
                    'border-width': tick_br_size,
                    'border-style': tick_br_style,
                    'border-radius': tick_br_radius,
                    'background': tick_bg_color,
                    'padding': tick_padd,
                    'border-color': tick_br_color
                } );
            }

            if ( $( this ).hasClass( 'porto-usrtz' ) ) {
                $( this ).porto_countdown( { labels: new_labels, labels1: new_labels_2, until: t, format: tfrmt, padZeroes: true, onTick: ticked } );
            } else {
                $( this ).porto_countdown( { labels: new_labels, labels1: new_labels_2, until: t, format: tfrmt, padZeroes: true, onTick: ticked, serverSync: server_time } );
            }
            $( this ).data( 'porto_countdown_initialized', true );
        } );
    }
    porto_init_countdown();
    $( document.body ).on( 'porto_init_countdown', function( e, obj ) {
        porto_init_countdown( obj );
    } );
} );