<?php
/**
 * Product Add-ons ajax
 *
 * @package WC_Product_Addons/Classes/Ajax
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * WC_Product_Addons_Cart_Ajax class.
 */
class WC_Product_Addons_Cart_Ajax {

	/**
	 * Handle ajax endpoints.
	 */
	public function __construct() {
		add_action( 'wp_ajax_wc_product_addons_calculate_tax', array( $this, 'calculate_tax' ) );
		add_action( 'wp_ajax_nopriv_wc_product_addons_calculate_tax', array( $this, 'calculate_tax' ) );
	}

	/**
	 * Calculate tax values for sub total (after options value)
	 * Used when we can't calculate tax from form values
	 * (since there 4 different combinations of how taxes can be displayed).
	 *
	 * @since 1.0.0
	 */
	public function calculate_tax() {
		// Make sure we have a total to calculate the tax on.
		$add_on_total = floatval( $_POST['add_on_total'] );
		if ( $add_on_total < 0 ) {
			wp_send_json( array(
				'result' => 'ERROR',
				'error'   => 'no-total',
			) );
		}

		$add_on_total_raw = floatval( $_POST['add_on_total_raw'] );

		// Make sure we have a valid product so we can calculate tax.
		$product_id = intval( $_POST['product_id'] );
		$product    = wc_get_product( $product_id );

		if ( ! $product ) {
			wp_send_json( array(
				'result' => 'ERROR',
				'html'   => 'invalid-product',
			) );
		}

		$qty = ! empty( $_POST['qty'] ) ? absint( $_POST['qty'] ) : 1;

		// If product prices include tax we need to calculate the cost of an addon excluding taxes.
		if ( wc_prices_include_tax() ) {
			$excluding = wc_get_price_excluding_tax( $product, array( 'price' => $add_on_total_raw ) );
			$including = ! empty( WC()->customer ) && WC()->customer->get_is_vat_exempt() ? $excluding : $add_on_total_raw;
		} else {
			$excluding = $add_on_total_raw;
			$including = wc_get_price_including_tax( $product, array( 'price' => $add_on_total_raw ) );
		}

		// Get the product price excluding and including taxes, then filter them.
		$product_price_excl = wc_get_price_excluding_tax( $product, array( 'qty' => $qty ) );
		$product_price_incl = wc_get_price_including_tax( $product, array( 'qty' => $qty ) );
		$product_price_excl = apply_filters( 'woocommerce_product_addons_ajax_get_product_price_excluding_tax', $product_price_excl, $qty, $product );
		$product_price_incl = apply_filters( 'woocommerce_product_addons_ajax_get_product_price_including_tax', $product_price_incl, $qty, $product );

		// Apply filters for excluding and including addons cost.
		$add_on_total_excl  = apply_filters( 'woocommerce_product_addons_get_addon_price_excluding_tax', $excluding, $product );
		$add_on_total_incl  = apply_filters( 'woocommerce_product_addons_get_addon_price_including_tax', $including, $product );

		wp_send_json(
			array(
				'result'              => 'SUCCESS',
				'price_including_tax' => wc_round_tax_total( $product_price_incl + $add_on_total_incl ),
				'price_excluding_tax' => wc_round_tax_total( $product_price_excl + $add_on_total_excl ),
			)
		);
	}

}
