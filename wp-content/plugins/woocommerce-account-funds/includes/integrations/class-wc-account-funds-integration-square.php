<?php
/**
 * Integration: Square for WooCommerce.
 *
 * @package WC_Account_Funds\Integrations
 * @since   2.6.1
 */

defined( 'ABSPATH' ) || exit;

/**
 * Class WC_Account_Funds_Integration_Square.
 */
class WC_Account_Funds_Integration_Square implements WC_Account_Funds_Integration {

	/**
	 * Init.
	 *
	 * @since 2.6.1
	 */
	public static function init() {
		add_filter( 'woocommerce_order_get_discount_total', array( __CLASS__, 'get_discount_total' ), 10, 2 );
	}

	/**
	 * Gets the plugin basename.
	 *
	 * @since 2.6.1
	 *
	 * @return string
	 */
	public static function get_plugin_basename() {
		return 'woocommerce-square/woocommerce-square.php';
	}

	/**
	 * Filters the 'discount_total' property for the order.
	 *
	 * @since 2.6.1
	 *
	 * @param float    $total_discount The total discount.
	 * @param WC_Order $order Order object.
	 *
	 * @return float
	 */
	public static function get_discount_total( $total_discount, $order ) {
		if ( 'square_credit_card' !== $order->get_payment_method() ) {
			return $total_discount;
		}

		$funds = (float) $order->get_meta( '_funds_used' );

		if ( 0 >= $funds ) {
			return $total_discount;
		}

		$backtrace  = wp_debug_backtrace_summary( 'WP_Hook', 0, false ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_wp_debug_backtrace_summary
		$save_index = array_search( 'WC_Abstract_Order->get_discount_total', $backtrace, true );
		$callback   = $backtrace[ $save_index + 1 ];

		if ( 'WooCommerce\Square\Gateway\API\Requests\Orders->set_create_order_data' === $callback ) {
			$rates = self::get_order_tax_rates( $order );

			/*
			 * When multiple tax rates are applied to the items, the funds' amount without taxes cannot be calculated.
			 * So we leave the WC Square extension to adjust the order total.
			 */
			if ( 1 >= count( $rates ) ) {
				$funds_tax = wc_round_tax_total( array_sum( WC_Tax::calc_tax( $funds, $rates, true ) ) );

				$total_discount += ( $funds - $funds_tax );
			}
		}

		return $total_discount;
	}

	/**
	 * Gets the order tax rates.
	 *
	 * @since 2.6.1
	 *
	 * @param WC_Order $order Order object.
	 * @return array
	 */
	protected static function get_order_tax_rates( $order ) {
		$taxes = $order->get_taxes();
		$rates = array();

		foreach ( $taxes as $tax ) {
			$rate_id = $tax->get_rate_id();
			$rate    = WC_Tax::_get_tax_rate( $rate_id );

			$rates[ $rate_id ] = array(
				'rate'     => $rate['tax_rate'],
				'name'     => $rate['tax_rate_name'],
				'priority' => (int) $rate['tax_rate_priority'],
				'compound' => (bool) $rate['tax_rate_compound'],
				'order'    => (int) $rate['tax_rate_order'],
				'class'    => $rate['tax_rate_class'] ? $rate['tax_rate_class'] : 'standard',
			);
		}

		return $rates;
	}
}
