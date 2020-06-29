<?php
/**
 * Product Add-ons ajax
 *
 * @package WC_Product_Addons/Classes/Legacy/Ajax
 * @since   2.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Addons_Ajax class.
 */
class WC_Addons_Ajax extends Product_Addon_Cart_Ajax {

	/**
	 * Calculate tax values for grand total (after options value)
	 * Used when we can't calculate tax from form values
	 * (since there 4 different combinations of how taxes can be displayed)
	 */
	public function calculate_tax() {

		// Make sure we have a total to calculate the tax on..
		$total = isset( $_POST['total'] ) ? floatval( $_POST['total'] ) : 0;
		if ( $total <= 0 ) {
			die( wp_json_encode( array(
				'result' => 'ERROR',
				'error'   => 'no-total'
			) ) );
		}

		// Make sure we have a valid producto so we can calculate tax
		$product_id = intval( $_POST['product_id'] );
		$product    = wc_get_product( $product_id );
		if ( ! $product ) {
			die( wp_json_encode( array(
				'result' => 'ERROR',
				'html'   => 'invalid-product'
			) ) );
		}

		// Return our price including tax and our price excluding tax

		// When the tax is set to exclusive and display mode is set to inclusive, our price excluding tax is just the normal price
		if ( get_option( 'woocommerce_prices_include_tax' ) === 'no' && 'incl' === get_option( 'woocommerce_tax_display_shop' ) ) {
			die( wp_json_encode( array(
				'result' => 'SUCCESS',
				'price_including_tax' => round( $product->get_price_including_tax( 1, $total ), wc_get_price_decimals() ),
				'price_excluding_tax' => round( $total, wc_get_price_decimals() ),
			) ) );
		}

		die( wp_json_encode( array(
			'result' => 'SUCCESS',
			'price_including_tax' => round( $product->get_price_including_tax( 1, $total ), wc_get_price_decimals() ),
			'price_excluding_tax' => round( $product->get_price_excluding_tax( 1, $total ), wc_get_price_decimals() ),
		) ) );

	}
}
