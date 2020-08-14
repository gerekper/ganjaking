<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YWSN_MultiVendor' ) ) {

	/**
	 * Implements compatibility with YITH WooCommerce Multi Vendor
	 *
	 * @class   YWSN_MultiVendor
	 * @since   1.0.3
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWSN_MultiVendor {

		/**
		 * Single instance of the class
		 *
		 * @since 1.0.0
		 * @var YWSN_MultiVendor
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return YWSN_MultiVendor
		 * @since 1.0.0
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {

				self::$instance = new self;

			}

			return self::$instance;
		}

		/**
		 * @var YITH_Vendor current vendor
		 */
		protected $vendor;

		/**
		 * @var YITH_Vendor active vendors
		 */
		protected $active_vendors;

		/**
		 * @var string Yith WooCommerce SMS Notifications vendor panel page
		 */
		protected $_panel_page = 'yith_vendor_sn_settings';

		/**
		 * Panel object
		 *
		 * @since   1.0.0
		 * @var     /Yit_Plugin_Panel object
		 * @see     plugin-fw/lib/yit-plugin-panel.php
		 */
		protected $_vendor_panel = null;

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   1.0.3
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			$this->vendor         = yith_get_vendor( 'current', 'user' );
			$this->active_vendors = YITH_Vendors()->get_vendors( array( 'enabled_selling' => true ) );

			if ( $this->check_ywsn_vendor_enabled() ) {

				if ( $this->vendor->is_valid() && $this->vendor->has_limited_access() ) {

					add_action( 'admin_menu', array( $this, 'add_ywsn_vendor' ), 5 );
					add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
					add_filter( 'woocommerce_screen_ids', array( $this, 'add_screen_ids' ) );

				}

				add_filter( 'ywsn_active_sms', array( $this, 'get_vendor_active_sms' ), 10, 2 );
				add_filter( 'ywsn_active_sms_booking', array( $this, 'get_vendor_active_sms_booking' ), 10, 2 );
				add_filter( 'ywsn_admin_phone_numbers', array( $this, 'get_vendor_admin_numbers' ), 10, 2 );

			}

			add_action( 'yith_new_vendor_registration', array( $this, 'get_new_vendor_phone_number' ) );
			add_action( 'yith_wpv_after_save_taxonomy', array( $this, 'get_vendor_phone_number' ) );
			add_action( 'admin_init', array( $this, 'copy_numbers_from_existing_vendors' ) );

		}

		/**
		 * Add SMS Notifications panel for vendors
		 *
		 * @return  void
		 * @since   1.0.3
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_ywsn_vendor() {

			if ( ! empty( $this->_vendor_panel ) ) {
				return;
			}

			$vendor = yith_get_vendor( 'current', 'user' );

			$tabs = array(
				'vendor' => esc_html__( 'Settings', 'yith-woocommerce-sms-notifications' ),
			);

			$args = array(
				'create_menu_page' => false,
				'parent_slug'      => '',
				'page_title'       => _x( 'SMS Notifications', 'plugin name in admin page title', 'yith-woocommerce-sms-notifications' ),
				'menu_title'       => _x( 'SMS Notifications', 'plugin name in admin WP menu', 'yith-woocommerce-sms-notifications' ),
				'capability'       => 'manage_vendor_store',
				'parent'           => 'vendor_' . $vendor->id,
				'parent_page'      => '',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $tabs,
				'options-path'     => YWSN_DIR . 'plugin-options/vendor',
				'icon_url'         => 'dashicons-admin-settings',
				'position'         => 99,
				'class'            => yith_set_wrapper_class(),

			);

			$this->_vendor_panel = new YIT_Plugin_Panel_WooCommerce( $args );

		}

		/**
		 * Get SMS Notifications panel for vendors
		 *
		 * @return  object
		 * @since   1.0.3
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_vendor_panel() {
			return $this->_vendor_panel;
		}

		/**
		 * Add custom post type screen to WooCommerce list
		 *
		 * @param   $screen_ids array
		 *
		 * @return  array
		 * @since   1.0.3
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_screen_ids( $screen_ids ) {

			$screen_ids[] = $this->_panel_page;

			return $screen_ids;

		}

		/**
		 * Initializes CSS and javascript
		 *
		 * @return  void
		 * @since   1.0.3
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function admin_scripts() {

			if ( ! empty( $_GET['page'] ) && ( $_GET['page'] === $this->_panel_page ) ) {

				wp_register_style( 'yit-plugin-style', YIT_CORE_PLUGIN_URL . '/assets/css/yit-plugin-panel.css' );
				wp_enqueue_style( 'yit-plugin-style' );

			}

		}

		/**
		 * Check if SMS Notifications for vendors allowed
		 *
		 * @return  boolean
		 * @since   1.0.3
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function check_ywsn_vendor_enabled() {

			return ( get_option( 'yith_wpv_vendors_enable_sms' ) === 'yes' );

		}

		/**
		 * Get active SMS list for vendor
		 *
		 * @param   $value array
		 * @param   $order WC_Order
		 *
		 * @return  array
		 * @since   1.0.3
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_vendor_active_sms( $value, $order ) {

			$vendor = yith_get_vendor( get_post_field( 'post_author', $order->get_id() ), 'user' );

			if ( $vendor->is_valid() ) {

				$value = get_option( 'ywsn_sms_active_send_vendor_' . $vendor->id );
			}

			return $value;

		}

		/**
		 * Get active SMS list for vendor
		 *
		 * @param   $value array
		 * @param   $order WC_Order
		 *
		 * @return  array
		 * @since   1.4.0
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_vendor_active_sms_booking( $value, $order ) {

			$vendor = yith_get_vendor( get_post_field( 'post_author', $order->get_id() ), 'user' );

			if ( $vendor->is_valid() ) {

				$value = get_option( 'ywsn_sms_active_send_booking_vendor_' . $vendor->id );
			}

			return $value;

		}

		/**
		 * Check active SMS list for vendor
		 *
		 * @param   $value array
		 * @param   $order WC_Order
		 *
		 * @return  array
		 * @since   1.0.3
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_vendor_admin_numbers( $value, $order ) {

			$vendor = yith_get_vendor( get_post_field( 'post_author', $order->get_id() ), 'user' );

			if ( $vendor->is_valid() ) {

				$value = explode( ',', trim( get_option( 'ywsn_admin_phone_vendor_' . $vendor->id ) ) );
			}

			return $value;

		}

		/**
		 * Copy new vendor phone number in relative option array
		 *
		 * @param   $vendor YITH_Vendor
		 *
		 * @return  void
		 * @since   1.2.8
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_new_vendor_phone_number( $vendor ) {

			$number = array( $vendor->telephone );
			update_option( 'ywsn_admin_phone_vendor_' . $vendor->id, implode( ',', $number ) );

		}

		/**
		 * Copy vendor phone number in relative option array
		 *
		 * @param   $vendor YITH_Vendor
		 *
		 * @return  void
		 * @since   1.2.8
		 *
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_vendor_phone_number( $vendor ) {

			if ( ! empty( $vendor->telephone ) ) {

				$numbers = get_option( 'ywsn_admin_phone_vendor_' . $vendor->id );

				if ( '' === $numbers ) {
					$number = array( $vendor->telephone );
					update_option( 'ywsn_admin_phone_vendor_' . $vendor->id, implode( ',', $number ) );
				}
			}

		}

		/**
		 * Copy vendor phone numbers in relative option array
		 *
		 * @return  void
		 * @since   1.2.8
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function copy_numbers_from_existing_vendors() {
			$db_option = get_option( 'ywsn_db_version', '1.0.0' );
			if ( $db_option && version_compare( $db_option, '1.2.8', '<' ) ) {
				if ( function_exists( 'YITH_Vendors' ) ) {
					$vendors = YITH_Vendors()->get_vendors();

					foreach ( $vendors as $vendor ) {

						$numbers = get_option( 'ywsn_admin_phone_vendor_' . $vendor->id );

						if ( '' === $numbers ) {
							$number = array( $vendor->telephone );
							update_option( 'ywsn_admin_phone_vendor_' . $vendor->id, implode( ',', $number ) );
						}
					}
				}

				update_option( 'ywsn_db_version', '1.2.8' );
			}
		}

	}

	/**
	 * Unique access to instance of YWSN_MultiVendor class
	 *
	 * @return YWSN_MultiVendor
	 */
	function YWSN_MultiVendor() { //phpcs:ignore
		return YWSN_MultiVendor::get_instance();
	}

	YWSN_MultiVendor();

}
