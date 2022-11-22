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
			$funds_used      = $order->get_meta( '_funds_used' );
			$recurring_total = $this->_get_recurring_total( $order->get_parent_id() );
			$order_total     = $order->get_total( 'edit' );

			if ( $order_total > 0 ) {
				$order->set_total( 0 );
			}

			if ( $funds_used !== $recurring_total ) {
				$order->update_meta_data( '_funds_used', $recurring_total );
			}

			$order->save();
		}
	}

	/**
	 * Gets subscription renewal orders which original order paid with account
	 * funds
	 *
	 * @return array List of renewal orders
	 */
	private function _get_renewal_orders_paid_with_af() {
		return wc_get_orders(
			array(
				'type'           => 'shop_subscription',
				'parent_exclude' => array( '0' ),
				'funds_query'    => array(
					array(
						'key'   => '_funds_removed',
						'value' => '1',
					),
					array(
						'key'     => '_funds_used',
						'value'   => '0',
						'compare' => '>',
					),
				),
			)
		);
	}

	/**
	 * Get recurring total.
	 *
	 * @param int $parent_id Order parent ID
	 *
	 * @return string Recurring total
	 */
	private function _get_recurring_total( $parent_id ) {
		$subscription    = wc_get_order( $parent_id );
		$recurring_total = $subscription->get_meta( '_wcs_migrated_order_recurring_total' );

		if ( ! $recurring_total ) {
			$recurring_total = $subscription->get_meta( '_order_recurring_total' );
		}

		return $recurring_total;
	}
}

return new WC_Account_Funds_Updater_2_0_9();
