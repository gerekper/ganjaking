/* global _SEARCHWP */

( function($) {

    'use strict';

    const app = {

        /**
         * Init.
         *
         * @since 4.3.0
         */
        init: () => {

            $( app.ready );
        },

        /**
         * Document ready
         *
         * @since 4.3.0
         */
        ready: () => {

            app.events();
        },

        /**
         * Events.
         *
         * @since 4.3.0
         */
        events: () => {

            $( document ).on( 'click', '[data-swp-modal]', app.clickOpen );
            $( document ).on( 'click','.swp-modal--close, .swp-modal--cancel', app.clickClose );
            $( document ).on( 'click','.swp-modal--bg', app.clickAway );
        },

        /**
         * Callback for clicking on an opening element.
         *
         * @since 4.3.0
         */
        clickOpen: (e) => {
            e.preventDefault();
            const $modal = $( $( e.target ).data( 'swp-modal' ) );
            if ( ! $modal.length ) {
                return;
            }
            $( '.swp-modal' ).hide();
            $( 'body' ).addClass( 'swp-modal-opened' );
            $modal.show();
        },

        /**
         * Callback for clicking on a closing button.
         *
         * @since 4.3.0
         */
        clickClose: (e) => {
            e.preventDefault();
            $( e.currentTarget ).closest( '.swp-modal' ).hide();
            $( 'body' ).removeClass( 'swp-modal-opened' );
        },

        /**
         * Callback for clicking away from the modal.
         *
         * @since 4.3.0
         */
        clickAway: (e) => {
            $( '.swp-modal' ).hide();
            $( 'body' ).removeClass( 'swp-modal-opened' );
        },
    };

    app.init();

    window.searchwp = window.searchwp || {};

    window.searchwp.Modal = window.searchwp.Modal || app;

}( jQuery ) );
