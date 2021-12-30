<?php
/**
 * WC_PB_Subscriptions_Compatibility class
 *
 * @package  WooCommerce Product Bundles
 * @since    4.11.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Subscriptions Integration.
 *
 * @version  5.10.1
 */
class WC_PB_Subscriptions_Compatibility {

	public static function init() {

		/*
		 * Remove orphaned bundled items when WC Subs sets up the cart in order to pay for an initial (not renewal) order that contains subscription items.
		 * Temporary workaround for https://github.com/Prospress/woocommerce-subscriptions/issues/1362
		 */
		add_action( 'woocommerce_add_to_cart', array( __CLASS__, 'remove_orphaned_bundled_cart_item' ), 10, 6 );

		/*
		 * Ensure bundles can't be added to the cart if a gateway doesn't support multiple subscriptions.
		 * Will be handled by WCS in the future - see https://github.com/Prospress/woocommerce-subscriptions/issues/2250
		 */
		add_filter( 'woocommerce_bundle_before_validation', array( __CLASS__, 'validate_bundle' ), 10, 2 );
	}

	/**
	 * Remove orphaned bundled items when WC Subs sets up the cart in order to pay for an initial (not renewal) order that contains subscription items.
	 *
	 * Bundled cart items are normally added to the cart when their container is added to the cart on the 'woocommerce_add_to_cart' action.
	 * This is carried on to the ordering-again logic, in which case bundled cart items are specifically prevented from ending up in the cart - @see 'WC_PB_Cart::woo_bundles_validation()'.
	 *
	 * WC Subs fakes some of the core re-ordering logic to populate the cart with subscription order items when paying for an initial order that is pending payment, or when paying for a pending/failed renewal order.
	 * However, due to https://github.com/Prospress/woocommerce-subscriptions/issues/1362, 'WC_PB_Cart::validate_add_to_cart()' does not run to prevent bundled cart items from being added to the cart when paying for initial orders that include the container bundle.
	 * This hook fixes that shortcoming.
	 *
	 * Note that this "cleaning up" should not be done for renewal orders, since these do not include the container item.
	 *
	 * @param  string  $cart_item_key
	 * @param  int     $product_id
	 * @param  int     $quantity
	 * @param  int     $variation_id
	 * @param  array   $variation
	 * @param  array   $cart_item_data
	 * @return void
	 */
	public static function remove_orphaned_bundled_cart_item( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {

		global $wp;

		if ( isset( $_GET[ 'pay_for_order' ] ) && isset( $_GET[ 'key' ] ) && isset( $wp->query_vars[ 'order-pay' ] ) ) {
			if ( isset( $cart_item_data[ 'is_order_again_bundled' ] ) && isset( $cart_item_data[ 'subscription_initial_payment' ] ) ) {
				unset( WC()->cart->cart_contents[ $cart_item_key ] );
			}
		}
	}

	/**
	 * Validate support for multiple subscriptions. Will be handled by WCS in the future.
	 *
	 * @param  WC_Product_Bundle  $bundle
	 * @return boolean
	 */
	public static function validate_bundle( $valid, $bundle ) {

		if ( $bundle->contains( 'multiple_subscriptions' ) ) {

			if ( class_exists( 'WC_Subscriptions_Payment_Gateways' ) && class_exists( 'WC_Subscriptions_Admin' ) ) {

				$manual_renewals_enabled = 'yes' === get_option( WC_Subscriptions_Admin::$option_prefix . '_accept_manual_renewals', 'no' );

				if ( false === WC_Subscriptions_Payment_Gateways::one_gateway_supports( 'multiple_subscriptions' ) && false === $manual_renewals_enabled ) {
					/* translators: %1$s: Product name */
					wc_add_notice( sprintf( __( '&quot;%1$s&quot; cannot be purchased due to payment gateway restrictions.', 'woocommerce-product-bundles' ), $bundle->get_title() ), 'error' );
					return false;
				}
			}

		}

		return $valid;
	}
}

WC_PB_Subscriptions_Compatibility::init();
