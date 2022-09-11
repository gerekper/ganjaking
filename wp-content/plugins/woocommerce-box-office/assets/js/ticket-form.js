/* globals jQuery, wcBOTicketFormParams */
jQuery( document ).ready( function() {
	if ( wcBOTicketFormParams && wcBOTicketFormParams.requiredCheckboxes ) {
		if ( ! wcBOTicketFormParams.multipleTickets ) {
			// Add Event listener for single ticket.
			wcBOCheckboxesRequired( 0 );
		}
	}
} );

/**
 * Handle Checkbox group required.
 *
 * @param {number} index Ticket index
 */
function wcBOCheckboxesRequired( index ) {
	if ( wcBOTicketFormParams && wcBOTicketFormParams.requiredCheckboxes ) {
		let fieldPrefix = wcBOTicketFormParams.fieldPrefix || 'ticket_fields[0]';
		fieldPrefix     = fieldPrefix.replace('[0]', '['+index+']');
		wcBOTicketFormParams.requiredCheckboxes.forEach( function( element ) {
			const requiredCheckboxes = jQuery('input[type="checkbox"][name="'+fieldPrefix+'['+element+'][]');
			// Setup on Change event on checkboxes.
			requiredCheckboxes.on( 'change', function(){
				wcBoxOfficeProcessCheckboxes( requiredCheckboxes );
			} );
			// Initially Trigger change to update required attribute of checkboxes accordingly.
			wcBoxOfficeProcessCheckboxes( requiredCheckboxes );			
		} );
	}
}

/**
 * Update required attribute of checkboxes.
 *
 * @param {Object} requiredCheckboxes 
 */
function wcBoxOfficeProcessCheckboxes( requiredCheckboxes ) {
	if ( requiredCheckboxes.is( ':checked' ) ) {
		requiredCheckboxes.removeAttr( 'required' );
		// Clear custom validity message.
		if ( requiredCheckboxes[0] ) {
			requiredCheckboxes[0].setCustomValidity( '' );
		}
	} else {
		// Set custom validity message.
		if ( requiredCheckboxes[0] && wcBOTicketFormParams.checkboxValidationMsg ) {
			requiredCheckboxes[0].setCustomValidity( wcBOTicketFormParams.checkboxValidationMsg );
		}
		requiredCheckboxes.attr( 'required', 'required' );
	}
}