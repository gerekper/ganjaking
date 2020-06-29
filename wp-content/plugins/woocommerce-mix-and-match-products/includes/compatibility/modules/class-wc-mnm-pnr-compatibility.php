<?php
/**
 * Points and Rewards Compatibility
 *
 * @author   SomewhereWarm
 * @category Compatibility
 * @package  WooCommerce Mix and Match Products/Compatibility
 * @since    1.0.5
 * @version  1.7.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_MNM_PnR_Compatibility Class.
 *
 * Adds compatibility with WooCommerce Points and Rewards.
 */
class WC_MNM_PnR_Compatibility {

	/**
	 * MnM points - @see 'WC_MNM_PnR_Compatibility::replace_points'.
	 * @var mixed
	 */
	private static $mnm_price_max = false;
	private static $mnm_price_min = false;

	/**
	 * Bypass 'wc_points_rewards_single_product_message' filter.
	 * @var bool
	 */
	private static $single_product_message_filter_active = true;

	/*
	 * Initialize.
	 */
	public static function init() {

		// Points earned for per-product priced MnM bundles.
		add_filter( 'woocommerce_points_earned_for_cart_item', array( __CLASS__, 'points_earned_for_bundled_cart_item' ), 10, 3 );
		add_filter( 'woocommerce_points_earned_for_order_item', array( __CLASS__, 'points_earned_for_bundled_order_item' ), 10, 5 );

		// Change earn points message for per-product-priced MnM bundles.
		add_filter( 'wc_points_rewards_single_product_message', array( __CLASS__, 'points_rewards_mnm_message' ), 10, 2 );
	}

	/**
	 * Return zero points for child cart items if container item has product- or category-level points or child item is not priced individually.
	 *
	 * @param  int     $points
	 * @param  string  $cart_item_key
	 * @param  array   $cart_item_values
	 * @return int
	 */
	public static function points_earned_for_bundled_cart_item( $points, $cart_item_key, $cart_item_values ) {

		if ( isset( $cart_item_values[ 'mnm_container' ] ) ) {

			$cart_contents = WC()->cart->get_cart();

			$container_cart_id = $cart_item_values[ 'mnm_container' ];
			$container         = $cart_contents[ $container_cart_id ][ 'data' ];

			if ( self::has_fixed_points( $container ) || false === $container->is_priced_per_product() ) {
				$points = 0;
			} else {
				$points = WC_Points_Rewards_Manager::calculate_points( $cart_item_values[ 'data' ]->get_price() );
			}
		}

		return $points;
	}

	/**
	 * Return zero points for child order items if container item has product- or category-level points or child item is not priced individually.
	 *
	 * @param  int        $points
	 * @param  string     $item_key
	 * @param  array      $item
	 * @param  WC_Order   $order
	 * @return int
	 */
	public static function points_earned_for_bundled_order_item( $points, $product, $item_key, $item, $order ) {

		if ( isset( $item[ 'mnm_container' ] ) ) {

			// find container item
			foreach ( $order->get_items() as $order_item ) {

				$is_parent = ( isset( $order_item[ 'mnm_cart_key' ] ) && $item[ 'mnm_container' ] == $order_item[ 'mnm_cart_key' ] ) ? true : false;

				if ( $is_parent ) {

					$parent_item = $order_item;
					$parent_obj  = wc_get_product( $parent_item[ 'product_id' ] );

					$is_priced_per_product = isset( $parent_item[ 'per_product_pricing' ] ) ? ( 'yes' === $parent_item[ 'per_product_pricing' ] ) : $parent_obj->is_priced_per_product();

					if ( self::has_fixed_points( $parent_obj ) || ! $is_priced_per_product ) {
						$points = 0;
					} else {
						$points = WC_Points_Rewards_Manager::calculate_points( $product->get_price() );
					}

					break;
				}
			}
		}

		return $points;
	}

	/**
	 * Points and Rewards single product message for MnM bundles.
	 *
	 * @param  string                    $message
	 * @param  WC_Points_Rewards_Product $points_n_rewards
	 * @return string
	 */
	public static function points_rewards_mnm_message( $message, $points_n_rewards ) {

		global $product;

		if ( $product->is_type( 'mix-and-match' ) && self::$single_product_message_filter_active ) {

			if ( false === self::has_fixed_points( $product ) && $product->is_priced_per_product() ) {

				$max_mnm_price = $product->get_mnm_price( 'max' );
				$min_mnm_price = $product->get_mnm_price( 'min' );

				if ( '' !== $max_mnm_price ) {
					self::$mnm_price_max = $max_mnm_price;
				} else {
					self::$mnm_price_min = $min_mnm_price;
				}

				// 'WC_Points_Rewards_Product' relies on 'get_price', which only returns the base price of a bundle.
				add_filter( 'woocommerce_product_get_price', array( __CLASS__, 'replace_price' ), 9999, 2 );

				$mnm_points = WC_Points_Rewards_Product::get_points_earned_for_product_purchase( $product );

				if ( '' !== $max_mnm_price ) {
					if ( $min_mnm_price === $max_mnm_price ) {
						self::$single_product_message_filter_active = false;
						$message = $points_n_rewards->render_product_message();
						self::$single_product_message_filter_active = true;
					} else {
						$message = $points_n_rewards->create_variation_message_to_product_summary( $mnm_points );
					}
				} else {
					$message = $points_n_rewards->create_at_least_message_to_product_summary( $mnm_points );
				}

				remove_filter( 'woocommerce_product_get_price', array( __CLASS__, 'replace_price' ), 9999, 2 );

				self::$mnm_price_min = self::$mnm_price_max = false;
			}
		}

		return $message;
	}

	/**
	 * Filter bundle price returned by 'get_price' to return the min/max bundle price.
	 *
	 * @param  mixed              $price
	 * @param  WC_Product_Bundle  $product
	 * @return mixed
	 */
	public static function replace_price( $price, $product ) {
		if ( false !== self::$mnm_price_max ) {
			$price = self::$mnm_price_max;
		} elseif ( false !== self::$mnm_price_min ) {
			$price = self::$mnm_price_min;
		}
		return $price;
	}

	/**
	 * True if the MnM bundle has fixed product- or category-level points.
	 *
	 * @param  WC_Product  $product
	 * @return bool
	 */
	private static function has_fixed_points( $product ) {

		$mnm_product_points  = WC_Points_Rewards_Product::get_product_points( $product );
		$mnm_category_points = is_callable( array( 'WC_Points_Rewards_Product', 'get_category_points' ) ) ? WC_Points_Rewards_Product::get_category_points( $product ) : '';

		return is_numeric( $mnm_product_points ) || is_numeric( $mnm_category_points );
	}
}

WC_MNM_PnR_Compatibility::init();
