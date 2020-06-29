jQuery( document ).ready( function( $ ) {
	'use strict';

	var opcSelectWoo = {

		/**
		 * Initialize our functions to the appropriate events.
		 */
		init: function() {
			$( document.body )
				.on( 'wcopc-enhanced-select-init', this.selectInit )
				.trigger( 'wcopc-enhanced-select-init' );
		},

		/**
		 * Initialize the overrideWoo function on the appropriate elements.
		 */
		selectInit: function() {
			$( ':input.wcopc-category-search' )
				.filter( ':not(.opc-enhanced)' )
				.each( opcSelectWoo.overrideWoo );
		},

		/**
		 * Override the Woo defaults for a Select2 element.
		 *
		 * We want term IDs to be returned, rather than term slugs. This re-initializes
		 * the select element using a modified function to process the ajax results.
		 */
		overrideWoo: function() {
			var $this = $( this ),
				overrides,
				selectWooOptions = $this.data( 'select2' ).options.options;

			if ( 'undefined' === typeof selectWooOptions ) {
				return;
			}

			overrides = $.extend( true, selectWooOptions, {
				ajax: {
					processResults: opcSelectWoo.processResults
				}
			} );

			$this.selectWoo( overrides ).addClass( 'opc-enhanced' );
		},

		/**
		 * Process the ajax results into usable term data.
		 *
		 * @param {Object} data
		 * @returns {{results: Array}}
		 */
		processResults: function( data ) {
			var terms = [];
			if ( data ) {
				$.each( data, function( id, term ) {
					terms.push( {
						id: term.term_id,
						text: term.formatted_name
					} );
				} );
			}

			return {
				results: terms
			};
		}
	};

	try {
		opcSelectWoo.init();
	} catch( err ) {
		// If select2 failed, log the error but don't stop other scripts.
		window.console.log( err );
	}
} );
