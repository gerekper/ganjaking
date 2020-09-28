<?php

class WoocommerceGpfWoocommerceMixAndMatchProducts {

	/**
	 * Add filters.
	 */
	public function run() {
		add_filter( 'woocommerce_gpf_wc_get_products_args', [ $this, 'register_product_type_for_query' ] );
		add_filter( 'woocommerce_gpf_item_prices', [ $this, 'manipulate_prices' ], 10, 3 );
	}

	/**
	 * Registers the mix and match product type to be retrieved by the product queries.
	 *
	 * @param $args
	 *
	 * @return mixed
	 */
	public function register_product_type_for_query( $args ) {
		$args['type'][] = 'mix-and-match';

		return $args;
	}

	/**
	 * Ensure that the price in the feed is reasonable for products priced by items.
	 *
	 * @param $prices
	 * @param $specific_product
	 * @param $general_product
	 *
	 * @return mixed
	 */
	public function manipulate_prices( $prices, $specific_product, $general_product ) {

		// Do nothing if it is not a mix and match product.
		if ( $specific_product->get_type() !== 'mix-and-match' ) {
			return $prices;
		}

		// Do nothing if it is not priced per product - the standard behaviour is fine.
		if ( ! $specific_product->is_priced_per_product() ) {
			return $prices;
		}

		// Retrieve the prices we need.
		$price_type        = apply_filters( 'woocommerce_gpf_mnm_price_type', 'min' );
		$min_price         = $specific_product->get_mnm_price( $price_type );
		$min_regular_price = $specific_product->get_mnm_regular_price( $price_type );

		// Adjust regular price
		$prices['regular_price_ex_tax']  = wc_get_price_excluding_tax(
			$specific_product,
			[ 'price' => $min_regular_price ]
		);
		$prices['regular_price_inc_tax'] = wc_get_price_including_tax(
			$specific_product,
			[ 'price' => $min_regular_price ]
		);

		// Adjust sale price
		if ( $min_price !== $min_regular_price ) {
			$prices['sale_price_ex_tax']  = wc_get_price_excluding_tax(
				$specific_product,
				[ 'price' => $min_price ]
			);
			$prices['sale_price_inc_tax'] = wc_get_price_including_tax(
				$specific_product,
				[ 'price' => $min_price ]
			);
		} else {
			$prices['sale_price_ex_tax']     = null;
			$prices['sale_price_inc_tax']    = null;
			$prices['sale_price_start_date'] = null;
			$prices['sale_price_end_date']   = null;
		}

		// Re-calculate the "price"
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

