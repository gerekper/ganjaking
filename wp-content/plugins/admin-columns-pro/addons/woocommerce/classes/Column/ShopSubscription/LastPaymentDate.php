<?php

namespace ACA\WC\Column\ShopSubscription;

use AC;
use ACA\WC\Search;
use ACP;

/**
 * @since 3.4
 */
class LastPaymentDate extends AC\Column
	implements ACP\Export\Exportable {

	public function __construct() {
		$this->set_type( 'last_payment_date' )
		     ->set_original( true );
	}

	public function get_value( $id ) {
		return null;
	}

	public function export() {
		return new ACP\Export\Model\RawValue( $this );
	}

	public function get_raw_value( $id ) {
		$subscription = wcs_get_subscription( $id );
		$date = $subscription->get_date( 'last_order_date_created' );

		if ( ! $date ) {
			return '';
		}

		return $date;
	}

}