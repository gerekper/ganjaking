(function ($) {
    // Date Picker
	$( document.body ).on( 'wcmv-init-datepickers', function() {
        var vacation_start_date = $( "#vacation-start-date"),
            vacation_end_date   = $( "#vacation-end-date" );

		vacation_start_date.datepicker({
			dateFormat: 'yy-mm-dd',
			numberOfMonths: 1,
			showButtonPanel: true,
            minDate: 0,
            onClose: function( selectedDate ) {
                vacation_end_date.datepicker("option", "minDate", selectedDate);
            }
		});

        vacation_end_date.datepicker({
			dateFormat: 'yy-mm-dd',
			numberOfMonths: 1,
			showButtonPanel: true,
            onClose: function( selectedDate ) {
                vacation_start_date.datepicker( "option", "maxDate", selectedDate );
            }
		});
	}).trigger( 'wcmv-init-datepickers' );
}(jQuery));
