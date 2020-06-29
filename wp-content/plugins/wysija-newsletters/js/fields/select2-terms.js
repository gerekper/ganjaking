(function( $, _ ){
'use strict';

$( document ).ready(function(){
	var $fields = $( '.mailpoet-field-select2-terms' );

	// Setup Select2
	$fields.each(function( k, field ){
		var $field = $( field );

		$field.select2( {
			width: $field.width(),
			multiple: $field.data( 'multiple' ),
			data: { results: [] },
			initSelection : function (element, callback) {
				callback( element.data( 'value' ) );
			},
			allowClear: true,
			ajax: { // instead of writing the function to execute the request we use Select2's convenient helper
				url: ajaxurl,
				data: function (term, page) {
					return {
						action: 'wysija_ajax',
						controller: 'campaigns',
						task: 'search_terms',
						search: term, // search term
						page_limit: 10,
						page: page,
						post_type: $( '#post_type' ).val()
					};
				},
				results: function (data, page) { // parse the results into the format expected by Select2.
					var information = data.result;
					$.each( information.results, function( k, result ){
						result.text = _.template('<%= tax %>: <%= term %>')( { tax: information.taxonomies[result.taxonomy].labels.singular_name, term: result.name } );
						result.id = result.term_id;
					} );
					return information;
				}
			},
		} );
	})
	.trigger( 'change' )
	.on({
		'change': function(e){
			var data = $( this ).data( 'value' );

			if ( e.added ){
				if ( _.isArray( data ) ) {
					data.push( e.added );
				} else {
					data = e.added;
				}
			} else {
				if ( _.isArray( data ) ) {
					data = _.without( data, e.removed );
				} else {
					data = '';
				}
			}
			$( this ).data( 'value', data ).attr( 'data-value', JSON.stringify( data ) );
		}
	});

});

}( jQuery, _ ));