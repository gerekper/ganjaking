<?php
/**
 * Class that updates DB in 2.1.3.
 *
 * @package WC_Account_Funds
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Updater for 2.1.3.
 *
 * This updates order item meta as WC 3.0 will invalidate order item with
 * invaild product ID. Before 2.1.3, AF stores `_product_id` with value sets to
 * page id of my-account page.
 */
class WC_Account_Funds_Updater_2_1_3 implements WC_Account_Funds_Updater {

	/**
	 * {@inheritdoc}
	 */
	public function update() {
		global $wpdb;

		// If we don't have myaccount page, no need to proceed.
		if ( wc_get_page_id( 'myaccount' ) <= 0 ) {
			return;
		}

		// Add new AF top-up order item meta.
		$res = $wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$wpdb->prefix}woocommerce_order_itemmeta " .
					'( order_item_id, meta_key, meta_value ) ' .
				'SELECT ids.order_item_id, "_top_up_amount", totals.meta_value ' .
					"FROM {$wpdb->prefix}woocommerce_order_itemmeta as ids " .
				"LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta as totals " .
					'ON ids.order_item_id = totals.order_item_id ' .
				'WHERE ' .
					'ids.meta_key = "_product_id" AND ' .
					'ids.meta_value = %d AND ' .
					'totals.meta_key = "_line_subtotal"',
				wc_get_page_id( 'myaccount' )
			)
		);
		if ( ! $res ) {
			return;
		}

		// Add item meta to indicate that this is top-up product.
		$res = $wpdb->query(
			$wpdb->prepare(
				"INSERT INTO {$wpdb->prefix}woocommerce_order_itemmeta " .
					'( order_item_id, meta_key, meta_value ) ' .
				'SELECT order_item_id, "_top_up_product", "yes" ' .
					"FROM {$wpdb->prefix}woocommerce_order_itemmeta " .
				'WHERE ' .
					'meta_key = "_product_id" AND ' .
					'meta_value = %d',
				wc_get_page_id( 'myaccount' )
			)
		);
		if ( ! $res ) {
			return;
		}

		// Updates all product item ID to 0.
		$wpdb->query(
			$wpdb->prepare(
				"UPDATE {$wpdb->prefix}woocommerce_order_itemmeta " .
				'SET meta_value = 0 ' .
				'WHERE meta_key = "_product_id" AND meta_value = %d',
				wc_get_page_id( 'myaccount' )
			)
		);
	}
}

return new WC_Account_Funds_Updater_2_1_3();
