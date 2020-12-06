<?php

class WoocommerceGpfWoocommerceCompositeProducts {

	/**
	 * Add filters.
	 */
	public function run() {
		add_filter( 'woocommerce_gpf_wc_get_products_args', [ $this, 'add_composite_products_to_query' ], 10, 2 );
		add_filter( 'woocommerce_gpf_product_price_calculator_callback', [ $this, 'assign_price_calculator' ], 10, 3 );
	}

	/**
	 * Add "composite" to the list of queried-for product types when relevant.
	 *
	 * @param $args
	 * @param $type
	 *
	 * @return mixed
	 */
	public function add_composite_products_to_query( $args, $type ) {
		if ( in_array( $type, [ 'feed', 'status', 'WoocommerceGpfRebuildSimpleJob' ], true ) ) {
			$args['type'][] = 'composite';
		}
		return $args;
	}

	/**
	 * Register our custom price calculator method for composite products.
	 *
	 * @param $calculator
	 * @param $product_type
	 * @param $product
	 *
	 * @return array
	 */
	public function assign_price_calculator( $calculator, $product_type, $product ) {
		if ( 'composite' === $product_type ) {
			return [ $this, 'calculate_prices' ];
		}
		return $calculator;
	}

	/**
	 * Calculate prices for Composite products.
	 * @param $product
	 * @param $prices
	 *
	 * @return array
	 */
	public function calculate_prices( $product, $prices ) {
		// Use tax-specific functions if available.
		if ( is_callable( [ $product, 'get_composite_regular_price_including_tax' ] ) ) {
			$prices['regular_price_ex_tax']  = $product->get_composite_regular_price_excluding_tax();
			$prices['regular_price_inc_tax'] = $product->get_composite_regular_price_including_tax();
			$current_price_ex_tax            = $product->get_composite_price_excluding_tax();
			if ( $current_price_ex_tax < $prices['regular_price_ex_tax'] ) {
				$prices['sale_price_ex_tax']  = $product->get_composite_price_excluding_tax();
				$prices['sale_price_inc_tax'] = $product->get_composite_price_including_tax();
			}
		} else {
			// Just take the current price as the regular price since its
			// the only one we can reliably get.
			$prices['regular_price_ex_tax']  = $product->get_composite_price_excluding_tax();
			$prices['regular_price_inc_tax'] = $product->get_composite_price_including_tax();
		}
		// Populate a "price", using the sale price if there is one, the actual price if not.
		if ( null !== $prices['sale_price_ex_tax'] ) {
			$prices['price_ex_tax']  = $prices['sale_price_ex_tax'];
			$prices['price_inc_tax'] = $prices['sale_price_inc_tax'];
		} else {
			$prices['price_ex_tax']  = $prices['regular_price_ex_tax'];
			$prices['price_inc_tax'] = $prices['regular_price_inc_tax'];
		}

		return $prices;
	}
}
