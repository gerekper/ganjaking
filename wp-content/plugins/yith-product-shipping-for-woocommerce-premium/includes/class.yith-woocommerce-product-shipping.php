<?php

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_WooCommerce_Product_Shipping' ) ) {

	/**
	 * YITH WooCommerce Product Shipping
	 *
	 * @since 1.0.0
	 */
	class YITH_WooCommerce_Product_Shipping {

		public $version;
		public $frontend = null;
		public $admin = null;

		protected static $_instance = null;
		
		/**
		 * Construct
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// if ( ! isset( $_SESSION ) ){ @ session_start(); }

			/**
			 * Scripts
			 */
			add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );

			/**
			 * Load Plugin Framework
			 */
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			add_action( 'plugins_loaded', array( $this, 'load_privacy' ), 20 );

			/**
			 * Register plugin to licence/update system
			 */
			add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
			add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );

			/**
			 * Database Table
			 */
			if ( YITH_WCPS_DB_VERSION != apply_filters( 'yith_wcps_db_version', get_option( 'yith_wcps_db_version' ) ) ) {
				$this->create_table();
				update_option( 'yith_wcps_db_version', YITH_WCPS_DB_VERSION );
			}

			if ( apply_filters( 'yith_wcps_shipping_method', true ) ) {

				/**
				 * New Shipping Method
				 */
				add_filter( 'woocommerce_shipping_methods', array( $this, 'woocommerce_shipping_methods' ) );

				/**
				 * Cart Shipping Info
				 */
				if ( apply_filters( 'yith_wcps_show_cart_shipping_info', true ) ) {
					add_action( 'woocommerce_after_shipping_rate', array( $this, 'cart_shipping_info' ), 10, 2 );
				}

			}

			/**
			 * Single Product Message
			 */

			add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'single_product_message' ) );
			add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'single_product_message' ) );

		}

		/**
		 * Enqueue Scripts & Style
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function wp_enqueue_scripts() {
			wp_enqueue_style( 'yith-product-shipping-style-style', plugins_url( 'assets/css/yith-wcps-style.css', YITH_WCPS_FILE ) );
			wp_register_script( 'yith-product-shipping-scripts', plugins_url( 'assets/js/yith-wcps-scripts.js', YITH_WCPS_FILE ), array( 'jquery' ), YITH_WCPS_VERSION, true );
			wp_enqueue_script('yith-product-shipping-scripts');
		}

		/**
		 * Load plugin framework
		 *
		 * @since 1.0.0
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}
		}

		/**
		 * Load Privacy
		 */  
		function load_privacy() {
			require_once( YITH_WCPS_DIR . 'includes/class.yith-woocommerce-product-shipping-privacy.php' );
		}

		/**
		 * Register plugins for activation tab
		 *
		 * @return	void
		 * @since	1.0.0
		 * @author	Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_activation() {
			if ( ! class_exists( 'YIT_Plugin_Licence' ) ) {
				require_once YITH_WCPS_DIR . '/plugin-fw/licence/lib/yit-licence.php';
				require_once YITH_WCPS_DIR . '/plugin-fw/licence/lib/yit-plugin-licence.php';
			}
			YIT_Plugin_Licence()->register( YITH_WCPS_INIT, YITH_WCPS_SECRET_KEY, YITH_WCPS_SLUG );
		}

		/**
		 * Register plugins for update tab
		 *
		 * @return	void
		 * @since	1.0.0
		 * @author	Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function register_plugin_for_updates() {
			if ( ! class_exists( 'YIT_Upgrade' ) ) { require_once( YITH_WCPS_DIR . '/plugin-fw/lib/yit-upgrade.php' ); }
			YIT_Upgrade()->register( YITH_WCPS_SLUG, YITH_WCPS_INIT );
		}

		/**
		 * Plugin Istance
		 *
		 * @since	1.0.0
		 */
		public static function instance() {
			if ( is_null( YITH_WooCommerce_Product_Shipping::$_instance ) ) {
				YITH_WooCommerce_Product_Shipping::$_instance = new YITH_WooCommerce_Product_Shipping();
			}
			return YITH_WooCommerce_Product_Shipping::$_instance;
		}

		/**
		 * Create table
		 *
		 * @since 1.0.0
		 */
		function create_table() {
			global $wpdb;
			$collate = '';
			if ( $wpdb->has_cap( 'collation' ) ) {
				if ( ! empty( $wpdb->charset ) ) { $collate .= "DEFAULT CHARACTER SET $wpdb->charset"; }
				if ( ! empty( $wpdb->collate ) ) { $collate .= " COLLATE $wpdb->collate"; }
			}
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			$sql = "CREATE TABLE {$wpdb->prefix}yith_wcps_shippings (
				id				bigint(20) NOT NULL AUTO_INCREMENT,
				product_id		bigint(20) NOT NULL,
				vendor_id		bigint(20) NOT NULL,
				role			varchar(250) NOT NULL,
				min_cart_total	varchar(250) NOT NULL,
				max_cart_total	varchar(250) NOT NULL,
				min_cart_qty	int(5) NOT NULL,
				max_cart_qty	int(5) NOT NULL,
				min_quantity	int(5) NOT NULL,
				max_quantity	int(5) NOT NULL,
				min_weight		decimal(10,2) NOT NULL,
				max_weight		decimal(10,2) NOT NULL,
				min_cart_weight	decimal(10,2) NOT NULL,
				max_cart_weight	decimal(10,2) NOT NULL,
				categories		text NOT NULL,
				tags			text NOT NULL,
				geo_exclude		int(1) NOT NULL,
				country_code	varchar(250) NOT NULL,
				state_code		varchar(250) NOT NULL,
				postal_code		text NOT NULL,
				zone			varchar(250) NOT NULL,
				shipping_cost	varchar(250) NOT NULL,
				product_cost	varchar(250) NOT NULL,
				unique_cost		varchar(250) NOT NULL,
				ord				bigint(20) NOT NULL,
				PRIMARY KEY (id)
			) $collate;";
			$wpdb->hide_errors();
			$wpdb->suppress_errors( true );
			$wpdb->show_errors( false );
			dbDelta( $sql );
		}

		/**
		 * Register the shipping method
		 *
		 * @since 1.0.0
		 */
		public function woocommerce_shipping_methods( $methods ) {

			$methods['yith_wc_product_shipping_method'] = 'YITH_WooCommerce_Product_Shipping_Method';
			
			return $methods;
		}

		/**
		 * Cart Shipping Info
		 *
		 * @since 1.0.0
		 */
		public function cart_shipping_info( $method, $index ) {
			if ( $method->id == 'yith_wc_product_shipping_method' ) {

				$cart_unique_cost = 0;

				$html = '';

				foreach( WC()->cart->get_cart() as $cart_item ) {
					$is_not_sold_individually = isset( $values['yith_wapo_sold_individually'] ) && $values['yith_wapo_sold_individually'] ? false : true;
					if ( $is_not_sold_individually ) {
						$item_id	= yit_get_prop( $cart_item['data'],'id' );
						$parent_id	= yit_get_base_product_id( $cart_item['data'] );
						$item_name	= yit_get_prop( $cart_item['data'],'name' );
						$quantity	= $cart_item['quantity'];
						$item_cost		= 0;
						$shipping_cost	= 0;
						$product_cost	= 0;
						$unique_cost	= 0;
						if ( isset( $_SESSION['yith_wcps_info_cart'][$item_id]['shipping_cost'] ) ) { $shipping_cost = $_SESSION['yith_wcps_info_cart'][$item_id]['shipping_cost']; }
						else if ( isset( $_SESSION['yith_wcps_info_cart'][$parent_id]['shipping_cost'] ) ) { $shipping_cost = $_SESSION['yith_wcps_info_cart'][$parent_id]['shipping_cost']; }
						if ( isset( $_SESSION['yith_wcps_info_cart'][$item_id]['product_cost'] ) ) { $product_cost = $_SESSION['yith_wcps_info_cart'][$item_id]['product_cost']; }
						else if ( isset( $_SESSION['yith_wcps_info_cart'][$parent_id]['product_cost'] ) ) { $product_cost = $_SESSION['yith_wcps_info_cart'][$parent_id]['product_cost']; }
						if ( isset( $_SESSION['yith_wcps_info_cart'][$item_id]['unique_cost'] ) ) { $unique_cost = $_SESSION['yith_wcps_info_cart'][$item_id]['unique_cost']; }
						else if ( isset( $_SESSION['yith_wcps_info_cart'][$parent_id]['unique_cost'] ) ) { $unique_cost = $_SESSION['yith_wcps_info_cart'][$parent_id]['unique_cost']; }

						if ( $cart_unique_cost == 0 && $unique_cost > 0 ) {
							$cart_unique_cost = $unique_cost;
						}

						$item_cost = $shipping_cost + ( $quantity * $product_cost );
						$html .= '<br /><div class="product-details">
								<small>
									<strong>' . wc_price( $item_cost ) . '</strong> -
									<strong>' . $item_name . '</strong><br />
									- ' . __( 'Cost per product', 'yith-product-shipping-for-woocommerce' ) . ': <strong>' . wc_price( $shipping_cost ) . '</strong><br />
									- ' . __( 'Cost per quantity', 'yith-product-shipping-for-woocommerce' ) . ': <strong>' . wc_price( $product_cost ) . '</strong> x ' . $quantity . '
								</small>
							</div>';
					}
				}

				if ( $cart_unique_cost > 0 ) {
					$html .= '<br /><div class="unique-cost"><small><strong>' . wc_price( $cart_unique_cost ) . '</strong> - <strong>' . __( 'Cost per order', 'yith-product-shipping-for-woocommerce' ) . '</strong></small></div>';
				}

				$html = '<div id="yith_wcps_info_cart" style="border: 1px dashed #ddd; padding: 10px; margin-top: 10px; line-height: 15px;">
							<div class="total-items">
								<small>' . __( 'Total cart items', 'yith-product-shipping-for-woocommerce' ) . ': <strong>' . WC()->cart->get_cart_contents_count() . '</strong></small>
							</div>
							' . $html . '
						</div>';

				echo apply_filters( 'yith_wcps_info_cart_html', $html );
			}
		}

		/**
		 * Single Product Message
		 *
		 * @since 1.0.0
		 */
		function single_product_message() {
			global $post;
			$message = get_post_meta( $post->ID, '_yith_product_shipping_message', true );
			if ( empty( $message ) || empty( get_post_meta( $post->ID, '_yith_product_shipping', true ) ) ) {
				$message = WC()->shipping->get_shipping_methods()['yith_wc_product_shipping_method']->settings['message'];
				$position = WC()->shipping->get_shipping_methods()['yith_wc_product_shipping_method']->settings['message_position'];
			} else {
				$position = get_post_meta( $post->ID, '_yith_product_shipping_message_position', true );
			}
			if ( $position == 'before' && current_action() == 'woocommerce_before_add_to_cart_button' && ! empty( $message ) ) {
				wc_print_notice( $message, 'notice' );
			} elseif ( $position == 'after' && current_action() == 'woocommerce_after_add_to_cart_button' && ! empty( $message ) ) {
				wc_print_notice( $message, 'notice' );
			}
		}

	}

}
