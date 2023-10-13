<?php

namespace WCML\Compatibility\TheEventsCalendar;

class MulticurrencyHooks implements \IWPML_Action {

	public function add_hooks() {
		if ( ! is_admin() ) {
			add_filter( 'tribe_events_cost_unformatted', [ $this, 'convert_events_cost' ], 10, 1 );
		}
	}

	/**
	 * @param float $cost
	 *
	 * @return float
	 */
	public function convert_events_cost( $cost ) {
		return apply_filters( 'wcml_raw_price_amount', $cost );
	}

}
