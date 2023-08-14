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
         * Extension page events.
         *
         * @since 4.3.0
         */
        events: () => {

            $( '.swp-toggle-checkbox' ).on( 'change', app.changeToggle );
        },

        /**
         * Callback for changing a setting toggle value.
         *
         * @since 4.3.0
         */
        changeToggle: (e) => {
            // TODO: Prevent multiple speed clicks.
            const $checkbox = $( e.target );
            const data      = {
                _ajax_nonce: _SEARCHWP.nonce,
                action: _SEARCHWP.prefix + 'update_setting',
                setting: $checkbox.attr( 'name' ),
                value: JSON.stringify( e.target.checked ),
            };

            $checkbox.siblings( '.swp-toggle-switch' ).css( 'opacity', 0.6 );

            $.post( ajaxurl, data, ( response ) => {
                if ( response.success ) {
                    $checkbox.siblings( '.swp-toggle-switch' ).css( 'opacity', 1 );
                    $checkbox.siblings( '.swp-toggle-switch' ).after( '<span class="swp-toggle-saved-msg swp-text-green swp-b">Saved</span>' );
                    setTimeout( () => { $checkbox.siblings( '.swp-toggle-saved-msg' ).remove() }, 1500 );
                } else {
                    console.error( response );
                    $checkbox.siblings( '.swp-toggle-switch' ).after( '<span class="swp-error-msg swp-text-red swp-b">Setting update failed</span>' );
                    setTimeout( () => { $checkbox.siblings( '.swp-error-msg' ).remove() }, 3000 );
                }
            } );
        },
    };

    app.init();

    window.searchwp = window.searchwp || {};

    window.searchwp.SettingsToggle = app;

}( jQuery ) );
