<?php

class WC_Dynamic_Pricing_Collector_Category_Inclusive extends WC_Dynamic_Pricing_Collector {


	/**
	 * @var array Array of category ID's this collector should count in the cart.
	 */
	public $categories_to_match;


	/**
	 * WC_Dynamic_Pricing_Collector_Category constructor.
	 *
	 * @param $collector_data Array of collector configuration.
	 */
	public function __construct( $collector_data ) {
		parent::__construct( $collector_data );
		$this->categories_to_match = ( isset( $collector_data['args'] ) && isset( $collector_data['args']['cats'] ) && is_array( $collector_data['args']['cats'] ) ) ? $collector_data['args']['cats'] : false;
	}


	public function collect_quantity( $cart_item ) {

		if ( isset( $this->type ) && $this->type == 'cat_product' ) {
			return $cart_item['quantity'];
		} else {
			$q                  = 0;
			$categories_matched = array_fill_keys( $this->categories_to_match, 0 );
			if ( $this->categories_to_match ) {
				foreach ( WC()->cart->cart_contents as $lck => $l_cart_item ) {
					$product_category_ids = WC_Dynamic_Pricing_Compatibility::get_product_category_ids( $l_cart_item['data'] );
					if ( $product_category_ids ) {
						$matched = array_intersect( $product_category_ids, $this->categories_to_match );
						if ( $matched ) {
							foreach ( $matched as $match ) {
								$categories_matched[ $match ] = 1;
							}
						}
					}

					if ( apply_filters( 'woocommerce_dynamic_pricing_is_object_in_terms', is_object_in_term( $l_cart_item['product_id'], 'product_cat', $this->categories_to_match ), $l_cart_item['product_id'], $this->categories_to_match ) ) {
						if ( apply_filters( 'woocommerce_dynamic_pricing_count_categories_for_cart_item', true, $l_cart_item, $lck ) ) {
							$q += (int) $l_cart_item['quantity'];
						}
					}
				}
			}

			if ( array_sum( $categories_matched ) >= count( $this->categories_to_match ) ) {
				return $q;
			} else {
				return 0;
			}

		}

	}

}
