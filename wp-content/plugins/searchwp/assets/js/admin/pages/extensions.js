/* global _SEARCHWP */

( function($) {

    'use strict';

    const app = {

        /**
         * Init.
         *
         * @since 4.2.2
         */
        init: () => {

            $( app.ready );
        },

        /**
         * Document ready
         *
         * @since 4.2.2
         */
        ready: () => {

            app.events();
        },

        /**
         * Extension page events.
         *
         * @since 4.2.2
         */
        events: () => {

            app.extensionSearchEvents();
            app.extensionActionBtnEvents();
        },

        /**
         * Search field events.
         *
         * @since 4.2.2
         */
        extensionSearchEvents: () => {

            if ( ! $( '.extensions-list-allowed' ).length ) {
                return;
            }

            const extensionSearch = new List(
                'searchwp-extensions-list',
                { valueNames: [ 'extension-name' ] }
            );

            $( '#searchwp-admin-extensions-search' )
                .on( 'input', ( event ) => {
                    const searchTerm = $( event.currentTarget ).val();
                    extensionSearch.search( searchTerm );
                } );
        },

        /**
         * Extension action button events.
         *
         * @since 4.2.2
         */
        extensionActionBtnEvents: () => {

            $( '.searchwp-extension-install' ).on( 'click', ( event ) => {
                app.extensionActionBtnCallback( 'install', event );
            } );

            $( '.searchwp-extension-activate' ).on( 'click', ( event ) => {
                app.extensionActionBtnCallback( 'activate', event );
            } );

            $( '.searchwp-extension-deactivate' ).on( 'click', ( event ) => {
                app.extensionActionBtnCallback( 'deactivate', event );
            } );
        },

        /**
         * Extension action button callback.
         *
         * @since 4.2.2
         */
        extensionActionBtnCallback: ( action, event ) => {

            event.preventDefault();
            event.stopImmediatePropagation();

            const elements = {
                $btn: $( event.currentTarget ),
            };

            if ( elements.$btn.hasClass( 'disabled' ) ) {
                return;
            }

            elements.$footer        = elements.$btn.closest( '.swp-card--footer' );
            elements.$action        = elements.$btn.closest( '.extension-action' );
            elements.$actions       = elements.$btn.closest( '.extension-actions' );
            elements.$buttonText    = elements.$btn.find( '.extension-action-button-text' );
            elements.$buttonSpinner = elements.$btn.find( '.swp-status--loading' );

            const actions = {
                install: `${_SEARCHWP.prefix}extension_install`,
                activate: `${_SEARCHWP.prefix}extension_activate`,
                deactivate: `${_SEARCHWP.prefix}extension_deactivate`,
            }

            if ( ! ( action in actions ) ) {
                return;
            }

            elements.$btn.addClass( 'disabled' );
            elements.$buttonText.hide();
            elements.$buttonSpinner.show();

            $.post(
                ajaxurl,
                {
                    action: actions[ action ],
                    "_ajax_nonce": _SEARCHWP.nonce,
                    "extension_slug": elements.$btn.data( "extension-slug" )
                },
                ( response ) => {
                    app.extensionActionBtnProcessResponse( response, elements );
                }
            );
        },

        /**
         * Process response for the extension action button callback.
         *
         * @since 4.2.2
         */
        extensionActionBtnProcessResponse: ( response, elements ) => {

            if ( response.success ) {
                app.extensionActionBtnProcessResponseSuccess( response, elements );
            } else {
                app.extensionActionBtnProcessResponseFail( response, elements );
            }
        },

        /**
         * Process a success response for the extension action button callback.
         *
         * @since 4.2.2
         */
        extensionActionBtnProcessResponseSuccess: ( response, elements ) => {

            if ( 'object' === typeof response.data ) {
                elements.$actions.hide();
                elements.$footer.append( '<div class="msg success">' + response.data.msg + '</div>' );
            }
            setTimeout(
        () => {
                    elements.$buttonText.show();
                    elements.$buttonSpinner.hide();
                    elements.$btn.removeClass( 'disabled' );
                    elements.$footer.find( '.msg' ).remove();
                    elements.$action.hide();
                    elements.$actions.find( `.extension-action-${response.data.show_action}` ).show();
                    elements.$actions.show();
                },
        3000
            );
        },

        /**
         * Process a fail response for the extension action button callback.
         *
         * @since 4.2.2
         */
        extensionActionBtnProcessResponseFail: ( response, elements ) => {

            if ( 'object' === typeof response.data ) {
                elements.$action.append( '<div class="msg error">' + _SEARCHWP.error_strings.extension_error + '</div>' );
            } else {
                elements.$action.append( '<div class="msg error">' + response.data + '</div>' );
            }
            setTimeout(
        () => {
                    elements.$buttonText.show();
                    elements.$buttonSpinner.hide();
                    elements.$btn.removeClass( 'disabled' );
                    elements.$action.find( '.msg' ).remove();
                },
        3000
            );
        },
    };

    app.init();

    window.searchwp = window.searchwp || {};

    window.searchwp.AdminExtensionsPage = app;

}( jQuery ) );
