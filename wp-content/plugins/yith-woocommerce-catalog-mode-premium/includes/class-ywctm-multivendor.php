<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YWCTM_MultiVendor' ) ) {

	/**
	 * Implements compatibility with YITH WooCommerce Multi Vendor
	 *
	 * @class   YWCTM_MultiVendor
	 * @package Yithemes
	 * @since   1.3.0
	 * @author  Your Inspiration Themes
	 *
	 */
	class YWCTM_MultiVendor {

		/**
		 * Single instance of the class
		 *
		 * @var \YWCTM_MultiVendor
		 * @since 1.3.0
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YWCTM_MultiVendor
		 * @since 1.3.0
		 */
		public static function get_instance() {

			if ( is_null( self::$instance ) ) {

				self::$instance = new self();

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
		 * @var string Yith WooCommerce Catalog Mode vendor panel page
		 */
		protected $_panel_page = 'yith_vendor_ctm_settings';

		/**
		 * Panel object
		 *
		 * @var     /Yit_Plugin_Panel object
		 * @since   1.3.0
		 * @see     plugin-fw/lib/yit-plugin-panel.php
		 */
		protected $_vendor_panel = null;

		/**
		 * Constructor
		 *
		 * @since   1.3.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$this->vendor         = yith_get_vendor( 'current', 'user' );
			$this->active_vendors = YITH_Vendors()->get_vendors( array( 'enabled_selling' => true ) );

			if ( $this->vendor->is_valid() && $this->vendor->has_limited_access() && $this->check_ywctm_vendor_enabled() ) {

				add_action( 'admin_menu', array( $this, 'add_ywctm_vendor' ), 5 );

			}

			add_filter( 'woocommerce_screen_ids', array( $this, 'add_screen_ids' ) );
			add_filter( 'ywctm_get_vendor_option', array( $this, 'get_vendor_option' ), 10, 3 );
			add_filter( 'ywctm_get_vendor_postmeta', array( $this, 'get_vendor_postmeta' ), 10, 3 );
			add_filter( 'ywctm_get_vendor_termmeta', array( $this, 'get_vendor_termmeta' ), 10, 4 );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );

		}

		/**
		 * Get vendor options
		 *
		 * @since   1.3.0
		 *
		 * @param   $value
		 * @param   $post_id
		 * @param   $option
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function get_vendor_option( $value, $post_id, $option ) {

			$vendor = yith_get_vendor( $post_id, 'product' );

			if ( $vendor->is_valid() && ! $this->admin_override_check( $vendor ) ) {

				$opt_val = get_option( $option . '_' . $vendor->id );

				$value = ( $opt_val != '' ) ? $opt_val : $value;

			}

			return $value;

		}

		/**
		 * Get vendor postmeta
		 *
		 * @since   1.3.0
		 *
		 * @param   $value
		 * @param   $post_id
		 * @param   $option
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function get_vendor_postmeta( $value, $post_id, $option ) {

			$vendor = yith_get_vendor( $post_id, 'product' );

			if ( $vendor->is_valid() && ! $this->admin_override_check( $vendor ) ) {

				$product = wc_get_product( $post_id );
				$opt_val = $product->get_meta( $option . '_' . $vendor->id );

				$value = ( $opt_val != '' ) ? $opt_val : $value;

			}

			return $value;

		}

		/**
		 * Get vendor termmeta
		 *
		 * @since   1.3.0
		 *
		 * @param   $value
		 * @param   $post_id
		 * @param   $term_id
		 * @param   $option
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function get_vendor_termmeta( $value, $post_id, $term_id, $option ) {

			$vendor = yith_get_vendor( $post_id, 'product' );

			if ( $vendor->is_valid() && ! $this->admin_override_check( $vendor ) ) {

				$opt_val = get_term_meta( $term_id, $option . '_' . $vendor->id, true );
				$value   = ( $opt_val != '' ) ? $opt_val : $value;

			}

			return $value;

		}

		/**
		 * Check if vendors options can be loaded
		 *
		 * @since   1.3.0
		 *
		 * @param   $vendor
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function admin_override_check( $vendor ) {

			$result = false;

			if ( get_option( 'ywctm_admin_override' ) == 'yes' ) {

				$has_exclusion = get_term_meta( $vendor->id, '_ywctm_vendor_override_exclusion', true );
				$result        = ( get_option( 'ywctm_admin_override_exclusion' ) != 'yes' ? true : ( $has_exclusion != 'yes' ? true : false ) );

				if ( get_option( 'ywctm_admin_override_reverse' ) == 'yes' ) {

					$result = ! $result;

				}

			}

			return $result;

		}

		/**
		 * Add Catalog Mode panel for vendors
		 *
		 * @since   1.3.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function add_ywctm_vendor() {

			if ( ! empty( $this->_vendor_panel ) ) {
				return;
			}

			$tabs = array(
				'settings'         => __( 'Settings', 'yith-woocommerce-catalog-mode' ),
				'exclusions'       => __( 'Exclusion List', 'yith-woocommerce-catalog-mode' ),
				'custom-url'       => __( 'Custom Button Url List', 'yith-woocommerce-catalog-mode' ),
				'alternative-text' => __( 'Alternative Text List', 'yith-woocommerce-catalog-mode' ),
			);

			$args = array(
				'create_menu_page' => false,
				'parent_slug'      => '',
				'page_title'       => __( 'Catalog Mode', 'yith-woocommerce-catalog-mode' ),
				'menu_title'       => 'Catalog Mode',
				'capability'       => 'manage_vendor_store',
				'parent'           => '',
				'parent_page'      => '',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $tabs,
				'options-path'     => YWCTM_DIR . 'plugin-options/vendor',
				'icon_url'         => 'dashicons-admin-settings',
				'position'         => 99
			);

			$this->_vendor_panel = new YIT_Plugin_Panel_WooCommerce( $args );

		}

		/**
		 * Add custom post type screen to WooCommerce list
		 *
		 * @since   1.3.0
		 *
		 * @param   $screen_ids
		 *
		 * @return  array
		 * @author  Alberto Ruggiero
		 */
		public function add_screen_ids( $screen_ids ) {

			$screen_ids[] = 'toplevel_page_yith_vendor_ctm_settings';

			return $screen_ids;

		}

		/**
		 * Initializes CSS and javascript
		 *
		 * @since   1.3.0
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function admin_scripts() {

			if ( ! empty( $_GET['page'] ) && ( $_GET['page'] == $this->_panel_page ) ) {

				wp_register_style( 'yit-plugin-style', YIT_CORE_PLUGIN_URL . '/assets/css/yit-plugin-panel.css' );
				wp_enqueue_style( 'yit-plugin-style' );

			}

		}

		/**
		 * Check if Catalog Mode for vendors allowed
		 *
		 * @since   1.3.0
		 * @return  boolean
		 * @author  Alberto Ruggiero
		 */
		public function check_ywctm_vendor_enabled() {

			if ( get_option( 'yith_wpv_vendors_enable_catalog_mode' ) == 'yes' ) {
				return true;
			}

			return false;

		}

	}

	/**
	 * Unique access to instance of YWCTM_MultiVendor class
	 *
	 * @return \YWCTM_MultiVendor
	 */
	function YWCTM_MultiVendor() {

		return YWCTM_MultiVendor::get_instance();

	}

	YWCTM_MultiVendor();

}