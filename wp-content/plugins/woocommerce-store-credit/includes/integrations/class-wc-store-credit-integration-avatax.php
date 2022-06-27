<?php
/**
 * Integration: WooCommerce AvaTax.
 *
 * @package WC_Store_Credit\Integrations
 * @since   4.1.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Store_Credit_Integration_Avatax
 */
class WC_Store_Credit_Integration_Avatax implements WC_Store_Credit_Integration {

	/**
	 * Init.
	 *
	 * @since 4.1.0
	 */
	public static function init() {
		add_filter( 'wc_store_credit_calculate_shipping_discounts_for_cart', array( __CLASS__, 'calculate_shipping_discounts_for_cart' ) );
		add_filter( 'wc_store_credit_discounts_order_tax_rate', array( __CLASS__, 'order_tax_rate' ), 10, 3 );
		add_filter( 'wc_avatax_api_tax_transaction_request_data', array( __CLASS__, 'avatax_request_data' ) );
	}

	/**
	 * Gets the plugin basename.
	 *
	 * @since 4.1.0
	 *
	 * @return string
	 */
	public static function get_plugin_basename() {
		return 'woocommerce-avatax/woocommerce-avatax.php';
	}

	/**
	 * Whether to calculate the shipping discounts for the specified cart.
	 *
	 * @since 4.1.0
	 *
	 * @param bool $calculate_discounts Whether to calculate the shipping discounts.
	 * @return bool
	 */
	public static function calculate_shipping_discounts_for_cart( $calculate_discounts ) {
		if ( ! $calculate_discounts ) {
			return $calculate_discounts;
		}

		$backtrace  = wp_debug_backtrace_summary( 'WP_Hook', 0, false ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_wp_debug_backtrace_summary
		$save_index = array_search( "do_action('woocommerce_after_calculate_totals')", $backtrace, true );

		/*
		 * "WooCommerce AvaTax" calculate the cart totals again inside the callback bound to
		 * the hook "woocommerce_after_calculate_totals". So, we need to discard the nested call and
		 * apply the changes only in the call made by WC_Cart->calculate_totals().
		 */
		if ( 'WC_Cart->calculate_totals' !== $backtrace[ $save_index + 1 ] || 0 === strpos( $backtrace[ $save_index + 2 ], 'WC_AvaTax_Checkout_Handler' ) ) {
			return false;
		}

		return $calculate_discounts;
	}

	/**
	 * Gets the data for an AvaTax rate.
	 *
	 * @since 4.1.0
	 *
	 * @param array    $tax_rate Tax rate data.
	 * @param int      $rate_id  Tax rate ID.
	 * @param WC_Order $order    Order object.
	 */
	public static function order_tax_rate( $tax_rate, $rate_id, $order ) {
		if ( 0 === strpos( $rate_id, 'AVATAX-' ) ) {
			$tax_items = $order->get_taxes();

			foreach ( $tax_items as $tax_item ) {
				if ( $rate_id === $tax_item->get_rate_code() ) {
					return array(
						'name'              => $rate_id,
						'tax_rate'          => $tax_item->get_rate_percent(),
						'tax_rate_shipping' => ( 0 < $tax_item->get_shipping_tax_total() ),
						'tax_rate_compound' => $tax_item->get_compound(),
					);
				}
			}
		}

		return $tax_rate;
	}

	/**
	 * Filters the AvaTax transaction request data.
	 *
	 * @since 4.1.0
	 *
	 * @param array $data Request data.
	 * @return array
	 */
	public static function avatax_request_data( $data ) {
		foreach ( $data['lines'] as $key => $line ) {
			// Exclude the shipping discount lines.
			if ( 'store_credit_discount' === $line['itemCode'] ) {
				unset( $data['lines'][ $key ] );
			}
		}

		return $data;
	}
}
