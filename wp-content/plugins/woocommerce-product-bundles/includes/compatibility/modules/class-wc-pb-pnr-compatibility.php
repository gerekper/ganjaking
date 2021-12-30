<?php
/**
 * WC_PB_PnR_Compatibility class
 *
 * @package  WooCommerce Product Bundles
 * @since    4.11.4
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Points and Rewards Compatibility.
 *
 * @version  5.5.0
 */
class WC_PB_PnR_Compatibility {

	/**
	 * Bundle points - @see 'WC_PB_PnR_Compatibility::replace_points'.
	 * @var boolean
	 */
	private static $bundle_price_max = false;
	private static $bundle_price_min = false;

	/**
	 * Bypass 'wc_points_rewards_single_product_message' filter.
	 * @var boolean
	 */
	private static $single_product_message_filter_active = true;

	/**
	 * Initialize.
	 */
	public static function init() {

		// Points earned filters.
		add_filter( 'woocommerce_points_earned_for_cart_item', array( __CLASS__, 'points_earned_for_bundled_cart_item' ), 10, 3 );
		add_filter( 'woocommerce_points_earned_for_order_item', array( __CLASS__, 'points_earned_for_bundled_order_item' ), 10, 5 );

		// Change earn points message for Bundles that contain individually-priced items.
		add_filter( 'wc_points_rewards_single_product_message', array( __CLASS__, 'points_rewards_bundle_message' ), 10, 2 );

		// Remove PnR message from bundled variations.
		add_filter( 'option_wc_points_rewards_single_product_message', array( __CLASS__, 'return_empty_message' ) );
	}

	/**
	 * Return zero points for bundled cart items if container item has product- or category-level points or bundled item is not priced individually.
	 *
	 * @param  int     $points
	 * @param  string  $cart_item_key
	 * @param  array   $cart_item_values
	 * @return int
	 */
	public static function points_earned_for_bundled_cart_item( $points, $cart_item_key, $cart_item_values ) {

		if ( $parent = wc_pb_get_bundled_cart_item_container( $cart_item_values ) ) {

			$bundle          = $parent[ 'data' ];
			$bundled_item_id = $cart_item_values[ 'bundled_item_id' ];
			$bundled_item    = $bundle->get_bundled_item( $bundled_item_id );

			if ( self::has_fixed_points( $bundle ) || false === $bundled_item->is_priced_individually() ) {
				$points = 0;
			} else {
				$points = WC_Points_Rewards_Manager::calculate_points( $cart_item_values[ 'data' ]->get_price() );
			}
		}

		return $points;
	}

	/**
	 * Return zero points for bundled order items if container item has product- or category-level points or bundled item is not priced individually.
	 *
	 * @param  int       $points
	 * @param  string    $item_key
	 * @param  array     $item
	 * @param  WC_Order  $order
	 * @return int
	 */
	public static function points_earned_for_bundled_order_item( $points, $product, $item_key, $item, $order ) {

		if ( $parent_item = wc_pb_get_bundled_order_item_container( $item, $order ) ) {

			$bundled_item_priced_individually = isset( $item[ 'bundled_item_priced_individually' ] ) ? 'yes' === $item[ 'bundled_item_priced_individually' ] : null;

			if ( $bundle = wc_get_product( $parent_item[ 'product_id' ] ) ) {

				// Back-compat.
				if ( null === $bundled_item_priced_individually ) {
					if ( isset( $parent_item[ 'per_product_pricing' ] ) ) {
						$bundled_item_priced_individually = 'yes' === $parent_item[ 'per_product_pricing' ];
					} elseif ( isset( $item[ 'bundled_item_id' ] ) ) {
						$bundled_item_id                  = $item[ 'bundled_item_id' ];
						$bundled_item                     = $bundle->get_bundled_item( $bundled_item_id );
						$bundled_item_priced_individually = ( $bundled_item instanceof WC_Bundled_Item ) ? $bundled_item->is_priced_individually() : false;
					}
				}

				if ( self::has_fixed_points( $bundle ) || false === $bundled_item_priced_individually ) {
					$points = 0;
				} else {
					$points = WC_Points_Rewards_Manager::calculate_points( $product->get_price() );
				}
			}
		}

		return $points;
	}

	/**
	 * Points and Rewards single product message for Bundles.
	 *
	 * @param  string                     $message
	 * @param  WC_Points_Rewards_Product  $points_n_rewards
	 * @return string
	 */
	public static function points_rewards_bundle_message( $message, $points_n_rewards ) {

		global $product;

		if ( $product->is_type( 'bundle' ) && self::$single_product_message_filter_active ) {

			if ( false === self::has_fixed_points( $product ) && $product->contains( 'priced_individually' ) ) {

				$max_bundle_price = $product->get_bundle_price( 'max' );
				$min_bundle_price = $product->get_bundle_price( 'min' );

				if ( '' !== $max_bundle_price ) {
					self::$bundle_price_max = $max_bundle_price;
				} else {
					self::$bundle_price_min = $min_bundle_price;
				}

				// 'WC_Points_Rewards_Product' relies on 'get_price', which only returns the base price of a bundle.
				add_filter( 'woocommerce_product_get_price', array( __CLASS__, 'replace_price' ), 9999, 2 );

				$bundle_points = WC_Points_Rewards_Product::get_points_earned_for_product_purchase( $product );

				if ( '' !== $max_bundle_price ) {

					if ( $max_bundle_price === $min_bundle_price ) {
						self::$single_product_message_filter_active = false;
						$message = $points_n_rewards->render_product_message();
						self::$single_product_message_filter_active = true;
					} else {
						$message = $points_n_rewards->create_variation_message_to_product_summary( $bundle_points );
					}

				} else {
					$message = $points_n_rewards->create_at_least_message_to_product_summary( $bundle_points );
				}

				remove_filter( 'woocommerce_product_get_price', array( __CLASS__, 'replace_price' ), 9999, 2 );

				self::$bundle_price_min = self::$bundle_price_max = false;
			}
		}

		return $message;
	}

	/**
	 * @see points_rewards_remove_price_html_messages
	 *
	 * @param  string  $message
	 * @return void
	 */
	public static function return_empty_message( $message ) {
		if ( did_action( 'woocommerce_bundled_product_price_filters_added' ) > did_action( 'woocommerce_bundled_product_price_filters_removed' ) ) {
			$message = false;
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
		if ( false !== self::$bundle_price_max ) {
			$price = self::$bundle_price_max;
		} elseif ( false !== self::$bundle_price_min ) {
			$price = self::$bundle_price_min;
		}
		return $price;
	}

	/**
	 * True if the bundle has fixed product- or category-level points.
	 *
	 * @param  WC_Product_Bundle  $bundle
	 * @return boolean
	 */
	private static function has_fixed_points( $bundle ) {

		$bundle_product_points  = WC_Points_Rewards_Product::get_product_points( $bundle );
		$bundle_category_points = is_callable( array( 'WC_Points_Rewards_Product', 'get_category_points' ) ) ? WC_Points_Rewards_Product::get_category_points( $bundle ) : '';

		return is_numeric( $bundle_product_points ) || is_numeric( $bundle_category_points );
	}
}

WC_PB_PnR_Compatibility::init();
