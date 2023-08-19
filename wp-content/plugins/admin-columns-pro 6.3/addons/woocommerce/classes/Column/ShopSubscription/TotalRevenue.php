<?php

namespace ACA\WC\Column\ShopSubscription;

use AC;
use ACA\WC\Search;

/**
 * @since 3.4
 */
class TotalRevenue extends AC\Column {

	public function __construct() {
		$this->set_type( 'column-wc-subscription_revenue' )
		     ->set_label( __( 'Total Revenue', 'codepress-admin-columns' ) )
		     ->set_group( 'woocommerce' );
	}

	public function get_value( $id ) {
		return wc_price( $this->get_raw_value( $id ) );
	}

	public function get_raw_value( $id ) {
		$subscription = wcs_get_subscription( $id );
		$total = 0;

		foreach ( $subscription->get_related_orders() as $order_id ) {
			$total += (float) get_post_meta( $order_id, '_order_total', true );
		}

		return $total;
	}

}