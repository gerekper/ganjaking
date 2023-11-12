<?php

/*
  Plugin Name: WooCommerce Recommendation Engine
 * Plugin URI: http://woothemes.com/woocommerce
 * Description: WooCommerce Recommendation Engine is a smart recommendation engine for your store, providing automatic cross-sells based on users viewing and purchasing history.
 * Version: 3.3.1
 * Author: Element Stark
 * Author URI: http://www.elementstark.com
 * Requires at least: 6.0
 * Tested up to: 6.3
 *
 * Text Domain: wc_recommender
 * Copyright: © 2009-2023 Element Stark LLC.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html

 * Woo: 216821:a3d370f38edc35cdc5bc41a4041ed308
 * WC requires at least: 7.0
 * WC tested up to: 8.2
 */

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

add_action(
	'before_woocommerce_init',
	function() {
		if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
		}
	}
);

if ( is_woocommerce_active() ) {
	/**
	 * woocommerce_product_addons class
	 * */
	if ( ! class_exists( 'WC_Recommendation_Engine' ) ) {

		class WC_Recommendation_Engine {

			private static $instance;

			public static function instance() {
				if ( self::$instance == null ) {
					self::$instance = new WC_Recommendation_Engine();
				}

				return self::$instance;
			}

			public static $message_controller;
			public $template_url;
			public $similar_products = [];
			public $version = '3.0.0';
			public $db_tbl_session_activity;
			public $db_tbl_recommendations;

			/**
			 * Creates a new instance of the WC_Recommendation_Engine.
			 * @global type $wpdb
			 */
			public function __construct() {
				global $wpdb;

				require 'classes/class-wc-recommender-messages.php';
				self::$message_controller = new WC_Recommender_Messages();

				define( 'WOOCOMMERCE_RECOMMENDER_VERSION', $this->version );
				$this->db_tbl_session_activity = $wpdb->prefix . 'woocommerce_session_activity';
				$this->db_tbl_recommendations  = $wpdb->prefix . 'woocommerce_recommendations';

				$this->template_url = apply_filters( 'woocommerce_recommender_template_url', 'recommendation-engine/' );

				//Global Includes
				require 'woocommerce-recommender-functions.php';
				require 'woocommerce-recommender-hooks.php';
				require 'woocommerce-recommender-template.php';
				require 'woocommerce-recommender-shortcodes.php';

				require 'widgets/widget-init.php';
				require 'classes/class-wc-recommender-recorder.php';
				require 'classes/class-wc-recommender-sorting-helper.php';
				require 'classes/class-wc-recommender-compatibility.php';

				add_action( 'plugins_loaded', [ $this, 'load_plugin_text_domain' ] );
				add_action( 'plugins_loaded', [ $this, 'register_cli_command' ] );

				if ( is_admin() ) {
					add_action( 'admin_notices', [ self::$message_controller, 'show_messages' ] );

					require 'admin/class-wc-recommender-admin.php';
					require 'admin/class-wc-recommender-table-recommendations.php';
					require 'admin/class-wc-recommender-table-session-history.php';

					WC_Recommender_Admin::register();


					if ( ! defined( 'DOING_AJAX' ) ) {
						require 'admin/class-wc-recommender-installer.php';
						require 'admin/class-wc-recommender-admin-settings.php';

						WC_Recommender_Admin_Settings::register();

						$this->install();
					}
				} else {
					add_action( 'woocommerce_init', [ $this, 'on_woocommerce_init' ] );
				}


				//Record product view
				add_action( 'template_redirect', [ $this, 'on_template_redirect' ] );

				//Record that someone added the item to the cart.
				add_action( 'woocommerce_add_to_cart', [ $this, 'on_add_to_cart' ], 10, 2 );


				// Record data when a new order is placed
				add_action( 'woocommerce_checkout_order_processed', [ $this, 'record_items_ordered' ], 100 );

				// Record data when a new order is completed
				add_action( 'woocommerce_order_status_completed', [ $this, 'record_update_order' ] );

				//Record the cancellation and product removal actions.
				add_action( 'woocommerce_order_status_refunded', [ $this, 'record_update_order' ] );
				add_action( 'woocommerce_order_status_cancelled', [ $this, 'record_update_order' ] );
				add_action( 'woocommerce_order_status_processing', [ $this, 'record_update_order' ] );
				add_action( 'woocommerce_order_status_on-hold', [ $this, 'record_update_order' ] );
				add_action( 'woocommerce_order_status_failed', [ $this, 'record_update_order' ] );
				add_action( 'woocommerce_order_status_pending', [ $this, 'record_update_order' ] );

				// Clean up the session history when an order is deleted.
				add_action( 'woocommerce_delete_order', [ $this, 'on_woocommerce_delete_order' ] );

				// Clean up the session history when a product is deleted.  Use delete post instead of delete product to catch variations.
				add_action( 'delete_post', [ $this, 'on_delete_post' ] );
			}

			/**
			 * Localisation
			 */
			public function load_plugin_text_domain() {
				$locale = apply_filters( 'plugin_locale', get_locale(), 'wc_recommender' );
				load_textdomain( 'wc_recommender', WP_LANG_DIR . '/woocommerce/wc_recommender-' . $locale . '.mo' );
				load_plugin_textdomain( 'wc_recommender', false, plugin_basename( dirname( __FILE__ ) ) . '/i18n/languages' );
			}

			/**
			 * Installs the database tables and initial recommendations based on previous orders.
			 */
			public function install() {
				register_activation_hook( __FILE__, [ $this, 'activate' ] );
				register_deactivation_hook( __FILE__, [ $this, 'deactivate' ] );

				if ( get_option( 'woocommerce_recommender_db_version' ) != $this->version ) {
					add_action( 'init', 'install_woocommerce_recommender', 99 );

					if ( ! wp_next_scheduled( 'wc_recommender_build' ) ) {
						wp_schedule_event( time(), 'twicedaily', 'wc_recommender_build' );
					}
				}
			}

			/**
			 * Runs when the plugin is activated.
			 */
			public function activate() {
				activate_woocommerce_recommender();
				if ( ! wp_next_scheduled( 'wc_recommender_build' ) ) {
					wp_schedule_event( time(), 'twicedaily', 'wc_recommender_build' );
				}
			}

			public function deactivate() {
				wp_clear_scheduled_hook( 'wc_recommender_build' );
			}

			public function register_cli_command() {
				// WP-CLI includes.
				if ( defined( 'WP_CLI' ) && WP_CLI ) {
					require_once( 'classes/class-wc-recommender-cli.php' );
				}
			}

			public function on_woocommerce_init() {
				if ( ! ( is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' ) && ! $this->is_rest_api_request() && ! is_checkout() && ! is_checkout_pay_page() ) {
					//We need to force WooCommerce to set the session cookie
					if ( WC()->session && ! WC()->session->has_session() ) {
						WC()->session->set_customer_session_cookie( true );
					}
				}
			}

			private function is_rest_api_request() {
				if ( empty( $_SERVER['REQUEST_URI'] ) ) {
					return false;
				}

				$rest_prefix         = trailingslashit( rest_get_url_prefix() );
				$is_rest_api_request = ( false !== strpos( $_SERVER['REQUEST_URI'], $rest_prefix ) ); // phpcs:disable WordPress.Security.ValidatedSanitizedInput.MissingUnslash, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

				return apply_filters( 'woocommerce_is_rest_api_request', $is_rest_api_request );
			}

			/**
			 * Hooks into the template_redirect to record product views.
			 */
			public function on_template_redirect() {
				if ( is_single() && is_product() ) {
					woocommerce_recommender_record_product_view( get_the_ID() );
				}
			}

			/**
			 * Hooks into the add_to_cart action to record items being added to the cart.
			 *
			 * @param string $cart_item_key
			 * @param int $product_id
			 */
			public function on_add_to_cart( string $cart_item_key, int $product_id ) {
				woocommerce_recommender_record_product_in_cart( $product_id );
			}

			/**
			 * Hooks into the new order item action, records the ordered item.
			 *
			 * @param int|string $order_id
			 */
			public function record_items_ordered( $order_id ) {
				$order       = new WC_Order( $order_id );
				$order_items = $order->get_items();
				foreach ( $order_items as $order_item ) {

					// Skip if not a WC_Order_Item_Product or not a line item.
					if ( ! is_a( $order_item, 'WC_Order_Item_Product' ) || ! $order_item->is_type( 'line_item' ) ) {
						continue;
					}

					$product = $order_item->get_product();
					if ( $product && is_object( $product ) ) {
						if ( $product->is_type( 'variation' ) ) {
							woocommerce_recommender_record_product_ordered( $order_id, $product->get_id(), $order->get_status() );
							woocommerce_recommender_record_product_ordered( $order_id, $product->get_parent_id(), $order->get_status() );
						} else {
							woocommerce_recommender_record_product_ordered( $order_id, $product->get_id(), $order->get_status() );
						}
					}
				}
			}

			/**
			 * Hooks into the update order action, updates the status of the previously recorded order item.
			 *
			 * @param int $order_id
			 */
			public function record_update_order( int $order_id ) {
				$order       = new WC_Order( $order_id );
				$order_items = $order->get_items();
				foreach ( $order_items as $order_item ) {

					// Skip if not a WC_Order_Item_Product or not a line item.
					if ( ! is_a( $order_item, 'WC_Order_Item_Product' ) || ! $order_item->is_type( 'line_item' ) ) {
						continue;
					}

					$product = $order_item->get_product();
					if ( $product && is_object( $product ) ) {
						if ( $product->is_type( 'variation' ) ) {
							woocommerce_recommender_update_recorded_product( $order_id, $product->get_id(), $order->get_status() );
							woocommerce_recommender_update_recorded_product( $order_id, $product->get_parent_id(), $order->get_status() );
						} else {
							woocommerce_recommender_update_recorded_product( $order_id, $product->get_id(), $order->get_status() );
						}
					}

				}
			}

			/**
			 * Hooks into the on_woocommerce_delete_order action to remove the orders items from the session history.
			 *
			 * @param int $order_id
			 */
			public function on_woocommerce_delete_order( int $order_id ) {
				global $wpdb, $woocommerce_recommender;
				$wpdb->query( $wpdb->prepare( "DELETE FROM $woocommerce_recommender->db_tbl_session_activity WHERE order_id = %d", $order_id ) );
			}

			/**
			 * Hooks into the delete_post action.  Removes items from session history when the products and orders are deleted.
			 *
			 * @param int $post_id
			 *
			 * @global wpdb $wpdb
			 *
			 */
			public function on_delete_post( int $post_id ) {
				global $wpdb;
				$type = $wpdb->get_var( $wpdb->prepare( 'SELECT post_type FROM $wpdb->posts WHERE post_id = %d', $post_id ) );
				if ( $type && ( $type == 'product' || $type == 'product_variation' ) ) {
					$query = $wpdb->prepare( "DELETE FROM $this->db_tbl_session_activity WHERE product_id = %d", $post_id );
					$wpdb->query( $query );
				}

				$wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_wc_recommender_%'" );
			}

			/**
			 * Returns the URL for this plugin.
			 * @return string The base url for this plugin
			 */
			public function plugin_url() {
				return plugin_dir_url( __FILE__ );
			}

			/**
			 * Returns the base file system path for this plugin.
			 * @return string.  The base file system path for this plugin.
			 */
			public function plugin_dir() {
				return plugin_dir_path( __FILE__ );
			}

			/**
			 * Wrapper for get_option.  Use this instead of calling get_option directly for future expansion.
			 *
			 * @param string $key
			 * @param mixed $default
			 *
			 * @return mixed
			 */
			public function get_setting( $key, $default = null ) {
				return get_option( $key, $default );
			}

			/*
			 * Templates and Query Modifications
			 */

			/**
			 * Hooks into the after_product_summary action to ouput the recommendations.
			 */
			public function on_after_product_summary() {
				$similar_products = woocommerce_recommender_get_simularity( get_the_ID() );

				if ( $similar_products ) {
					echo 'You might also like<br />';
					echo '<ul>';
					foreach ( $similar_products as $product_id => $score ) {
						if ( $score > 0 ) {
							$product = wc_get_product( $product_id );
							echo '<li>' . $product->get_title() . '</li>';
						}
					}
					echo '</ul>';
				}
			}

			/**
			 * Returns the URL for this plugin.
			 * @return string The base url for this plugin
			 */
			public static function url() {
				//TODO:  Remove the references to the non-static version of this method.
				return plugin_dir_url( __FILE__ );
			}

			/**
			 * Returns the base file system path for this plugin.
			 * @return string.  The base file system path for this plugin.
			 */
			public static function directory() {
				//TODO:  Remove the references to the non-static version of this method.
				return plugin_dir_path( __FILE__ );
			}

			/** Messages ************************************************************ */
			public static function add_message( $message ) {
				self::$message_controller->add_message( $message );
			}

			public static function add_error( $error ) {
				self::$message_controller->add_error( $error );
			}

		}

	}

	/**
	 *
	 * @return WC_Recommendation_Engine
	 */
	function wcre() {
		return WC_Recommendation_Engine::instance();
	}

	$GLOBALS['woocommerce_recommender'] = wcre();
}
