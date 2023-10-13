<?php

namespace WCML\Compatibility\WcMixAndMatch;

class MulticurrencyHooks implements \IWPML_Action {

	public function add_hooks() {
		add_filter( 'wcml_price_custom_fields_filtered', [ $this, 'get_price_custom_fields' ], 10, 2 );
		add_filter( 'wcml_update_custom_prices_values', [ $this, 'update_container_custom_prices_values' ], 10, 2 );
		add_filter( 'wcml_after_save_custom_prices', [ $this, 'update_container_base_price' ], 10, 4 );
	}

	/**
	 * Add MNM price fields to list to be converted.
	 *
	 * @since 5.0.0
	 *
	 * @param array $custom_fields
	 * @return array
	 */
	public function get_price_custom_fields( $custom_fields ) {
		return array_merge(
			$custom_fields,
			[
				'_mnm_base_regular_price',
				'_mnm_base_sale_price',
				'_mnm_base_price',
				'_mnm_max_price',
				'_mnm_max_regular_price',
			]
		);
	}

	/**
	 * Swap the base price for the custom price in that currency.
	 *
	 * @since 5.0.0
	 *
	 * @param array  $prices
	 * @param string $code
	 * @return array
	 */
	public function update_container_custom_prices_values( $prices, $code ) {
		foreach ( [
			'_custom_regular_price' => '_mnm_base_regular_price',
			'_custom_sale_price'    => '_mnm_base_sale_price',
		] as $wc_price => $custom_price ) {
			if ( isset( $_POST[ $wc_price ][ $code ] ) ) {
				$prices[ $custom_price ] = wc_format_decimal( $_POST[ $wc_price ][ $code ] );
			}
		}

		return $prices;
	}

	/**
	 * Save base price per currency.
	 *
	 * @since 5.0.0
	 *
	 * @param int    $post_id
	 * @param string $product_price
	 * @param array  $custom_prices
	 * @param string $code
	 */
	public function update_container_base_price( $post_id, $product_price, $custom_prices, $code ) {

		if ( isset( $custom_prices['_mnm_base_regular_price'] ) ) {
			update_post_meta( $post_id, '_mnm_base_price_' . $code, $product_price );
		}

	}

}
