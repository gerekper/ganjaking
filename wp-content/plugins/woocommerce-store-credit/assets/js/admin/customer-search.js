/**
 * Customer search.
 *
 * @package WC_Store_Credit/Assets/Js/Admin
 * @since   3.1.0
 */

(function( $ ) {

	'use strict';

	/**
	 * Email Validation.
	 */
	function isEmail( email ) {
		return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test( email );
	}

	$( function() {
		var wc_store_credit_customer_search = {

			init: function() {
				// Enhanced selects script is initialized in head.
				this.enhanceCustomerSearch();
			},

			enhanceCustomerSearch: function () {
				$( ':input.wc-customer-search' ).filter( '.enhanced' ).each( function() {
					var options = $( this ).data( 'select2' ).options.options;

					// Tags option not enabled.
					if ( ! options.tags ) {
						return;
					}

					// Adds restrictions for creating tags.
					options.createTag = function ( params ) {
						// Don't create a tag if it isn't an email.
						if ( ! isEmail( params.term ) ) {
							return null;
						}

						return {
							id: params.term,
							text: params.term
						};
					};

					// Re-initialize the select.
					$( this ).select2( 'destroy' ).select2( options );
				});
			}
		};

		wc_store_credit_customer_search.init();
	});
})( jQuery );
