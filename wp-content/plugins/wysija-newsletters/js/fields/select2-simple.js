(function( $, _ ){
'use strict';

$( document ).ready(function(){
	var $fields = $( '.mailpoet-field-select2-simple' );

	// Setup Select2
	$fields.each(function( k, field ){
		var $field = $( field );

		$field.select2({
			placeholder: $field.data( 'placeholder' ),
			allowClear: true,
			multiple: $field.data( 'multiple' ),
			minimumResultsForSearch: $field.data( 'minimumResultsForSearch' ),
			width: $field.width()
		});
	});
});

}( jQuery, _ ));