<?php

class WoocommerceProductVendors {

	/**
	 * Add filters.
	 */
	public function run() {
		add_filter( 'woocommerce_gpf_feed_item_google', array( $this, 'add_shipping_elements' ), 10, 2 );
	}

	/**
	 * Add shipping elements to the feed for per-product prices if configured.
	 *
	 * @param WoocommerceGpfFeedItem $feed_item
	 * @param WC_Product $product
	 *
	 * @return WoocommerceGpfFeedItem
	 */
	public function add_shipping_elements( $feed_item, $product ) {
		global $wpdb;

		// Allow this to be turned off via filters.
		if ( ! apply_filters( 'woocommerce_gpf_include_pv_per_product_shipping', true, $product ) ) {
			return $feed_item;
		}

		// Fetch the per-product shipping rules.
		$rules = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT *
                   FROM ' . WC_PRODUCT_VENDORS_PER_PRODUCT_SHIPPING_TABLE . '
                  WHERE product_id = %d
               ORDER BY rule_order',
				$product->get_id()
			)
		);

		// Do nothing if no per-product rules defined.
		if ( empty( $rules ) ) {
			return $feed_item;
		}

		// Output the rules.
		foreach ( $rules as $rule ) {
			$google_rule = [];
			if ( ! empty( $rule->rule_country ) ) {
				$google_rule['country'] = $rule->rule_country;
			}
			if ( ! empty( $rule->rule_state ) ) {
				$google_rule['region'] = $rule->rule_state;
			}
			if ( ! empty( $rule->rule_postcode ) ) {
				$google_rule['postal_code'] = $rule->rule_postcode;
			}
			$price = 0;
			if ( ! empty( $rule->rule_item_cost ) ) {
				$price += $rule->rule_item_cost;
			}
			if ( ! empty( $rule->rule_cost ) ) {
				$price += $rule->rule_cost;
			}
			$google_rule['price'] = number_format( $price, 2, '.', '' ) . ' ' . get_woocommerce_currency();

			$feed_item->additional_elements['shipping'][] = $google_rule;
		}

		return $feed_item;
	}
}

