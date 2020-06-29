<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


if ( ! function_exists( 'ywcds_format_number' ) ) {

	function ywcds_format_number( $number ) {

		$number = str_replace( get_option( 'woocommerce_price_thousand_sep' ), '', $number );

		return wc_format_decimal( $number );
	}
}

if ( ! function_exists( 'ywcds_get_product_donation_title' ) ) {

	/**
	 * @param WC_Product $product
	 */
	function ywcds_get_product_donation_title( $product ) {

		$donation_name = sprintf( __( 'Donation ( %s )', 'yith-donations-for-woocommerce' ), $product->get_title() );

		return apply_filters( 'ywcds_get_product_donation_title', $donation_name, $product );
	}
}