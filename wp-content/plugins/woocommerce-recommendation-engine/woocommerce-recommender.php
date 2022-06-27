<?php

/*
  Plugin Name: WooCommerce Recommendation Engine
 * Plugin URI: http://woothemes.com/woocommerce
 * Description: WooCommerce Recommendation Engine is a smart recommendation engine for your store, providing automatic cross sells based on users viewing and purcahsing history.
 * Version: 3.2.9
 * Author: Element Stark
 * Author URI: http://www.elementstark.com
 * Requires at least: 3.8.0
 * Tested up to: 6.0
 *
 * Text Domain: wc_recommender
 * Copyright: © 2009-2022 Element Stark LLC.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html

 * Woo: 216821:a3d370f38edc35cdc5bc41a4041ed308
 * WC requires at least: 3.0.0
 * WC tested up to: 6.5
 */

/**
 * Required functions
 */
if ( !function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), 'a3d370f38edc35cdc5bc41a4041ed308', '216821' );

if ( is_woocommerce_active() ) {


	/**
	 * woocommerce_product_addons class
	 * */
	if ( !class_exists( 'WC_Recommendation_Engine' ) ) {

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
			public $similar_products = array();
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
				require 'classes/class-wc-recommender-sorting-helper.php';
				require 'classes/class-wc-recommender-compatibility.php';


				add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
				add_action('plugins_loaded', array($this, 'register_cli_command'));

				if ( is_admin() ) {
					add_action( 'admin_notices', array( self::$message_controller, 'show_messages' ) );


					require 'admin/class-wc-recommender-admin.php';
					require 'admin/class-wc-recommender-table-recommendations.php';
					require 'admin/class-wc-recommender-table-session-history.php';

					WC_Recommender_Admin::register();


					if ( !defined( 'DOING_AJAX' ) ) {
						require 'admin/class-wc-recommender-installer.php';
						require 'admin/class-wc-recommender-admin-settings.php';

						WC_Recommender_Admin_Settings::register();

						$this->install();
					}
				} else {
					add_action( 'woocommerce_init', array( $this, 'on_woocommerce_init' ) );
				}


				//Record product view
				add_action( 'template_redirect', array( &$this, 'on_template_redirect' ) );

				//Record that someone added the item to the cart.
				add_action( 'woocommerce_add_to_cart', array( &$this, 'on_add_to_cart' ), 10, 6 );


				// Record data when a new order is placed
				add_action( 'woocommerce_checkout_order_processed', array( &$this, 'record_item_ordered' ), 100 );

				// Record data when a new order is completed
				add_action( 'woocommerce_order_status_completed', array( &$this, 'record_update_order' ) );

				//Record the cancellation and product removal actions.
				add_action( 'woocommerce_order_status_refunded', array( &$this, 'record_update_order' ) );
				add_action( 'woocommerce_order_status_cancelled', array( &$this, 'record_update_order' ) );
				add_action( 'woocommerce_order_status_processing', array( &$this, 'record_update_order' ) );
				add_action( 'woocommerce_order_status_on-hold', array( &$this, 'record_update_order' ) );
				add_action( 'woocommerce_order_status_failed', array( &$this, 'record_update_order' ) );
				add_action( 'woocommerce_order_status_pending', array( &$this, 'record_update_order' ) );

				add_action( 'delete_posts', array( &$this, 'on_delete_post' ) );
			}

			/**
			 * Localisation
			 */
			public function load_plugin_textdomain() {
				$locale = apply_filters( 'plugin_locale', get_locale(), 'wc_recommender' );
				load_textdomain( 'wc_recommender', WP_LANG_DIR . '/woocommerce/wc_recommender-' . $locale . '.mo' );
				load_plugin_textdomain( 'wc_recommender', false, plugin_basename( dirname( __FILE__ ) ) . '/i18n/languages' );
			}

			/**
			 * Installs the database tables and initial recommendations based on previous orders.
			 */
			public function install() {
				register_activation_hook( __FILE__, array( $this, 'activate' ) );
				register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

				if ( get_option( 'woocommerce_recommender_db_version' ) != $this->version ) {
					add_action( 'init', 'install_woocommerce_recommender', 99 );

					if ( !wp_next_scheduled( 'wc_recommender_build' ) ) {
						wp_schedule_event( time(), 'twicedaily', 'wc_recommender_build' );
					}
				}
			}

			/**
			 * Runs when the plugin is activated.
			 */
			public function activate() {
				activate_woocommerce_recommender();
				if ( !wp_next_scheduled( 'wc_recommender_build' ) ) {
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
				if ( !(is_admin() || defined( 'DOING_AJAX' ) ) && !defined( 'DOING_CRON' ) && ! $this->is_rest_api_request() && !is_checkout() && !is_checkout_pay_page() ) {
					//We need to force WooCommerce to set the session cookie
					if ( WC()->session && !WC()->session->has_session() ) {
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
			 * @param int $quantity
			 * @param int|null $variation_id
			 * @param array|null $variation
			 * @param array|null $cart_item_data
			 */
			public function on_add_to_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
				woocommerce_recommender_record_product_in_cart( $product_id );
			}

			/**
			 * Hooks into the new order item action, records the ordered item.
			 *
			 * @param int $order_id
			 */
			public function record_item_ordered( $order_id ) {
				$order       = new WC_Order( $order_id );
				$order_items = $order->get_items();
				foreach ( $order_items as $item ) {
					if ( WC_Recommender_Compatibility::is_wc_version_gte_2_7() ) {
						$product = $item->get_product();
						if ( $product && is_object( $product ) ) {
							if ( $product->is_type( 'variation' ) ) {
								woocommerce_recommender_record_product_ordered( $order_id, $product->get_id(), $order->get_status() );
								woocommerce_recommender_record_product_ordered( $order_id, $product->get_parent_id(), $order->get_status() );
							} else {
								woocommerce_recommender_record_product_ordered( $order_id, $product->get_id(), $order->get_status() );
							}
						}
					} else {
						$product = $order->get_product_from_item( $item );
						if ( $product && is_object( $product ) ) {
							if ( $product->is_type( 'variation' ) ) {
								woocommerce_recommender_record_product_ordered( $order_id, $product->get_id(), $order->status );
								woocommerce_recommender_record_product_ordered( $order_id, $product->get_parent_id(), $order->status );
							} else {
								woocommerce_recommender_record_product_ordered( $order_id, $product->get_id(), $order->status );
							}
						}
					}
				}
			}

			/**
			 * Hooks into the update order action, updates the status of the previously recorded order item.
			 *
			 * @param int $order_id
			 */
			public function record_update_order( $order_id ) {
				$order       = new WC_Order( $order_id );
				$order_items = $order->get_items();
				foreach ( $order_items as $item ) {

					if ( WC_Recommender_Compatibility::is_wc_version_gte_2_7() ) {
						$product = $item->get_product();
						if ( $product && is_object( $product ) ) {
							if ( $product->is_type( 'variation' ) ) {
								woocommerce_recommender_update_recorded_product( $order_id, $product->get_id(), $order->get_status() );
								woocommerce_recommender_update_recorded_product( $order_id, $product->get_parent_id(), $order->get_status() );
							} else {
								woocommerce_recommender_update_recorded_product( $order_id, $product->get_id(), $order->get_status() );
							}
						}
					} else {
						$product = $order->get_product_from_item( $item );
						if ( $product && is_object( $product ) ) {
							if ( $product->is_type( 'variable' ) ) {
								woocommerce_recommender_update_recorded_product( $order_id, $product->get_id(), $order->status );
								woocommerce_recommender_update_recorded_product( $order_id, $product->variation_id, $order->status );
							} else {
								woocommerce_recommender_update_recorded_product( $order_id, $product->get_id(), $order->status );
							}
						}
					}
				}
			}

			/**
			 * Hooks into the delete_post action.  Removes items from session history when the products and orders are deleted.
			 * @global wpdb $wpdb
			 *
			 * @param int $post_id
			 */
			public function on_delete_post( $post_id ) {
				global $wpdb;

				$type = $wpdb->get_var( $wpdb->prepare( 'SELECT post_type FROM $wpdb->posts WHERE post_id = %d', $post_id ) );

				if ( $type && $type == 'shop_order' ) {
					$order       = new WC_Order( $post_id );
					$order_items = $order->get_items();

					$product_ids = array();
					foreach ( $order_items as $item ) {

						if ( WC_Recommender_Compatibility::is_wc_version_gte_2_7() ) {
							$product = $item->get_product();
						} else {
							$product = $order->get_product_from_item( $item );
						}
						$product_ids[] = $product->get_id();
					}

					$query = $wpdb->prepare( "DELETE FROM $this->db_tbl_session_activity WHERE product_id IN (%s) AND order_id > 0)", implode( ',', $product_ids ) );
					$wpdb->query( $query );
				} elseif ( $type && ( $type == 'product' || $type == 'product_variation' ) ) {
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
