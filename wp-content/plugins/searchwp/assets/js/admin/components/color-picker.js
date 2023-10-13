/* global _SEARCHWP */

( function($) {

    'use strict';

    const app = {

        /**
         * Init.
         *
         * @since 4.3.2
         */
        init: () => {

            $( app.ready );
        },

        /**
         * Document ready
         *
         * @since 4.3.2
         */
        ready: () => {

            $('.swp-input--colorpicker input').iris({
                change: function(event, ui) {
                    const $el = $(event.target).siblings('svg').find('path');
                    const color = ui.color.toString();

                    app.updateColorSwatch( $el, color );
                }
            });

            $('.swp-input--colorpicker input').each( function () {
                const $el = $(this).siblings('svg').find('path');
                const color = $(this).iris('color');

                app.updateColorSwatch( $el, color );
            } );

            app.events();
        },

        /**
         * Events.
         *
         * @since 4.3.2
         */
        events: () => {

            $('.swp-input--colorpicker input').on('focus', function () {
                $('.swp-input--colorpicker input').iris('hide');
                $(this).iris('show');
            });

            $('.swp-input--colorpicker input').on('input', function () {
                if ( ! $(this).val() ) {
                    const $el = $(this).siblings('svg').find('path');
                    app.updateColorSwatch( $el, null );
                }
            });

            $(window).on('click', function () {
                $('.swp-input--colorpicker input').iris('hide');
            });

            $('.swp-input--colorpicker').on( 'click', function(e){
                e.stopPropagation();
            });

            $('.swp-input--colorpicker').on( 'keydown', function(e){
                if (e.keyCode === 13 || e.keyCode === 27) {
                    e.preventDefault();
                    $(this).find('input').iris('hide');
                }
            });

            $('a, button, input, textarea, select').on( 'focus', function(e){
                if ( ! $(e.target).closest('.swp-input--colorpicker').length ) {
                    $('.swp-input--colorpicker input').iris('hide');
                }
            });
        },

        /**
         * Update the color of the color swatch.
         *
         * @since 4.3.2
         */
        updateColorSwatch: ( $el, color ) => {

            if ( color === null ) {
                $el.css( 'fill', '#fff' ).css( 'stroke', '#e1e1e1' );
                return;
            }

            $el.css( 'fill', color );

            if ( color.toLowerCase() === '#ffffff' ) {
                $el.css( 'stroke', '#e1e1e1' );
            } else {
                $el.css( 'stroke', color );
            }
        },
    };

    app.init();

    window.searchwp = window.searchwp || {};

    window.searchwp.ColorPicker = window.searchwp.ColorPicker || app;

}( jQuery ) );
