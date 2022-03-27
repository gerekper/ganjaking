function SetEntryCreationConditionalLogic( isChecked ) {
	form.entryCreation = isChecked ? { conditionalLogic : new ConditionalLogic() } : null;
}

jQuery( document ).ready(
	function ( $ ) {

		gform.addFilter( 'gform_conditional_logic_description', function ( str, descPieces, objectType, obj ) {

			if ( objectType === 'entry_creation' ) {
				delete descPieces.actionType;
				descPieces.objectDescription = 'Disable entry creation if';
				var descPiecesArr            = makeArray( descPieces );
				return descPiecesArr.join( ' ' );
			}

			return str;
		} );

		gform.addFilter( 'gform_conditional_object', function ( object, objectType ) {

			if ( objectType !== 'entry_creation' ) {
				return object;
			}

			if ( typeof form.entryCreation === 'undefined' ) {
				form.entryCreation                  = {};
				form.entryCreation.conditionalLogic = new ConditionalLogic();
			}

			return form.entryCreation;
		} );

		if ( typeof form.entryCreation !== 'undefined'
			&& form.entryCreation.conditionalLogic !== null
			&& typeof form.entryCreation.conditionalLogic.logicType !== 'undefined'
		) {
			$( '#entry_creation_conditional_logic' ).prop( 'disabled', false ).prop( 'checked', true );
		}

		ToggleConditionalLogic( 'entry_creation' );

	}
);
