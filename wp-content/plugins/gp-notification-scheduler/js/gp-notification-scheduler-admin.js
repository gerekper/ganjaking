( function( $ ) {

	function toggleType( type ) {
		$( '.gpns-type-settings' ).hide();
		$( '#gpns-{0}-settings'.format( type ) ).show();

		toggleRecurring();
	}

	function toggleRecurring() {

		var $recurring       = $( '#gpns-enable-recurring' ),
			$scheduleSection = $( '#gpns-recurring-schedule-section' ),
			$endingSection   = $( '#gpns-recurring-ending-section' ),
			$ending          = $( '#gpns-recurring-ending' ),
			$scheduleType 	 = $('[name="_gform_setting_scheduleType"]'),
			scheduleType 	 = $scheduleType.filter(':checked').val(),
			isEnabled        = $recurring.is( ':checked' ) && $scheduleType.filter(':checked').val() !== 'immediate',
			hasEnding        = $ending.val() !== 'never';

		if (scheduleType === 'immediate') {
			$('#gpns-recurring').hide();
		} else {
			$('#gpns-recurring').show();
		}

		if ( isEnabled ) {
			$scheduleSection.show();
			if ( hasEnding ) {
				$endingSection.show();
			} else {
				$endingSection.hide();
			}
		} else {
			$scheduleSection.hide();
			$endingSection.hide();
		}

	}

	$( document ).ready( function() {

		var $types     = $( 'input[name="_gform_setting_scheduleType"]' ),
			$date      = $( 'input[name="_gform_setting_scheduleDate"]' ),
			$recurring = $( '#gpns-enable-recurring' ),
			$ending    = $( '#gpns-recurring-ending' );

		$types.click( function() {
			toggleType( $( this ).val() );
		} );

		$date.datepicker( {
			dateFormat: 'yy-mm-dd'
		} );

		$().add( $recurring ).add( $ending ).change( function() {
			toggleRecurring();
		} );

		toggleRecurring();

	} );

} )( jQuery );
