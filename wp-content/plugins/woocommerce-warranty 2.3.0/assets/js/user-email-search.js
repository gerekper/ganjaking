/*global wc_enhanced_select_params, user_email_search */
jQuery( function( $ ) {

	function getEnhancedSelectFormatString() {
		var formatString = {
			noResults: function() {
				return wc_enhanced_select_params.i18n_no_matches;
			}, errorLoading: function() {
				return wc_enhanced_select_params.i18n_ajax_error;
			}, inputTooShort: function( args ) {
				var remainingChars = args.minimum - args.input.length;

				if ( 1 === remainingChars ) {
					return wc_enhanced_select_params.i18n_input_too_short_1;
				}

				return wc_enhanced_select_params.i18n_input_too_short_n.replace( '%qty%', remainingChars );
			}, inputTooLong: function( args ) {
				var overChars = args.input.length - args.maximum;

				if ( 1 === overChars ) {
					return wc_enhanced_select_params.i18n_input_too_long_1;
				}

				return wc_enhanced_select_params.i18n_input_too_long_n.replace( '%qty%', overChars );
			}, maximumSelected: function( args ) {
				if ( 1 === args.maximum ) {
					return wc_enhanced_select_params.i18n_selection_too_long_1;
				}

				return wc_enhanced_select_params.i18n_selection_too_long_n.replace( '%qty%', args.maximum );
			}, loadingMore: function() {
				return wc_enhanced_select_params.i18n_load_more;
			}, searching: function() {
				return wc_enhanced_select_params.i18n_searching;
			},
		};
	}

	$( 'body' ).on( 'wc-enhanced-select-init', function() {

		$( '.wc-user-search' ).filter( ':not(.enhanced)' ).each( function() {
			var select2_args = {
				allowClear: $( this ).data( 'allow_clear' ) ? true : false,
				placeholder: $( this ).data( 'placeholder' ),
				minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this )
					.data( 'minimum_input_length' ) : '3',
				escapeMarkup: function( m ) {
					return m;
				},
				ajax: {
					url: wc_enhanced_select_params.ajax_url,
					dataType: 'json',
					quietMillis: 250,
					data: function( term, page ) {
						return {
							term: term.term,
							action: $( this ).data( 'action' ) || 'warranty_user_search',
							security: user_email_search_params.user_search_nonce,
						};
					},
					processResults: function( data, page ) {
						var terms = [];
						if ( data ) {
							$.each( data, function( id, text ) {
								terms.push( { id: id, text: text } );
							} );
						}
						return { results: terms };
					},
					cache: true,
				},
			};

			select2_args = $.extend( select2_args, getEnhancedSelectFormatString() );

			$( this ).selectWoo( select2_args ).addClass( 'enhanced' );
		} );

		$( '.email-search-select' ).filter( ':not(.enhanced)' ).each( function() {
			var select2_args = {
				allowClear: $( this ).data( 'allow_clear' ) ? true : false,
				placeholder: $( this ).data( 'placeholder' ),
				dropdownAutoWidth: 'true',
				minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this )
					.data( 'minimum_input_length' ) : '3',
				escapeMarkup: function( m ) {
					return m;
				},
				ajax: {
					url: ajaxurl, dataType: 'json', quietMillis: 250, data: function( term, page ) {
						return {
							term: term.term,
							action: $( this ).data( 'action' ) || 'warranty_search_for_email',
							security: user_email_search_params.search_for_email_nonce,
						};
					}, processResults: function( data, page ) {
						var terms = [];
						if ( data ) {
							$.each( data, function( id, text ) {
								terms.push( { id: id, text: text } );
							} );
						}
						return { results: terms };
					}, cache: true,
				},
			};

			$( this ).selectWoo( select2_args ).addClass( 'enhanced' );
		} );
	} ).trigger( 'wc-enhanced-select-init' );

} );
