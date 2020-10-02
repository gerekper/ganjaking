var sfn_ajax_search = null;
jQuery( document ).ready( function( $ ) {

	// Use selectWoo if it exists.
	if ( $().selectWoo ) {

		sfn_ajax_search = function(){
			$( '.sfn-product-search' ).filter( ':not(.enhanced)' ).each( function() {
				var select2_args = {
					allowClear:  $( this ).data( 'allow_clear' ) ? true : false,
					placeholder: $( this ).data( 'placeholder' ),
					minimumInputLength: $( this ).data( 'minimum_input_length' ) ? $( this ).data( 'minimum_input_length' ) : '3',
					escapeMarkup: function( m ) {
						return m;
					},
					language: {
						errorLoading: function() {
							return sfn_product_search.errorLoading;
						}
					},
					ajax: {
						url:         ajaxurl,
						dataType:    'json',
						quietMillis: 250,
						data: function( term, page ) {
							return {
								term:     term.term,
								action:   $( this ).data( 'action' ) || 'woocommerce_json_search_products_and_variations',
								security: sfn_product_search.security
							};
						},
						processResults: function( data, page ) {
							var terms = [];
							if ( data ) {
								$.each( data, function( id, text ) {
									terms.push( { id: id, text: text } );
								});
							}
							return { results: terms };
						},
						cache: true
					}
				};

				$( this ).selectWoo( select2_args ).addClass( 'enhanced' );
			} );
		}
	}

	sfn_ajax_search();

} );
