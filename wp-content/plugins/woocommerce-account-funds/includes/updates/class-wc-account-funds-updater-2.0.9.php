<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_Account_Funds_Updater_2_0_9 implements WC_Account_Funds_Updater {

	/**
	 * {@inheritdoc}
	 */
	public function update() {
		$orders = $this->_get_renewal_orders_paid_with_af();

		foreach ( $orders as $order ) {
			$funds_used      = get_post_meta( $order->ID, '_funds_used', true );
			$recurring_total = $this->_get_recurring_total( $order->post_parent );
			$order_total     = get_post_meta( $order->ID, '_order_total', true );

			if ( $order_total > 0 ) {
				update_post_meta( $order->ID, '_order_total', 0 );
			}

			if ( $funds_used !== $recurring_total ) {
				update_post_meta( $order->ID, '_funds_used', $recurring_total );
			}
		}
	}

	/**
	 * Gets subscription renewal orders which original order paid with account
	 * funds
	 *
	 * @return array List of renewal orders
	 */
	private function _get_renewal_orders_paid_with_af() {
		return get_posts( array(
			'post_type'   => 'shop_subscription',
			'meta_query'  => array(
				array(
					'key'   => '_funds_removed',
					'value' => '1',
				),
				array(
					'key'     => '_funds_used',
					'value'   => '0',
					'compare' => '>',
				)
			),
			'post_status'         => 'any',
			'nopaging'            => true,
			'post_parent__not_in' => array( '0' ),
		) );
	}

	/**
	 * Get recurring total.
	 *
	 * @param int $parent_id Order parent ID
	 *
	 * @return string Recurring total
	 */
	private function _get_recurring_total( $parent_id ) {
		if ( version_compare( WC_Subscriptions::$version, '2.0.0', '<' ) ) {
			$recurring_total = get_post_meta( $parent_id, '_order_recurring_total', true );
		} else {
			$recurring_total = get_post_meta( $parent_id, '_wcs_migrated_order_recurring_total', true );
		}

		return $recurring_total;
	}
}

return new WC_Account_Funds_Updater_2_0_9();
