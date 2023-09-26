<?php

namespace ACA\WC\Export\ShopOrder;

use ACP;

class Order implements ACP\Export\Service {

	private function get_customer_name( $order ): string {
		$first_name = $order->get_billing_first_name();
		$last_name = $order->get_billing_last_name();
		$company = $order->get_billing_company();

		if ( $order->get_customer_id() ) {
			return get_user_by( 'id', $order->get_customer_id() )->display_name;
		}
		if ( $first_name || $last_name ) {
			return $first_name . ' ' . $last_name;
		}
		if ( $company ) {
			return $company;
		}

		return __( 'guest', 'woocommerce' );
	}

	public function get_value( $id ) {
		$order = wc_get_order( $id );

		if ( ! $order ) {
			return '';
		}

		return sprintf(
			'%d (%s)',
			$order->get_order_number(),
			$this->get_customer_name( $order )
		);
	}

}