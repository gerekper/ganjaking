<?php
/**
 * Multi Vendor compatibility class
 *
 * @author  YITH
 * @package YITH WooCommerce Waiting List
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCWTL' ) ) {
	exit; // Exit if accessed directly
}

/**
 * YITH_WCWTL_Multivendor class to add compatibility with YITH WooCommerce Multivendor
 *
 * @class   YITH_WCWTL_Multivendor
 * @since   1.0.0
 * @author  Yithemes
 * @package YITH WooCommerce Subscription
 */
if ( ! class_exists( 'YITH_WCWTL_Multivendor' ) ) {

	class YITH_WCWTL_Multivendor {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCWTL_Multivendor
		 */
		protected static $instance;

		/**
		 * Current vendor
		 *
		 * @var \YITH_WCWTL_Multivendor
		 */
		protected $vendor;

		/**
		 * Returns single instance of the class
		 *
		 * @since 1.0.0
		 * @return \YITH_WCWTL_Multivendor
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
			add_action( 'admin_menu', array( $this, 'vendor_admin_init' ), 4 );
		}

		public function vendor_admin_init() {
			$this->vendor = yith_get_vendor( 'current', 'user' );

			if ( $this->vendor->is_valid() && $this->vendor->has_limited_access() ) {
				add_filter( 'yith-wcwtl-register-panel-create-menu-page', '__return_true' );
				add_filter( 'yith-wcwtl-register-panel-parent-page', array( $this, 'admin_vendor_parent_page' ) );
				add_filter( 'yith-wcwtl-register-panel-capabilities', array( $this, 'admin_vendor_register_panel_capabilities' ) );
				add_filter( 'yith-wcwtl-admin-tabs', array( $this, 'admin_vendor_register_panel_tabs' ), 99, 1 );
				// add screen id for load wc scripts
				add_filter( 'woocommerce_screen_ids', array( $this, 'add_plugin_screen_ids' ), 99, 1 );
				// filter waitlist data table
				add_filter( 'yith-wcwtl-waitlistdata-where', array( $this, 'show_only_vendor_products' ), 10, 1 );
				add_filter( 'yith-wcwtl-exclusionstable-where', array( $this, 'show_only_vendor_products' ), 10, 1 );

				add_action( 'admin_init', array( $this, 'redirect_exclusion_tab' ), 99 );
			}
		}

		/**
		 * Redirect to exclusion tab
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 */
		public function redirect_exclusion_tab() {
			if ( isset( $_GET['page'] ) && $_GET['page'] == 'yith_wcwtl_panel' && ! isset( $_GET['tab'] ) ) {
				wp_safe_redirect( add_query_arg( 'tab', 'exclusions' ) );
				exit;
			}
		}

		/**
		 * Permit vendor to see the subscription menu in administration panel
		 *
		 * @access public
		 *
		 * @since  1.0.0
		 * @param string $parent_page
		 *
		 * @return string
		 */
		public function admin_vendor_parent_page( $parent_page ) {
			return '';
		}

		/**
		 * Permit vendor to see the subscription and activities panel
		 *
		 * @access public
		 *
		 * @since  1.0.0
		 * @param array $capabilities
		 *
		 * @return array
		 */
		public function admin_vendor_register_panel_capabilities( $capabilities ) {

			$capabilities = 'manage_vendor_store';
			return $capabilities;
		}

		/**
		 * Permit vendor to see the subscription and activities panel
		 *
		 * @access public
		 *
		 * @since  1.0.0
		 * @param array $tabs
		 *
		 * @return array
		 */
		public function admin_vendor_register_panel_tabs( $tabs ) {

			$vendor_tabs = array(
				'exclusions'   => __( 'Exclusions List', 'yith-woocommerce-waiting-list' ),
				'waitlistdata' => __( 'Waiting list Checklist', 'yith-woocommerce-waiting-list' ),
			);

			return $vendor_tabs;
		}

		/**
		 * Filter where in query for waitlist data table.
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @param string $where
		 * @return string
		 */
		public function show_only_vendor_products( $where ) {

			$products = $this->vendor->get_products();

			if ( empty( $products ) ) {
				$where .= ' AND 1=-1';
			} else {

				$post_in = array();

				foreach ( $products as $key => $product_id ) {
					// first get product
					$product = wc_get_product( $product_id );
					if ( $product->is_type( 'variable' ) ) {
						$children = $product->get_children();

						foreach ( $children as $ckey => $child_id ) {
							$post_in[] = $child_id;
						}
					} else {
						$post_in[] = $product->get_id();
					}
				}

				$post_in = implode( ',', $post_in );
				$where   .= " AND pm.post_id IN ( $post_in )";
			}

			return $where;
		}


		/**
		 * Add plugin screen id to main WC array
		 */
		public function add_plugin_screen_ids( $ids ) {
			$ids[] = 'toplevel_page_' . YITH_WCWTL_Admin_Premium()->get_panel_page_name();

			return $ids;
		}
	}

}

/**
 * Unique access to instance of YITH_WCWTL_Multivendor class
 *
 * @return \YITH_WCWTL_Multivendor
 */
function YITH_WCWTL_Multivendor() {
	return YITH_WCWTL_Multivendor::get_instance();
}
