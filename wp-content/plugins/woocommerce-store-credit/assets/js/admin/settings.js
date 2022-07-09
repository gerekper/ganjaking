/**
 * Settings
 *
 * @package WC_Store_Credit/Assets/Js/Admin
 * @since   4.2.0
 */

( function( $ ) {

	'use strict';

	$(function() {

		var wc_store_credit_settings = {

			formFields: {},

			/**
			 * Initialize actions.
			 */
			init: function() {
				this.bindEvents();
			},

			/**
			 * Bind events.
			 */
			bindEvents: function() {
				var that = this;

				this.getFormField( 'wc_store_credit_show_cart_notice' ).on( 'change', function() {
					that.toggleFormFields( ['wc_store_credit_cart_notice'], $( this ).prop( 'checked' ) )
				}).trigger( 'change' );
			},

			/**
			 * Gets the jQuery object which represents the form field.
			 */
			getFormField: function( id ) {
				// Load field on demand.
				if ( ! this.formFields[ id ] ) {
					this.formFields[ id ] = $( '#' + id );
				}

				return this.formFields[ id ];
			},

			/**
			 * Handles the visibility of the form fields.
			 */
			toggleFormFields: function( ids, visible ) {
				var that = this;

				if ( ! Array.isArray( ids ) ) {
					ids = [ ids ];
				}

				$.each( ids, function( index, id ) {
					that.getFormField( id ).closest( 'tr' ).toggle( visible );
				});
			},
		};

		wc_store_credit_settings.init();
	});
})( jQuery );
