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
         * Page events.
         *
         * @since 4.3.0
         */
        events: () => {

			$( '#swp-license-activate' ).on( 'click', app.activateLicense );
			$( '#swp-license-deactivate' ).on( 'click', app.deactivateLicense );
        },

		/**
		 * Callback for clicking "Activate License" button.
		 *
		 * @since 4.3.0
		 */
        activateLicense: function(e) {

			e.preventDefault();

			$( '#swp-license-error-msg' ).hide().empty();

			$( '.swp-content-container button' ).attr( 'disabled','disabled' );
			$( '#swp-license-activate' ).addClass( 'swp-button--processing' );

			$.post(
				ajaxurl,
				{
					_ajax_nonce: _SEARCHWP.nonce,
					action: _SEARCHWP.prefix + 'license_activate',
					license_key: $( '#swp-license' ).val(),
				},
				app.activateLicenseProcessResponse
			);
		},

		/**
		 * Callback for clicking "Deactivate License" button.
		 *
		 * @since 4.3.0
		 */
		deactivateLicense: function(e) {

			e.preventDefault();

			$( '#swp-license-error-msg' ).hide().empty();

			$( '.swp-content-container button' ).attr( 'disabled','disabled' );
			$( '#swp-license-deactivate' ).addClass( 'swp-button--processing' );

			$.post(
				ajaxurl,
				{
					_ajax_nonce: _SEARCHWP.nonce,
					action: _SEARCHWP.prefix + 'license_deactivate',
					license_key: $( '#swp-license' ).val(),
				},
				app.deactivateLicenseProcessResponse
			);
		},

		/**
		 * Process response for the "Activate License" button callback.
		 *
		 * @since 4.3.0
		 */
		activateLicenseProcessResponse: ( response ) => {

			if ( response.success && 'valid' === response.data.status ) {
				location.reload();
			}

			if ( ! response.success ) {
				$( '.swp-content-container button' ).removeAttr( 'disabled' );
				$( '#swp-license-activate' ).removeClass( 'swp-button--processing' );
				$( '#swp-license-error-msg' ).show().text( typeof response.data === 'string' ? response.data : 'There was a problem activating your license.' );
			}
		},

		/**
		 * Process response for the "Deactivate License" button callback.
		 *
		 * @since 4.3.0
		 */
		deactivateLicenseProcessResponse: ( response ) => {

			if ( response.success && 'deactivated' === response.data.status ) {
				location.reload();
			}

			if ( ! response.success ) {
				$( '.swp-content-container button' ).removeAttr( 'disabled' );
				$( '#swp-license-deactivate' ).removeClass( 'swp-button--processing' );
				$( '#swp-license-error-msg' ).show().text( typeof response.data === 'string' ? response.data : 'There was a problem deactivating your license.' );
			}
		},
    };

    app.init();

    window.searchwp = window.searchwp || {};

    window.searchwp.AdminGeneralSettingsPage = app;

}( jQuery ) );
