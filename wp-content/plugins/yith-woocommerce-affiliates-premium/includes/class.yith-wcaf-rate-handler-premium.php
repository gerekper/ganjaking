<?php
/**
 * Rate Handler Premium class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAF_Rate_Handler_Premium' ) ) {
	/**
	 * WooCommerce Rate Handler Premium
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Rate_Handler_Premium extends YITH_WCAF_Rate_Handler {
		/**
		 * Single instance of the class for each token
		 *
		 * @var \YITH_WCAF_Rate_Handler_Premium
		 * @since 1.0.0
		 */
		protected static $instance = null;

		/**
		 * Constructor method
		 *
		 * @return \YITH_WCAF_Rate_Handler_Premium
		 * @since 1.0.0
		 */
		public function __construct() {
			// add rates panel handling
			add_action( 'yith_wcaf_rate_panel', array( $this, 'print_rate_panel' ) );

			// register admin rates action
			add_action( 'admin_init', array( $this, 'save_general_rate' ) );
			add_action( 'admin_init', array( $this, 'add_affiliate_rate' ) );
			add_action( 'wp_ajax_yith_wcaf_update_affiliate_commission', array( $this, 'update_affiliate_rate' ) );
			add_action( 'wp_ajax_yith_wcaf_delete_affiliate_commission', array( $this, 'delete_affiliate_rate' ) );
			add_action( 'admin_init', array( $this, 'add_product_rate' ) );
			add_action( 'wp_ajax_yith_wcaf_update_product_commission', array( $this, 'update_product_rate' ) );
			add_action( 'wp_ajax_yith_wcaf_delete_product_commission', array( $this, 'delete_product_rate' ) );
		}

		/* === RATE HANDLING METHODS === */

		/**
		 * Save general rate
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function save_general_rate() {
			// save general rate
			if ( ! isset( $_POST['yith_wcaf_general_rate'] ) ) {
				return;
			}

			$rate = floatval( $_POST['yith_wcaf_general_rate'] );

			update_option( 'yith_wcaf_general_rate', $rate );
		}

		/**
		 * Add an affiliate rate
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function add_affiliate_rate() {
			if ( ! isset( $_POST['yith_new_affiliate_rate'] ) ) {
				return;
			}

			$affiliate = isset( $_POST['yith_new_affiliate_rate']['affiliate'] ) ? intval( $_POST['yith_new_affiliate_rate']['affiliate'] ) : 0;
			$rate      = isset( $_POST['yith_new_affiliate_rate']['rate'] ) ? floatval( $_POST['yith_new_affiliate_rate']['rate'] ) : 0;

			if ( empty( $affiliate ) ) {
				return;
			}

			YITH_WCAF_Affiliate_Handler()->update_affiliate_rate( $affiliate, $rate );
		}

		/**
		 * Update affiliate rate
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function update_affiliate_rate() {
			$affiliate = isset( $_POST['affiliate_id'] ) ? intval( $_POST['affiliate_id'] ) : 0;
			$rate      = isset( $_POST['rate'] ) ? floatval( $_POST['rate'] ) : 0;

			if ( empty( $affiliate ) ) {
				wp_send_json( false );
			}

			YITH_WCAF_Affiliate_Handler()->update_affiliate_rate( $affiliate, $rate );
		}

		/**
		 * Delete affiliate rate
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function delete_affiliate_rate() {
			$affiliate = isset( $_POST['affiliate_id'] ) ? intval( $_POST['affiliate_id'] ) : 0;

			if ( empty( $affiliate ) ) {
				wp_send_json( false );
			}

			YITH_WCAF_Affiliate_Handler()->update_affiliate_rate( $affiliate );
		}

		/**
		 * Add a product rate
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function add_product_rate() {
			if ( ! isset( $_POST['yith_new_product_rate'] ) ) {
				return;
			}

			$product = isset( $_POST['yith_new_product_rate']['product'] ) ? intval( $_POST['yith_new_product_rate']['product'] ) : 0;
			$rate    = isset( $_POST['yith_new_product_rate']['rate'] ) ? floatval( $_POST['yith_new_product_rate']['rate'] ) : 0;

			if ( empty( $product ) ) {
				return;
			}

			$registered_rates             = get_option( 'yith_wcaf_product_rates', array() );
			$registered_rates[ $product ] = $rate;

			update_option( 'yith_wcaf_product_rates', $registered_rates );
		}

		/**
		 * Update a product rate
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function update_product_rate() {
			$product = isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : 0;
			$rate    = isset( $_POST['rate'] ) ? floatval( $_POST['rate'] ) : 0;

			if ( empty( $product ) ) {
				wp_send_json( false );
			}

			$registered_rates             = get_option( 'yith_wcaf_product_rates', array() );
			$registered_rates[ $product ] = $rate;

			update_option( 'yith_wcaf_product_rates', $registered_rates );
			wp_send_json( $registered_rates );
		}

		/**
		 * Delete a product rate
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function delete_product_rate() {
			$product = isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : 0;

			if ( empty( $product ) ) {
				wp_send_json( false );
			}

			$registered_rates = get_option( 'yith_wcaf_product_rates', array() );
			unset( $registered_rates[ $product ] );

			update_option( 'yith_wcaf_product_rates', $registered_rates );
			wp_send_json( $registered_rates );
		}

		/* === PANEL RATE METHODS === */

		/**
		 * Print rate panel
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function print_rate_panel() {
			// define variables to use in template
			$general_rate        = get_option( 'yith_wcaf_general_rate', 0 );
			$persistent_rate     = get_option( 'yith_wcaf_persistent_rate', 0 );
			$user_rates_table    = new YITH_WCAF_User_Rates_Table();
			$product_rates_table = new YITH_WCAF_Product_Rates_Table();

			// prepare rates table items
			$user_rates_table->prepare_items();
			$product_rates_table->prepare_items();

			// require rate panel template
			include( YITH_WCAF_DIR . 'templates/admin/rate-panel.php' );
		}

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCAF_Rate_Handler_Premium
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}
	}
}

/**
 * Unique access to instance of YITH_WCAF_Rate_Handler_Premium class
 *
 * @return \YITH_WCAF_Rate_Handler_Premium
 * @since 1.0.0
 */
function YITH_WCAF_Rate_Handler_Premium() {
	return YITH_WCAF_Rate_Handler_Premium::get_instance();
}