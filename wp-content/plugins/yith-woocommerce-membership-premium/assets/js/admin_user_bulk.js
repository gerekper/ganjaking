jQuery( function ( $ ) {
	/* ======================================
	 *             Bulk User Edit
	 * ===================================== */
	var plansFilter = $( '.filter_by_membership_plan' ),
		bulkAction  = $( '.membership_editing_bulk_action' ),
		bulkPlan    = $( '.membership_editing_action_plan' );

	plansFilter.on( 'change', function () {
		plansFilter.val( $( this ).val() );
	} );

	bulkAction.on( 'change', function () {
		var value = $( this ).val();
		bulkAction.val( value );

		if ( 'delete_history' === value ) {
			bulkPlan.hide();
		} else {
			bulkPlan.show();
		}
	} );

	bulkPlan.on( 'change', function () {
		bulkPlan.val( $( this ).val() );
	} );

} );
