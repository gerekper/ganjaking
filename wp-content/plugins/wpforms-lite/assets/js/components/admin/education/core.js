/* global wpforms_education */
/**
 * WPForms Education Core.
 *
 * @since 1.6.6
 */

'use strict';

var WPFormsEducation = window.WPFormsEducation || {};

WPFormsEducation.core = window.WPFormsEducation.core || ( function( document, window, $ ) {

	/**
	 * Public functions and properties.
	 *
	 * @since 1.6.6
	 *
	 * @type {object}
	 */
	var app = {

		/**
		 * Start the engine.
		 *
		 * @since 1.6.6
		 */
		init: function() {

			$( app.ready );
		},

		/**
		 * Document ready.
		 *
		 * @since 1.6.6
		 */
		ready: function() {

			app.events();
		},

		/**
		 * Register JS events.
		 *
		 * @since 1.6.6
		 */
		events: function() {

			app.dismissEvents();
		},

		/**
		 * Dismiss button events.
		 *
		 * @since 1.6.6
		 */
		dismissEvents: function() {

			$( '.wpforms-dismiss-container' ).on( 'click', '.wpforms-dismiss-button', function( e ) {

				var $this = $( this ),
					$cont = $this.closest( '.wpforms-dismiss-container' ),
					$out = $cont.find( '.wpforms-dismiss-out' ),
					data = {
						action: 'wpforms_education_dismiss',
						nonce: wpforms_education.nonce,
						section: $this.data( 'section' ),
					};

				if ( $out.length > 0 ) {
					$out.addClass( 'out' );
					setTimeout(
						function() {
							$cont.remove();
						},
						300
					);
				} else {
					$cont.remove();
				}

				$.post( wpforms_education.ajax_url, data );
			} );
		},
	};

	// Provide access to public functions/properties.
	return app;

}( document, window, jQuery ) );

WPFormsEducation.core.init();
