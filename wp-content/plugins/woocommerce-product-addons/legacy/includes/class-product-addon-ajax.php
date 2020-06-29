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
 * Product_Addon_Cart_Ajax class.
 */
class Product_Addon_Cart_Ajax {

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
	 * @since 2.9.1 Extracted out a method to calculate only addon prices.
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

		wp_send_json( array(
			'result' => 'SUCCESS',
			'price_including_tax' => round( wc_get_price_including_tax( $product, array( 'qty' => $qty ) ) + $this->calculate_addon_tax( $product, $add_on_total ), wc_get_price_decimals() ),
			'price_excluding_tax' => round( wc_get_price_excluding_tax( $product, array( 'qty' => $qty ) ) + $add_on_total_raw, wc_get_price_decimals() ),
		) );
	}

	/**
	 * Calculates the tax for the addon price.
	 *
	 * @since 2.9.1
	 * @param object $product
	 * @param float $price The addon price
	 */
	public function calculate_addon_tax( $product, $price ) {
		$line_price   = $price;
		$return_price = $line_price;

		if ( $product->is_taxable() ) {
			if ( ! wc_prices_include_tax() ) {
				$tax_rates    = WC_Tax::get_rates( $product->get_tax_class() );
				$taxes        = WC_Tax::calc_tax( $line_price, $tax_rates, false );
				$tax_amount   = WC_Tax::get_tax_total( $taxes );
				$return_price = round( $line_price + $tax_amount, wc_get_price_decimals() );
			} else {
				$tax_rates      = WC_Tax::get_rates( $product->get_tax_class() );
				$base_tax_rates = WC_Tax::get_base_tax_rates( $product->get_tax_class( 'unfiltered' ) );

				/**
				 * If the customer is excempt from VAT, remove the taxes here.
				 * Either remove the base or the user taxes depending on woocommerce_adjust_non_base_location_prices setting.
				 */
				if ( ! empty( WC()->customer ) && WC()->customer->get_is_vat_exempt() ) {
					$remove_taxes = apply_filters( 'woocommerce_adjust_non_base_location_prices', true ) ? WC_Tax::calc_tax( $line_price, $base_tax_rates, true ) : WC_Tax::calc_tax( $line_price, $tax_rates, true );
					$remove_tax   = array_sum( $remove_taxes );
					$return_price = round( $line_price - $remove_tax, wc_get_price_decimals() );

				/**
				 * The woocommerce_adjust_non_base_location_prices filter can stop base taxes being taken off when dealing with out of base locations.
				 * e.g. If a product costs 10 including tax, all users will pay 10 regardless of location and taxes.
				 * This feature is experimental @since 2.4.7 and may change in the future. Use at your risk.
				 */
				} elseif ( $tax_rates !== $base_tax_rates && apply_filters( 'woocommerce_adjust_non_base_location_prices', true ) ) {
					$base_taxes   = WC_Tax::calc_tax( $line_price, $base_tax_rates, true );
					$modded_taxes = WC_Tax::calc_tax( $line_price - array_sum( $base_taxes ), $tax_rates, false );
					$return_price = round( $line_price - array_sum( $base_taxes ) + wc_round_tax_total( array_sum( $modded_taxes ), wc_get_price_decimals() ), wc_get_price_decimals() );
				}
			}
		}

		return apply_filters( 'woocommerce_product_addons_get_addon_price_including_tax', $return_price, $product );
	}
}
