<?php
/**
 * WC_CP_PnR_Compatibility class
 *
 * @author   SomewhereWarm <info@somewherewarm.com>
 * @package  WooCommerce Composite Products
 * @since    3.3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Points and Rewards Compatibility.
 *
 * @version  3.12.0
 */
class WC_CP_PnR_Compatibility {

	/**
	 * Composite points - @see 'WC_CP_PnR_Compatibility::replace_points'.
	 * @var boolean
	 */
	private static $composite_price_max = false;
	private static $composite_price_min = false;

	/**
	 * Bypass 'wc_points_rewards_single_product_message' filter.
	 * @var boolean
	 */
	private static $single_product_message_filter_active = true;

	/**
	 * Initialization.
	 */
	public static function init() {

		// Points and Rewards support.
		add_filter( 'woocommerce_points_earned_for_cart_item', array( __CLASS__, 'points_earned_for_composited_cart_item' ), 10, 3 );
		add_filter( 'woocommerce_points_earned_for_order_item', array( __CLASS__, 'points_earned_for_composited_order_item' ), 10, 5 );

		// Change earn points message for Composites that contain individually-priced items.
		add_filter( 'wc_points_rewards_single_product_message', array( __CLASS__, 'points_rewards_composite_message' ), 10, 2 );

		// Remove PnR message from variations.
		add_action( 'woocommerce_composite_products_apply_product_filters', array( __CLASS__, 'points_rewards_remove_price_html_messages' ) );
		add_action( 'woocommerce_composite_products_remove_product_filters', array( __CLASS__, 'points_rewards_restore_price_html_messages' ) );
	}

	/**
	 * Return zero points for composited cart items if container item has product level points.
	 *
	 * @param  int     $points
	 * @param  string  $cart_item_key
	 * @param  array   $cart_item_values
	 * @return int
	 */
	public static function points_earned_for_composited_cart_item( $points, $cart_item_key, $cart_item_values ) {

		if ( $composite_container_item = wc_cp_get_composited_cart_item_container( $cart_item_values ) ) {

			$composite    = $composite_container_item[ 'data' ];
			$product_id   = $cart_item_values[ 'product_id' ];
			$component_id = $cart_item_values[ 'composite_item' ];

			$component_option = $composite->get_component_option( $component_id, $product_id );

			if ( self::has_fixed_points( $composite ) || false === $component_option->is_priced_individually() ) {
				$points = 0;
			} else {
				$points = WC_Points_Rewards_Manager::calculate_points( $cart_item_values[ 'data' ]->get_price() );
			}
		}

		return $points;
	}

	/**
	 * Return zero points for composited cart items if container item has product level points.
	 *
	 * @param  int       $points
	 * @param  string    $item_key
	 * @param  array     $item
	 * @param  WC_Order  $order
	 * @return int
	 */
	public static function points_earned_for_composited_order_item( $points, $product, $item_key, $item, $order ) {

		if ( $composite_container_item = wc_cp_get_composited_order_item_container( $item, $order ) ) {

			// Check if earned points are set at product-level.
			$item_priced_individually = isset( $item[ 'component_priced_individually' ] ) ? 'yes' === $item[ 'component_priced_individually' ] : null;

			if ( $composite = wc_get_product( $composite_container_item[ 'product_id' ] ) ) {

				// Back-compat.
				if ( null === $item_priced_individually ) {
					if ( isset( $composite_container_item[ 'per_product_pricing' ] ) ) {
						$item_priced_individually = 'yes' === $composite_container_item[ 'per_product_pricing' ];
					} elseif ( isset( $item[ 'composite_item' ] ) ) {
						$product_id               = $item[ 'product_id' ];
						$component_id             = $item[ 'composite_item' ];
						$component_option         = $composite->get_component_option( $component_id, $product_id );
						$item_priced_individually = $component_option instanceof WC_CP_Product ? $component_option->is_priced_individually() : false;
					}
				}

				if ( self::has_fixed_points( $composite ) || false === $item_priced_individually ) {
					$points = 0;
				} else {
					$points = WC_Points_Rewards_Manager::calculate_points( $product->get_price() );
				}
			}
		}

		return $points;
	}

