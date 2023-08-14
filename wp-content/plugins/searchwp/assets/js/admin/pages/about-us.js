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
         * Plugin page events.
         *
         * @since 4.3.0
         */
        events: () => {

            app.pluginActionBtnEvents();
        },

        /**
         * Plugin action button events.
         *
         * @since 4.3.0
         */
        pluginActionBtnEvents: () => {

            $( '.swp-plugin-status-missing button' ).on( 'click', ( event ) => {
                app.pluginActionBtnCallback( 'install', event );
            } );

            $( '.swp-plugin-status-inactive button' ).on( 'click', ( event ) => {
                app.pluginActionBtnCallback( 'activate', event );
            } );
        },

        /**
         * Plugin action button callback.
         *
         * @since 4.3.0
         */
        pluginActionBtnCallback: ( action, event ) => {

            event.preventDefault();
            event.stopImmediatePropagation();

            const actions = {
                install: `${_SEARCHWP.prefix}plugin_install`,
                activate: `${_SEARCHWP.prefix}plugin_activate`,
            }

            if ( ! ( action in actions ) ) {
                return;
            }

            const elements = {
                $btn: $( event.currentTarget ),
            };

            elements.$footer  = elements.$btn.closest( '.swp-card--footer' );
            elements.$action  = elements.$btn.closest( '.swp-plugin-status' );
            elements.$actions = elements.$btn.closest( '.swp-plugin-statuses' );

            elements.$btn.addClass( 'swp-button--processing' ).attr( 'disabled','disabled' );

            $.post(
                ajaxurl,
                {
                    action: actions[ action ],
                    "_ajax_nonce": _SEARCHWP.nonce,
                    "plugin_file": elements.$btn.data( 'plugin' )
                },
                ( response ) => {
                    app.pluginActionBtnProcessResponse( response, elements );
                }
            );
        },

        /**
         * Process response for the plugin action button callback.
         *
         * @since 4.3.0
         */
        pluginActionBtnProcessResponse: ( response, elements ) => {

            if ( response.success ) {
                app.pluginActionBtnProcessResponseSuccess( response, elements );
            } else {
                app.pluginActionBtnProcessResponseFail( response, elements );
            }
        },

        /**
         * Process a success response for the plugin action button callback.
         *
         * @since 4.3.0
         */
        pluginActionBtnProcessResponseSuccess: ( response, elements ) => {

            if ( 'object' === typeof response.data ) {
                elements.$actions.hide();
                elements.$footer.append(
                    '<div class="swp-msg swp-flex--row swp-justify-center swp-flex--align-c">' +
                        '<div class="swp-card--p-bold swp-text-green">'
                            + response.data.msg +
                    '</div></div>'
                );
            }
            setTimeout(
        () => {
                    elements.$btn.removeAttr('disabled').removeClass('swp-button--processing');
                    elements.$footer.find( '.swp-msg' ).remove();
                    elements.$action.hide();
                    elements.$actions.find( `.swp-plugin-status-${response.data.showStatus}` ).show();
                    elements.$actions.show();
                },
        3000
            );
        },

        /**
         * Process a fail response for the plugin action button callback.
         *
         * @since 4.3.0
         */
        pluginActionBtnProcessResponseFail: ( response, elements ) => {

            elements.$actions.hide();

            if ( 'object' === typeof response.data ) {
                elements.$footer.append(
                    '<div class="swp-msg swp-flex--row swp-justify-center swp-flex--align-c">' +
                        '<div class="swp-card--p swp-text-red">'
                            + _SEARCHWP.error_strings.plugin_error +
                    '</div></div>'
                );
            } else {
                elements.$footer.append(
                    '<div class="swp-msg swp-flex--row swp-justify-center swp-flex--align-c">' +
                        '<div class="swp-card--p swp-text-red">'
                            + response.data +
                    '</div></div>'
                );
            }
            setTimeout(
        () => {
                    elements.$btn.removeAttr('disabled').removeClass('swp-button--processing');
                    elements.$footer.find( '.swp-msg' ).remove();
                    elements.$actions.show();
                },
        3000
            );
        },
    };

    app.init();

    window.searchwp = window.searchwp || {};

    window.searchwp.AdminAboutUsPage = app;

}( jQuery ) );
