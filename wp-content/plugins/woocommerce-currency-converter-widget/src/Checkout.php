<?php
/**
 * Checkout Manager.
 *
 * @since 1.8.0
 */

namespace KoiLab\WC_Currency_Converter;

defined( 'ABSPATH' ) || exit;

use WC_Order;

/**
 * Checkout class.
 */
class Checkout {

	/**
	 * Init.
	 *
	 * @since 1.8.0
	 */
	public static function init() {
		add_action( 'woocommerce_checkout_create_order', array( __CLASS__, 'create_order' ) );
	}

	/**
	 * Before creating an order during checkout.
	 *
	 * @since 1.8.0
	 *
	 * @param WC_Order $order Order object.
	 */
	public static function create_order( $order ) {
		$currency = ( isset( $_COOKIE['woocommerce_current_currency'] ) ? wc_clean( wp_unslash( $_COOKIE['woocommerce_current_currency'] ) ) : '' );

		if ( ! $currency ) {
			return;
		}

		$order->update_meta_data( 'Viewed Currency', $currency );

		$store_currency = get_woocommerce_currency();
		$rates          = \WC_Currency_Converter::instance()->rates;

		if ( $store_currency && $rates->$currency && $rates->$store_currency ) {
			$new_order_total = ( $order->get_total() / $rates->$store_currency ) * $rates->$currency;
			$new_order_total = round( $new_order_total, 2 ) . ' ' . $currency;

			$order->update_meta_data( 'Converted Order Total', $new_order_total );
		}
	}
}

class_alias( Checkout::class, 'Themesquad\WC_Currency_Converter\Checkout' );
