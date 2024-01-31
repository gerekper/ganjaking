/* global _SEARCHWP */

( function($) {

    'use strict';

    const app = {

        /**
         * Init.
         *
         * @since 4.2.8
         */
        init: () => {

            $( app.ready );
        },

        /**
         * Document ready
         *
         * @since 4.2.8
         */
        ready: () => {

            app.events();
        },

        /**
         * Extension page events.
         *
         * @since 4.2.8
         */
        events: () => {

            const $notificationsPanel = $( '.swp-notifications-panel--wrapper' );

            const showNotificationsPanelOnHash = () => {
                if ( window.location.hash === '#notifications' ) {
                    $notificationsPanel.show();
                }
            };

            showNotificationsPanelOnHash();

            $( window ).on( 'hashchange', showNotificationsPanelOnHash );

            $( '.searchwp-admin-menu-notification-indicator' ).parent()
                .on( 'click', showNotificationsPanelOnHash );

            $( '#swp-notifications-page-header-button' )
                .on( 'click', () => $notificationsPanel.show() );

            $( '.swp-notifications-panel--close' )
                .on( 'click', () => $notificationsPanel.hide() );

            $( '.swp-notifications-backdrop' )
                .on( 'click', () => $notificationsPanel.hide() );

            $( '.swp-notification--dismiss' ).on( 'click', app.dismiss );
        },

        /**
         * Dismiss notification.
         *
         * @since 4.2.8
         */
        dismiss: ( event ) => {

            const $el = $( event.target );

            // AJAX call - update option.
            const data = {
                _ajax_nonce: _SEARCHWP.nonce,
                action: _SEARCHWP.prefix + 'notification_dismiss',
                id: $el.data( 'id' ),
            };

            const $notification = $el.closest( '.swp-notifications--notification' );

            const handleResponse = ( res ) => {
                if ( res.success ) {
                    $notification.fadeOut(
                        100,
                        () => {
                            $notification.remove();
                            app.updateNotificationCount();
                        }
                    );
                }
            };

            $.post( ajaxurl, data, handleResponse );
        },

        /**
         * Update notification count in various places or reload the page if no notifications left.
         *
         * @since 4.2.8
         */
        updateNotificationCount: () => {

            const notificationsCount = $( '.swp-notifications-panel--notifications .swp-notifications--notification' ).length;

            $( '.searchwp-branding-bar__actions-button-count' ).text( notificationsCount );
            $( '#wp-admin-bar-searchwp .searchwp-menu-notification-counter' ).text( notificationsCount );
            $( '.swp-notifications-panel--header span span' ).text( notificationsCount );

            if ( notificationsCount !== 0 ) {
                return;
            }

			$( '.swp-notifications--empty-notifications-message' ).show();

            if ( window.location.hash ) {
                window.location.hash = '';
            }

            location.reload();
        }
    };

    app.init();

    window.searchwp = window.searchwp || {};

    window.searchwp.AdminNotifications = app;

}( jQuery ) );
