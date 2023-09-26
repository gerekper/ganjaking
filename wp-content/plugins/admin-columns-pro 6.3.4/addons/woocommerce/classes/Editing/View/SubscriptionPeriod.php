<?php

namespace ACA\WC\Editing\View;

use ACP;

class SubscriptionPeriod extends ACP\Editing\View {

	public function __construct( $interval_options, $period_options ) {
		parent::__construct( 'wc_subscription_period' );

		$this->set( 'interval_options', $interval_options )
		     ->set( 'period_options', $period_options );
	}

}
