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

            $( document ).on( 'click', '.swp-pills-container .swp-pill-delete', app.clickDelete );
            $( document ).on( 'keypress', '.swp-pills-container .swp-pills-input', app.pressEnter );
        },

        /**
         * Get pills' values.
         *
         * @since 4.3.0
         */
        get: ( target ) => {
            return $( target )
                .closest( '.swp-pills' )
                .find( '.swp-pills-container .swp-pill-text' )
                .map( (i,el) => el.innerText )
                .get();
        },

        /**
         * Add one or more pills.
         *
         * @since 4.3.0
         */
        add: ( target, text ) => {
            const $wrapper = $( target ).closest( '.swp-pills' );
            const template = $wrapper.find( '.swp-pill-template' ).html();

            if ( typeof text === 'string' ) {
                text = [ text ];
            }

            const addedElements = Array.from( text ).map(
                (entry) => {
                    return $( template )
                        .find( '.swp-pill-text' )
                        .text( entry )
                        .closest( '.swp-pill' );
                }
            );

            $wrapper.find( '.swp-pills-container .swp-pills-input' ).before( addedElements );
        },

        /**
         * Add a pill via text input.
         *
         * @since 4.3.0
         */
        input: ( input ) => {
            app.add( input, input.value );
            input.value = '';
        },

        /**
         * Delete pill.
         *
         * @since 4.3.0
         */
        delete: ( target ) => {
            $( target ).closest( '.swp-pill' ).remove();
        },

        /**
         * Clear all pills.
         *
         * @since 4.3.0
         */
        clear: ( target ) => {
            $( target )
                .closest( '.swp-pills' )
                .find( '.swp-pills-container .swp-pill' )
                .remove();
        },

        /**
         * Sort pills ASC or DESC.
         *
         * @since 4.3.0
         */
        sort: ( target, order = 'ASC' ) => {
            if ( ! ['ASC', 'DESC'].includes( order ) ) {
                return;
            }

            const $textElements = $( target ).closest( '.swp-pills' ).find( '.swp-pills-container .swp-pill-text' );
            const values        = $textElements.map( ( i, el ) => el.innerText ).get().sort();

            if ( order === 'DESC' ) {
                values.reverse();
            }

            $textElements.each( (i, el) => { el.innerText = values[i] } );
        },

        /**
         * Callback for clicking on a delete element.
         *
         * @since 4.3.0
         */
        clickDelete: (e) => {
            e.preventDefault();
            app.delete( e.target )
        },

        /**
         * Callback for pressing Enter in an input field.
         *
         * @since 4.3.0
         */
        pressEnter: (e) => {
            if ( e.which === 13 ) {
                app.input( e.target );
            }
        },
    };

    app.init();

    window.searchwp = window.searchwp || {};

    window.searchwp.Pills = window.searchwp.Pills || app;

}( jQuery ) );
