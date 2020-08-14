<?php

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_WFBT' ) ) {
	exit; // Exit if accessed directly
}
/**
 * YITH_WFBT_Multivendor class to add compatibility with YITH WooCommerce Multivendor
 *
 * @class   YITH_WCWTL_Multivendor
 * @package YITH WooCommerce Frequently Bought Together
 * @since   1.0.0
 * @author  Yithemes
 */
if ( ! class_exists( 'YITH_WFBT_Multivendor' ) ) {

	class YITH_WFBT_Multivendor {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WFBT_Multivendor
		 */
		protected static $instance;

		/**
		 * Current vendor
		 *
		 * @var \YITH_WFBT_Multivendor
		 */
		protected $vendor;

		/**
		 * Current vendor products
		 *
		 * @var array
		 */
		protected $vendor_products = array();

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WFBT_Multivendor
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
		 * Initialize class and registers actions and filters to be used
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		public function __construct() {
			add_action( 'init', array( $this,'vendor_admin_init' ), 15 );
		}

		public function vendor_admin_init( ) {
			$this->vendor = yith_get_vendor( 'current', 'user' );

			if ( $this->vendor->is_valid() && $this->vendor->has_limited_access() ) {
				// init products array
				$this->vendor_products = $this->vendor->get_products();
				
				add_filter( 'yith-wfbt-register-panel-create-menu-page', '__return_true' );
				add_filter( 'yith-wfbt-register-panel-parent-page', array( $this, 'admin_vendor_parent_page' ) );
				add_filter( 'yith-wfbt-register-panel-capabilities', array( $this, 'admin_vendor_register_panel_capabilities' ) );
				add_filter( 'yith-wfbt-admin-tabs', array( $this, 'admin_vendor_register_panel_tabs' ), 99, 1 );
				// add screen id for load wc scripts
				add_filter( 'woocommerce_screen_ids', array( $this, 'add_plugin_screen_ids' ), 99, 1 );
				add_filter( 'yith_wfbt_linked_products_where', array( $this, 'show_only_vendor_products' ), 10, 1 );
				add_action( 'admin_init', array( $this, 'redirect_data_tab' ), 99 );
			}
		}

		/**
		 * Redirect to exclusion tab
		 *
		 * @since 1.0.0
		 * @author Francesco Licandro
		 */
		public function redirect_data_tab(){
		    $page = YITH_WFBT_Admin()->get_panel_page_name();
			if( isset( $_GET['page'] ) && $_GET['page'] == $page && ! isset( $_GET['tab'] ) ) {
				wp_safe_redirect( add_query_arg( 'tab', 'data' ) );
				exit;
			}
		}

		/**
		 * Permit vendor to see the subscription menu in administration panel
		 *
		 * @access public
		 *
		 * @param string $parent_page
		 *
		 * @return string
		 * @since  1.0.0
		 */
		public function admin_vendor_parent_page( $parent_page ) {
			return '';
		}

		/**
		 * Permit vendor to see the subscription and activities panel
		 *
		 * @access public
		 *
		 * @param string $capabilities
		 * @return string
		 * @since  1.0.0
		 */
		public function admin_vendor_register_panel_capabilities( $capabilities ) {
			return 'manage_vendor_store';
		}

		/**
		 * Permit vendor to see the plugin panel tabs
		 *
		 * @access public
		 * @param array $tabs
		 * @return array
		 * @since  1.0.0
		 */
		public function admin_vendor_register_panel_tabs( $tabs ) {

			$vendor_tabs = array(
                'data'    => __( 'Linked Products', 'yith-woocommerce-frequently-bought-together' )
			);

			return $vendor_tabs;
		}

		/**
		 * Check if a product belongs to current vendor
		 *
		 * @access public
		 * @since 1.0.0
		 * @param string $product_id
		 * @return boolean
		 * @author Francesco Licandro
		 */
        public function is_vendor_product( $product_id ){
			return empty( $this->vendor_products ) || in_array( $product_id, $this->vendor_products );
        }

        /**
		 * Add plugin screen id to main WC array
		 */
		public function add_plugin_screen_ids( $ids ) {
			$ids[] = 'toplevel_page_' . YITH_WFBT_Admin()->get_panel_page_name();

			return $ids;
		}

		/**
		 * Filter where in query for waitlist data table.
		 *
		 * @access public
		 * @since 1.0.0
		 * @param string $where
		 * @return string
		 * @author Francesco Licandro
		 */
		public function show_only_vendor_products( $where ){

			if( empty( $this->vendor_products ) ){
				$where .= ' AND 1=2';
			}
			else {

				$post_in = implode(',', $this->vendor_products );
				$where .= " AND pm.post_id IN ( $post_in )";
			}

			return $where;
		}
}

}

/**
 * Unique access to instance of YITH_WFBT_Multivendor class
 *
 * @return \YITH_WFBT_Multivendor
 */
function YITH_WFBT_Multivendor() {
	return YITH_WFBT_Multivendor::get_instance();
}
