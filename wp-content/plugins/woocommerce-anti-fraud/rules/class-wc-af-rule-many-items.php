<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WC_AF_Rule_Many_Items extends WC_AF_Rule {

	/**
	 * The constructor
	 */
	public function __construct() {
		parent::__construct( 'many_items', sprintf( 'Order has more items than %s times the total items in shop.', apply_filters( 'wc_af_many_items_multiplier', 1.5 ) ), 2 );
	}

	/**
	 * Do the required check in this method. The method must return a boolean.
	 *
	 * @param WC_Order $order
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return bool
	 */
	public function is_risk( WC_Order $order ) {
		global $wpdb;

		// Default risk is false
		$risk = false;

		// Get the COUNT of total products
		$total_products = $wpdb->get_var( "SELECT COUNT(`ID`) FROM $wpdb->posts WHERE `post_type` = 'product' AND `post_status` = 'publish' ;" );

		// Check if the order total is higher than 2 times the average order total
		if ( $order->get_item_count() > ( $total_products * apply_filters( 'wc_af_many_items_multiplier', 1.5 ) ) ) {
			$risk = true;
		}

		return $risk;
	}

}