	/**
	 * Points and Rewards single product message for Composites that contain individually-priced components.
	 *
	 * @param  string                     $message
	 * @param  WC_Points_Rewards_Product  $points_n_rewards
	 * @return string
	 */
	public static function points_rewards_composite_message( $message, $points_n_rewards ) {

		global $product;

		if ( $product->is_type( 'composite' ) && self::$single_product_message_filter_active ) {

			if ( false === self::has_fixed_points( $product ) && $product->contains( 'priced_individually' ) ) {

				$max_composite_price = $product->get_composite_price( 'max' );
				$min_composite_price = $product->get_composite_price( 'min' );

				if ( '' !== $max_composite_price ) {
					self::$composite_price_max = $max_composite_price;
				} else {
					self::$composite_price_min = $min_composite_price;
				}

				// 'WC_Points_Rewards_Product' relies on 'get_price', which only returns the base price of a composite.
				add_filter( 'woocommerce_product_get_price', array( __CLASS__, 'replace_price' ), 9999, 2 );

				$composite_points = WC_Points_Rewards_Product::get_points_earned_for_product_purchase( $product );

				if ( '' !== $max_composite_price ) {

					if ( $max_composite_price === $min_composite_price ) {
						self::$single_product_message_filter_active = false;
						$message = $points_n_rewards->render_product_message();
						self::$single_product_message_filter_active = true;
					} else {
						$message = $points_n_rewards->create_variation_message_to_product_summary( $composite_points );
					}

				} else {
					$message = $points_n_rewards->create_at_least_message_to_product_summary( $composite_points );
				}

				remove_filter( 'woocommerce_product_get_price', array( __CLASS__, 'replace_price' ), 9999, 2 );

				self::$composite_price_min = self::$composite_price_max = false;
			}
		}

		return $message;
	}

	/**
	 * Filter option_wc_points_rewards_single_product_message in order to force 'WC_Points_Rewards_Product::render_variation_message' to display nothing.
	 */
	public static function points_rewards_remove_price_html_messages( $args ) {
		add_filter( 'option_wc_points_rewards_single_product_message', array( __CLASS__, 'return_empty_message' ) );
	}

	/**
	 * Restore option_wc_points_rewards_single_product_message. Forced in order to force 'WC_Points_Rewards_Product::render_variation_message' to display nothing.
	 */
	public static function points_rewards_restore_price_html_messages( $args ) {
		remove_filter( 'option_wc_points_rewards_single_product_message', array( __CLASS__, 'return_empty_message' ) );
	}

	/**
	 * @see points_rewards_remove_price_html_messages
	 *
	 * @param  string  $message
	 * @return void
	 */
	public static function return_empty_message( $message ) {
		return false;
	}

	/**
	 * Filter composite price returned by 'get_price' to return the min/max composite price.
	 *
	 * @param  mixed                 $price
	 * @param  WC_Product_Composite  $product
	 * @return mixed
	 */
	public static function replace_price( $price, $product ) {
		if ( false !== self::$composite_price_max ) {
			$price = self::$composite_price_max;
		} elseif ( false !== self::$composite_price_min ) {
			$price = self::$composite_price_min;
		}
		return $price;
	}

	/**
	 * True if the composite has fixed product- or category-level points.
	 *
	 * @param  WC_Product_Composite  $composite
	 * @return boolean
	 */
	private static function has_fixed_points( $composite ) {

		$composite_product_points  = WC_Points_Rewards_Product::get_product_points( $composite );
		$composite_category_points = is_callable( array( 'WC_Points_Rewards_Product', 'get_category_points' ) ) ? WC_Points_Rewards_Product::get_category_points( $composite ) : '';

		return is_numeric( $composite_product_points ) || is_numeric( $composite_category_points );
	}
}

WC_CP_PnR_Compatibility::init();
