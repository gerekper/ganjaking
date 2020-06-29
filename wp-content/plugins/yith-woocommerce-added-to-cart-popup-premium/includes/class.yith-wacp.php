<?php
/**
 * Main class
 *
 * @author YITH
 * @package YITH WooCommerce Added to Cart Popup
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WACP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WACP' ) ) {
	/**
	 * YITH WooCommerce Added to Cart Popup
	 *
	 * @since 1.0.0
	 */
	class YITH_WACP {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WACP
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $version = YITH_WACP_VERSION;


		/**
		 * Returns single instance of the class
		 *
		 * @return YITH_WACP
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function __construct() {

			// Load Plugin Framework.
			add_action( 'after_setup_theme', array( $this, 'plugin_fw_loader' ), 1 );

			// Class admin.
			if ( $this->is_admin() ) {

				// Require admin class.
				require_once 'class.yith-wacp-admin.php';
				require_once 'class.yith-wacp-admin-premium.php';

				// Require admin class tables.
				require_once 'class.yith-wacp-exclusions-handler.php';
				require_once 'admin-tables/class.yith-wacp-exclusions-prod-table.php';
				require_once 'admin-tables/class.yith-wacp-exclusions-cat-table.php';

				YITH_WACP_Admin_Premium();
				YITH_WACP_Exclusions_Handler();
			} elseif ( $this->load_frontend() ) {

				// Require frontend class.
				$this->is_mini_cart_active() && require_once 'class.yith-wacp-mini-cart.php';
				require_once 'class.yith-wacp-frontend.php';
				require_once 'class.yith-wacp-frontend-premium.php';

				YITH_WACP_Frontend_Premium();
			}

			$this->load_integrations();

			// Register image size.
			add_action( 'init', array( $this, 'register_size' ) );

			add_action( 'init', array( $this, 'update_old_options' ), 1 );
		}

		/**
		 * Load Plugin Framework
		 *
		 * @return void
		 * @since  1.0
		 * @access public
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once $plugin_fw_file;
				}
			}
		}

		/**
		 * Check if is admin
		 *
		 * @return boolean
		 * @author Francesco Licandro
		 * @since 1.1.0
		 * @access public
		 */
		public function is_admin() {
			$context_check = isset( $_REQUEST['context'] ) && 'frontend' === $_REQUEST['context']; // phpcs:ignore
			$is_admin      = is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX && $context_check );
			return apply_filters( 'yith_wacp_check_is_admin', $is_admin );
		}

		/**
		 * Check if load or not frontend class
		 *
		 * @return boolean
		 * @author Francesco Licandro
		 * @since 1.2.0
		 */
		public function load_frontend() {
			$is_one_click = isset( $_REQUEST['_yith_wocc_one_click'] ) && 'is_one_click' === $_REQUEST['_yith_wocc_one_click']; // phpcs:ignore
			$load         = ( ! wp_is_mobile() || get_option( 'yith-wacp-enable-mobile' ) !== 'no' ) && ! $is_one_click;
			return apply_filters( 'yith_wacp_check_load_frontend', $load );
		}

		/**
		 * Check if mini cart feature is active
		 *
		 * @return boolean
		 * @author Francesco Licandro
		 * @since 1.4.0
		 */
		public function is_mini_cart_active() {
			$is_mobile = wp_is_mobile();
			return ( ! $is_mobile && get_option( 'yith-wacp-mini-cart-enable', 'yes' ) === 'yes' ) || ( $is_mobile && get_option( 'yith-wacp-mini-cart-enable-mobile', 'yes' ) === 'yes' );
		}

		/**
		 * Register size
		 *
		 * @access public
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function register_size() {
			// Set image size.
			$size   = get_option( 'yith-wacp-image-size' );
			$width  = isset( $size['width'] ) ? $size['width'] : 0;
			$height = isset( $size['height'] ) ? $size['height'] : 0;
			$crop   = isset( $size['crop'] ) ? $size['crop'] : false;

			add_image_size( 'yith_wacp_image_size', $width, $height, $crop );
		}

		/**
		 * Load class integrations if needed
		 *
		 * @since 1.3.0
		 * @author Francesco Licandro
		 * @access public
		 */
		public function load_integrations() {

			$classes = array();

			// YITH WooCommerce Cart Messages Premium integration class.
			if ( defined( 'YITH_YWCM_PREMIUM' ) && YITH_YWCM_PREMIUM ) {
				$classes[] = 'class.yith-wacp-ywcm-integration.php';
			}

			// YITH WooCommerce Request A Quote integration class.
			if ( defined( 'YITH_YWRAQ_INIT' ) && YITH_YWRAQ_INIT && ! $this->is_admin() && get_option( 'yith-wacp-enable-raq', 'no' ) === 'yes' ) {
				$classes[] = 'class.yith-wacp-ywraq-integration.php';
			}

			foreach ( $classes as $class ) {
				require_once 'integrations/' . $class;
			}
		}

		/**
		 * Update old option for new panel in version 1.5
		 *
		 * @since  1.5.0
		 * @author Francesco Licandro
		 * @return void
		 */
		public function update_old_options() {

			$last_options_updated = get_option( 'yith-wacp-option-version', '1.0.0' );
			if ( version_compare( $last_options_updated, '1.5', '<' ) ) {

				$close_color       = get_option( 'yith-wacp-close-color' );
				$close_color_hover = get_option( 'yith-wacp-close-color-hover' );

				if ( $close_color_hover ) {
					update_option(
						'yith-wacp-close-color',
						array(
							'normal' => $close_color,
							'hover'  => $close_color_hover,
						)
					);

					delete_option( 'yith-wacp-close-color-hover' );
				}

				$product_color       = get_option( 'yith-wacp-product-name-color' );
				$product_color_hover = get_option( 'yith-wacp-product-name-color-hover' );

				if ( $product_color_hover ) {
					update_option(
						'yith-wacp-product-name-color',
						array(
							'normal' => $product_color,
							'hover'  => $product_color_hover,
						)
					);

					delete_option( 'yith-wacp-product-name-color-hover' );
				}

				$button_color       = get_option( 'yith-wacp-button-background' );
				$button_color_hover = get_option( 'yith-wacp-button-background-hover' );

				if ( $button_color_hover ) {
					update_option(
						'yith-wacp-button-background',
						array(
							'normal' => $button_color,
							'hover'  => $button_color_hover,
						)
					);

					delete_option( 'yith-wacp-button-background-hover' );
				}

				$button_text_color       = get_option( 'yith-wacp-button-text' );
				$button_text_color_hover = get_option( 'yith-wacp-button-text-hover' );

				if ( $button_text_color_hover ) {
					update_option(
						'yith-wacp-button-text',
						array(
							'normal' => $button_text_color,
							'hover'  => $button_text_color_hover,
						)
					);

					delete_option( 'yith-wacp-button-text-hover' );
				}

				update_option( 'yith-wacp-option-version', '1.5' );
			}
		}
	}
}

/**
 * Unique access to instance of YITH_WACP class
 *
 * @return YITH_WACP
 * @since 1.0.0
 */
function YITH_WACP() { // phpcs:ignore
	return YITH_WACP::get_instance();
}
