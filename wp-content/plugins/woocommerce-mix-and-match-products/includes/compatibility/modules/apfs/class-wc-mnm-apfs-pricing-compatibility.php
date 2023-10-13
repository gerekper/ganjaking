<?php
/**
 * All Products for Subscriptions - Adds support for per-item priced containers
 *
 * @package  WooCommerce Mix and Match Products/Compatibility
 * @since    2.0.0
 * @version  2.3.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Main WC_MNM_APFS_Pricing_Compatibility class
 */
if ( ! class_exists( 'WC_MNM_APFS_Pricing_Compatibility' ) ) :

	class WC_MNM_APFS_Pricing_Compatibility {

		/**
		 * Hooks for MNM/APFS Per-Item Pricing Compat.
		 */
		public static function add_hooks() {

			remove_filter( 'wcsatt_product_subscription_schemes', array( 'WCS_ATT_Integration_PB_CP', 'get_product_bundle_schemes' ), 10, 2 );
			add_filter( 'wcsatt_product_subscription_schemes', array( __CLASS__, 'get_product_bundle_schemes' ), 10, 2 );

			add_action( 'wcsatt_add_price_filters', array( __CLASS__, 'add_price_filters' ) );
			add_action( 'wcsatt_remove_price_filters', array( __CLASS__, 'remove_price_filters' ) );

			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );
			add_action( 'woocommerce_mix-and-match_add_to_cart', array( __CLASS__, 'load_script' ), 20 );
			add_filter( 'wc_mnm_container_price_data', array( __CLASS__, 'container_price_data' ) );

			// Front end strings.
			add_filter( 'wcsatt_single_product_one_time_option_has_price', array( __CLASS__, 'update_one_time_price' ), 10, 2 );
			add_filter( 'wcsatt_price_html_discount_format', array( __CLASS__, 'html_discount_format' ), 10, 2 );

			// Temporarily disable APFS price filters when getting the child item Regular price.
			add_action( 'wc_mnm_child_item_get_unfiltered_regular_price_start', array( __CLASS__, 'remove_regular_price_filters' ) );
			add_action( 'wc_mnm_child_item_get_unfiltered_regular_price_end', array( __CLASS__, 'add_regular_price_filters' ) );
		}

		/**
		 * Per-item pricing container base prices can be empty strings, which throws notice in Subscriptions.
		 */
		public static function add_price_filters( $context = '' ) {
			if ( in_array( $context, array( 'price', '' ) ) ) {
				add_filter( 'woocommerce_product_get_price', array( __CLASS__, 'filter_price' ), -1, 2 );
				add_filter( 'woocommerce_product_get_price', array( __CLASS__, 'filter_price' ), 101, 2 );
			}
		}

		/**
		 * Per-item pricing container base prices can be empty strings, which throws notice in Subscriptions.
		 */
		public static function remove_price_filters( $context = '' ) {
			if ( in_array( $context, array( 'price', '' ) ) ) {
				remove_filter( 'woocommerce_product_get_price', array( __CLASS__, 'filter_price' ), -1, 2 );
				remove_filter( 'woocommerce_product_get_price', array( __CLASS__, 'filter_price' ), 101, 2 );
			}
		}

		/**
		 * Filter get_price() calls to take scheme price overrides into account.
		 *
		 * @param  double      $price
		 * @param  WC_Product  $product
		 * @return double
		 */
		public static function filter_price( $price, $product ) {

			if ( WCS_ATT_Product::is_subscription( $product ) ) {

				if ( '' === $price && $product->is_type( 'mix-and-match' ) && $product->is_priced_per_product() ) {
					$price = (float) $price;
				}
			}

			return $price;
		}


		/**
		 * Remove APFS price filters before retrieving the bundled item Regular Price.
		 *
		 * @since 2.3.1
		 */
		public static function remove_regular_price_filters() {
			WCS_ATT_Product_Price_Filters::remove( 'price' );
		}

		/**
		 * Re-add APFS price filters after retrieving the bundled item Regular Price.
		 *
		 * @since 2.3.1
		 */
		public static function add_regular_price_filters() {
			WCS_ATT_Product_Price_Filters::add( 'price' );
		}


		/**
		 * Sub schemes attached on a Product Bundle should not work if the bundle contains a non-convertible product, such as a "legacy" subscription.
		 *
		 * WCS_ATT_Integration_PB_CP::bundle_contains_subscription() is private and can't be used here, so duplicate it's logic.
		 *
		 * @param  array       $schemes
		 * @param  WC_Product  $product
		 * @return array
		 */
		public static function get_product_bundle_schemes( $schemes, $product ) {

			if ( $product->is_type( 'bundle' ) && function_exists( 'WC_PB' ) ) {
				if ( version_compare( WC_PB()->version, '5.0.0' ) < 0 ) {
					$contains_subs = $product->contains_sub();
				} else {
					$contains_subs = $product->contains( 'subscription' );
				}
				if ( $contains_subs ) {
					$schemes = array();
				}
			}

			return $schemes;
		}

		/**
		 * Register our custom script.
		 *
		 * @return void
		 */
		public static function enqueue_scripts() {
			$suffix      = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			$script_path = 'assets/js/frontend/add-to-cart-mnm-apfs-compat' . $suffix . '.js';

			wp_register_script( 'wc-add-to-cart-mnm-apfs', WC_Mix_and_Match()->plugin_url() . '/' . $script_path, array( 'jquery', 'jquery-blockui', 'wc-add-to-cart-mnm', 'wcsatt-single-product' ), WC_Mix_and_Match()->get_file_version( WC_MNM_ABSPATH . $script_path ), true );
		}

		/**
		 * Load our custom script on Mix and Match products.
		 *
		 * @return void
		 */
		public static function load_script() {
			wp_enqueue_script( 'wc-add-to-cart-mnm-apfs' );
		}

		/**
		 * Modify container price data attributes to always show the total updating for schemes.
		 *
		 * @param  array       $data
		 * @return array
		 */
		public static function container_price_data( $data ) {
			$data['hide_total_on_validation_fail'] = 'no';
			return $data;
		}

		/**
		 * Dynamically update one-time price.
		 *
		 * @param  bool $has_price
		 * @param  WC_Product       $product
		 * @return bool
		 */
		public static function update_one_time_price( $has_price, $product ) {
			if ( $product->is_type( 'mix-and-match' ) && apply_filters( 'wc_mnm_subscription_update_one_time_price', $product->is_priced_per_product() && $product->get_container_price( 'min' ) !== $product->get_container_price( 'max' ), $product ) ) {
				$has_price = true;
			}
			return $has_price;
		}

		/**
		 * Change price strings.
		 *
		 * @param  bool $discount_format
		 * @param  WC_Product       $product
		 * @return bool
		 */
		public static function html_discount_format( $discount_format, $product ) {
			if ( $product->is_type( 'mix-and-match' ) && $product->is_priced_per_product() && $product->get_container_price( 'min' ) !== $product->get_container_price( 'max' ) ) {
				$discount_format = true;
			}
			return $discount_format;
		}
	} // End class: do not remove or there will be no more guacamole for you.

endif; // End class_exists check.

WC_MNM_APFS_Pricing_Compatibility::add_hooks();
