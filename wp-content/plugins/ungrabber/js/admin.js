/**
 * UnGrabber
 * A most effective way to protect your online content from being copied or grabbed
 * Exclusively on https://1.envato.market/ungrabber
 *
 * @encoding        UTF-8
 * @version         3.0.2
 * @copyright       (C) 2018 - 2021 Merkulove ( https://merkulov.design/ ). All rights reserved.
 * @license         Commercial Software
 * @contributors    Dmitry Merkulov (dmitry@merkulov.design)
 * @support         help@merkulov.design
 **/

jQuery( function ( $ ) {

    "use strict";

    $( document ).ready( function () {

        /**
         * Hide or close next tr after switch
         * @param $element
         * @param num
         * @param reverse
         */
        function switchSingle( $element, num, reverse ) {

            for ( let i = 0; i < num; i++ ) {

                if ( ! reverse ) {

                    $element.is( ':checked' ) ?
                        $element.closest( 'tr' ).nextAll( 'tr' ).eq( i ).show( 300 ) :
                        $element.closest( 'tr' ).nextAll( 'tr' ).eq( i ).hide( 300 );

                } else {

                    $element.is( ':checked' ) ?
                        $element.closest( 'tr' ).nextAll( 'tr' ).eq( i ).hide( 300 ) :
                        $element.closest( 'tr' ).nextAll( 'tr' ).eq( i ).show( 300 );

                }

            }

        }

        /**
         * Init single switch
         * @param $element
         * @param num
         * @param reverse
         */
        function initSingleSwitch( $element, num = 1, reverse = false ) {

            switchSingle( $element, num, reverse );

            $element.on( 'change', () => {

                switchSingle( $element, num, reverse );

            } );

        }

        initSingleSwitch( $( '#mdp_ungrabber_general_settings_right_click' ), 1 , true );
        initSingleSwitch( $( '#mdp_ungrabber_general_settings_javascript' ) );

    } );

} );
