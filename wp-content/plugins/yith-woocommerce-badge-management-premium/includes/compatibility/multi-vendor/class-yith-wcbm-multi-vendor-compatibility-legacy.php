<?php
/**
 * Multi Vendor Compatibility Class - Legacy
 *
 * @auhtor  Arcifa Giuseppe <giuseppe.arcifa@yithemes.com>
 * @package YITH\BadgeManagementPremium\Compatibility
 * @since   2.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'YITH_WCBM_Multi_Vendor_Compatibility' ) ) {
	/**
	 * Multi Vendor Compatibility Class
	 */
	class YITH_WCBM_Multi_Vendor_Compatibility_Legacy {

		/**
		 * Single instance of the class
		 *
		 * @var YITH_WCBM_Multi_Vendor_Compatibility_Legacy
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * The vendor panel page.
		 */
		const PANEL_PAGE = 'yith_wcbm_vendor_panel';

		/**
		 * Return the class instance
		 *
		 * @return YITH_WCBM_Multi_Vendor_Compatibility|YITH_WCBM_Multi_Vendor_Compatibility_Legacy
		 */
		public static function get_instance() {
			$self = defined( 'YITH_WPV_VERSION' ) && version_compare( YITH_WPV_VERSION, '4.0.0', '>=' ) && class_exists( 'YITH_WCBM_Multi_Vendor_Compatibility' ) ? 'YITH_WCBM_Multi_Vendor_Compatibility' : 'YITH_WCBM_Multi_Vendor_Compatibility_Legacy';

			return ! is_null( self::$instance ) ? self::$instance : self::$instance = new $self();
		}

		/**
		 * YITH_WCBM_Multi_Vendor_Compatibility constructor.
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'register_vendor_panel' ), 5 );
			add_filter( 'yith_wpv_vendor_menu_items', array( $this, 'add_allowed_menu_items' ), 10, 1 );
		}

		/**
		 * Add a panel under YITH Plugins tab
		 *
		 * @since    1.0
		 * @use      YIT_Plugin_Panel class
		 * @see      plugin-fw/lib/yit-plugin-panel.php
		 */
		public function register_vendor_panel() {
			$vendor = $this->get_current_vendor();
			if ( $vendor ) {
				if ( ! empty( $this->panel ) ) {
					return;
				}

				$tabs = array(
					'badges' => esc_html__( 'Badges', 'yith-woocommerce-badges-management' ),
				);

				$args = array(
					'create_menu_page' => true,
					'parent_slug'      => '',
					'class'            => yith_set_wrapper_class(),
					'page_title'       => 'Badge Management for WooCommerce',
					'menu_title'       => 'Badge',
					'capability'       => 'yith_vendor',
					'parent'           => '',
					'parent_page'      => '',
					'page'             => self::PANEL_PAGE,
					'admin-tabs'       => $tabs,
					'icon_url'         => 'dashicons-visibility',
					'position'         => 30,
					'options-path'     => YITH_WCBM_COMPATIBILITY_PATH . 'multi-vendor/panel',
				);

				$this->panel = new YIT_Plugin_Panel_WooCommerce( $args );
			}
		}


		/**
		 * Retrieve the current Vendor.
		 *
		 * @return YITH_Vendor|false
		 * @since 2.1.28
		 */
		public function get_current_vendor() {
			$current_vendor = false;
			if ( function_exists( 'yith_get_vendor' ) ) {
				$vendor = yith_get_vendor( 'current', 'user' );
				if ( $vendor->is_valid() && $vendor->has_limited_access() ) {
					$current_vendor = $vendor;
				}
			}

			return $current_vendor;
		}

		/**
		 * Add allowed menu items for vendor.
		 *
		 * @param array $items Allowed menu items.
		 *
		 * @return array
		 * @since 3.6.0
		 */
		public function add_allowed_menu_items( $items ) {
			$items[] = self::PANEL_PAGE;

			return $items;
		}
	}
}

if ( ! function_exists( 'yith_wcbm_multi_vendor_compatibility' ) ) {
	/**
	 * Get the class instance
	 *
	 * @return YITH_WCBM_Multi_Vendor_Compatibility|YITH_WCBM_Multi_Vendor_Compatibility_Legacy
	 */
	function yith_wcbm_multi_vendor_compatibility() {
		return YITH_WCBM_Multi_Vendor_Compatibility_Legacy::get_instance();
	}
}
