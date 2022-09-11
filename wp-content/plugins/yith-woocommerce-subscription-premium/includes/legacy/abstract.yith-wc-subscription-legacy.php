<?php // phpcs:ignore WordPress.Files.FileName.InvalidClassFileName
/**
 * YITH_WC_Subscription_Legacy Legacy Abstract Class.
 *
 * @class   YITH_WC_Subscription_Legacy
 * @package YITH WooCommerce Subscription
 * @since   2.0.0
 * @author  YITH
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class YITH_WC_Subscription_Legacy
 */
abstract class YITH_WC_Subscription_Legacy {

	/**
	|--------------------------------------------------------------------------
	| Deprecated Methods
	|--------------------------------------------------------------------------
	 */

	/**
	 * Change price HTML to the product
	 *
	 * @param WC_Product $product  WC_Product.
	 * @param int        $quantity Quantity.
	 *
	 * @return     string
	 * @since      1.2.0
	 * @deprecated 2.0.0
	 */
	public function change_general_price_html( $product, $quantity = 1 ) {
		_deprecated_function( 'YITH_WC_Subscription::change_general_price_html', '2.0.0', 'YWSBS_Subscription_Cart::change_general_price_html' );
		return YWSBS_Subscription_Cart::change_general_price_html( $product, $quantity );
	}


	/**
	 * Check if in the cart there are subscription products.
	 *
	 * @return     bool|array
	 * @since      1.0.0
	 * @deprecated 2.0.0
	 */
	public function cart_has_subscriptions() {
		_deprecated_function( 'YITH_WC_Subscription::cart_has_subscriptions', '2.0.0', 'YWSBS_Subscription_Cart::cart_has_subscriptions' );
		return YWSBS_Subscription_Cart::cart_has_subscriptions();
	}

	/**
	 * Removes all subscription products from the shopping cart.
	 *
	 * @param int $item_key Cart item key.
	 *
	 * @return     void
	 * @since      1.0.0
	 * @deprecated 2.0.0
	 */
	public function clean_cart_from_subscriptions( $item_key ) {
		_deprecated_function( 'YITH_WC_Subscription::clean_cart_from_subscriptions', '2.0.0', 'YWSBS_Subscription_Cart::remove_subscription_from_cart' );
		YWSBS_Subscription_Cart::remove_subscription_from_cart( $item_key );
	}


	/**
	 * Return overdue time period
	 *
	 * @deprecated 2.0.0
	 * @since      1.0.0
	 */
	public function overdue_time() {
		_deprecated_function( 'YITH_WC_Subscription::overdue_time', '2.0.0', 'ywsbs_get_overdue_time' );
		return ywsbs_get_overdue_time();
	}

	/**
	 * Return suspension time period
	 *
	 * @deprecated 2.0.0
	 * @since      1.0.0
	 */
	public function suspension_time() {
		_deprecated_function( 'YITH_WC_Subscription::suspension_time', '2.0.0', 'ywsbs_get_suspension_time' );
		return ywsbs_get_suspension_time();
	}


	/**
	 * Change add to cart label in subscription product
	 *
	 * @param string          $label   Current add to cart label.
	 * @param null|WC_Product $product Current product.
	 *
	 * @return     string
	 * @since      1.0.0
	 * @deprecated 2.0.0
	 */
	public function change_add_to_cart_label( $label, $product = null ) {
		_deprecated_function( 'YITH_WC_Subscription::change_add_to_cart_label', '2.0.0', 'YITH_WC_Subscription_Frontend()->change_add_to_cart_label' );
		return YITH_WC_Subscription_Frontend()->change_add_to_cart_label( $label );
	}

	/**
	 * Add custom params to variations
	 *
	 * @param array                $args      Arguments.
	 * @param WC_Product           $product   Current product.
	 * @param WC_Product_Variation $variation WC_Product_Variation.
	 *
	 * @return     array
	 * @deprecated 2.0.0
	 * @since      1.0.0
	 */
	public function add_params_to_available_variation( $args, $product, $variation ) {
		_deprecated_function( 'YITH_WC_Subscription::add_params_to_available_variation', '2.0.0', 'YITH_WC_Subscription_Frontend()->add_params_to_available_variation' );
		return YITH_WC_Subscription_Frontend()->add_params_to_available_variation( $args, $product, $variation );
	}

	/**
	 * Disable gateways that don't support multiple subscription on cart
	 *
	 * @param      array $gateways Gateways list.
	 * @deprecated 2.0.0
	 */
	public function disable_gateways( $gateways ) {
		_deprecated_function( 'YITH_WC_Subscription::disable_gateways', '2.0.0', 'YWSBS_Subscription_Cart()->disable_gateways' );
		return YWSBS_Subscription_Cart()->disable_gateways( $gateways );
	}

	/**
	 * Check if a product is a subscription.
	 *
	 * @param      WC_Product|int $product Product Object or Product ID.
	 * @return     bool
	 * @deprecated 2.0.0
	 * @since      1.0.0
	 */
	public function is_subscription( $product ) {
		_deprecated_function( 'YITH_WC_Subscription::is_subscription', '2.0.0', 'ywsbs_is_subscription_product' );
		return ywsbs_is_subscription_product( $product );
	}

}
