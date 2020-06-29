<?php


if ( ! WC_Dynamic_Pricing_Compatibility::is_wc_version_gte_2_7() ) {

	if ( ! function_exists( 'wc_get_price_including_tax' ) ) {

		/**
		 * @param $product WC_Product
		 * @param array $args
		 */
		function wc_get_price_including_tax( $product, $args = array() ) {
			$args = wp_parse_args( $args, array(
				'qty'   => 1,
				'price' => '',
			) );

			return $product->get_price_including_tax( $args['qty'], $args['price'] );
		}
	}


	if ( ! function_exists( 'wc_get_price_excluding_tax' ) ) {
		/**
		 * @param $product WC_Product
		 * @param array $args
		 */
		function wc_get_price_excluding_tax( $product, $args = array() ) {
			$args = wp_parse_args( $args, array(
				'qty'   => 1,
				'price' => '',
			) );

			return $product->get_price_excluding_tax( $args['qty'], $args['price'] );
		}
	}
}


