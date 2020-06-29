jQuery(function($){

	$('table.paymentplans').on( 'click', 'a.delete_plan', function( e ) {
		var answer = confirm( wc_deposits_payment_plans_params.i18n_delete_plan );
		if ( answer ) {
			return true;
		}
		return false;
	} );

	$('table.wc-deposits-plan')
		.on( 'click', '.add-row', function() {
			var $table       = $('table.wc-deposits-plan');
			var $current_row = $(this).closest('tr');
			$current_row.after( $table.data( 'row' ) );
			$('table.wc-deposits-plan').find( '.plan_interval_amount, .plan_amount' ).change();
			return false;
		})
		.on( 'click', '.remove-row', function() {
			var $current_row = $(this).closest('tr');
			$current_row.remove();
			$('table.wc-deposits-plan').find( '.plan_interval_amount, .plan_amount' ).change();
			return false;
		})
		.on( 'change input', '.plan_amount', function() {
			var $table = $('table.wc-deposits-plan');
			var total  = 0;
			$table.find('.plan_amount').each(function(){
				total = total + parseFloat( ( $(this).val() || 0 ) );
			});
			total = Math.round( total * 10) / 10;
			$table.find('.total_percent').text( total );
		})
		.on( 'change input', '.plan_interval_amount, .plan_interval_unit', function() {
			var $table          = $('table.wc-deposits-plan');
			var $total_duration = $table.find('.total_duration');
			var total           = 0;
			var duration        = [];
			var years           = 0;
			var months          = 0;
			var days            = 0;
			var has_duration    = false;

			$table.find('.plan_interval_amount').each(function(){
				var unit     = $(this).closest( 'tr' ).find('.plan_interval_unit').val();
				var amount   = parseInt( $(this).val() );

				if ( 'day' === unit ) {
					days = days + amount;
				}
				if ( 'week' === unit ) {
					days = days + ( amount * 7 );
				}
				if ( 'year' === unit ) {
					years = years + amount;
				}
				if ( 'month' === unit ) {
					months = months + amount;
				}
			});

			if ( years ) {
				duration.push( years + ' ' + $total_duration.data('years') );
				has_duration = true;
			}
			if ( months ) {
				duration.push( months + ' ' + $total_duration.data('months') );
				has_duration = true;
			}
			if ( days || ! has_duration ) {
				duration.push( days + ' ' + $total_duration.data('days') );
			}

			$total_duration.text( duration.join(', ') );
		});

	$('table.wc-deposits-plan').find( '.plan_interval_amount, .plan_amount' ).change();
});