<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YWCTM_Multi_Vendor' ) ) {

	/**
	 * Implements compatibility with YITH WooCommerce Multi Vendor
	 *
	 * @class   YWCTM_Multi_Vendor
	 * @since   2.0.0
	 * @author  Your Inspiration Themes
	 *
	 * @package Yithemes
	 */
	class YWCTM_Multi_Vendor {

		/**
		 * @var string Yith WooCommerce Catalog Mode vendor panel page
		 */
		protected $_panel_page = 'yith_vendor_ctm_settings';

		/**
		 * Panel object
		 *
		 * @since   2.0.0
		 * @var     /Yit_Plugin_Panel object
		 * @see     plugin-fw/lib/yit-plugin-panel.php
		 */
		protected $_vendor_panel = null;

		/**
		 * Constructor
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function __construct() {

			$vendor = yith_get_vendor( 'current', 'user' );

			if ( $vendor->is_valid() && $vendor->has_limited_access() && $this->check_ywctm_vendor_enabled() ) {
				add_action( 'admin_menu', array( $this, 'add_ywctm_vendor' ), 5 );
			}

			add_action( 'woocommerce_screen_ids', array( $this, 'add_screen_ids' ) );
			add_action( 'yith_plugin_fw_wc_panel_screen_ids_for_assets', array( $this, 'add_screen_ids' ) );
			add_filter( 'ywctm_get_vendor_option', array( $this, 'get_vendor_option' ), 10, 3 );
			add_filter( 'ywctm_get_vendor_postmeta', array( $this, 'get_vendor_postmeta' ), 10, 3 );
			add_filter( 'ywctm_get_vendor_termmeta', array( $this, 'get_vendor_termmeta' ), 10, 4 );

		}

		/**
		 * Check if Catalog Mode for vendors allowed
		 *
		 * @return  boolean
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function check_ywctm_vendor_enabled() {
			return 'yes' === get_option( 'yith_wpv_vendors_enable_catalog_mode', 'no' );
		}

		/**
		 * Add custom post type screen to YITH Plugin list
		 *
		 * @param   $screen_ids array
		 *
		 * @return  array
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_screen_ids( $screen_ids ) {

			$screen_ids[] = 'toplevel_page_yith_vendor_ctm_settings';

			return $screen_ids;

		}

		/**
		 * Add Catalog Mode panel for vendors
		 *
		 * @return  void
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function add_ywctm_vendor() {

			if ( ! empty( $this->_vendor_panel ) ) {
				return;
			}

			$tabs = array(
				'premium-settings' => esc_html_x( 'Settings', 'general settings tab name', 'yith-woocommerce-catalog-mode' ),
				'exclusions'       => esc_html_x( 'Exclusion List', 'exclusion settings tab name', 'yith-woocommerce-catalog-mode' ),
				'inquiry-form'     => esc_html_x( 'Inquiry Form', 'inquiry form settings tab name', 'yith-woocommerce-catalog-mode' ),
				'buttons-labels'   => esc_html_x( 'Buttons & Labels', 'buttons & labels settings tab name', 'yith-woocommerce-catalog-mode' ),
			);

			$args = array(
				'create_menu_page' => false,
				'parent_slug'      => '',
				'page_title'       => 'Catalog Mode',
				'menu_title'       => 'Catalog Mode',
				'capability'       => 'manage_vendor_store',
				'parent'           => '',
				'parent_page'      => '',
				'page'             => $this->_panel_page,
				'admin-tabs'       => $tabs,
				'options-path'     => YWCTM_DIR . 'plugin-options/',
				'icon_url'         => 'dashicons-admin-settings',
				'position'         => 99,
				'class'            => yith_set_wrapper_class(),
			);

			$this->_vendor_panel = new YIT_Plugin_Panel_WooCommerce( $args );

		}

		/**
		 * Check if vendors options can be loaded
		 *
		 * @param   $vendor YITH_Vendor
		 *
		 * @return  boolean
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function admin_override_check( $vendor ) {

			if ( 'yes' === get_option( 'ywctm_admin_override', 'no' ) ) {

				$admin_override = get_option( 'ywctm_admin_override_settings' );
				$behavior       = $admin_override['action'];
				$target         = $admin_override['target'];

				if ( 'disable' === $behavior && 'all' === $target ) {
					return true;
				} elseif ( 'enable' === $behavior && 'all' === $target ) {
					return false;
				} else {

					$has_exclusion = 'yes' === get_term_meta( $vendor->id, '_ywctm_vendor_override_exclusion', true );

					if ( ( 'disable' === $behavior && $has_exclusion ) || ( 'enable' === $behavior && ! $has_exclusion ) ) {
						return true;
					} elseif ( ( 'enable' === $behavior && $has_exclusion ) || ( 'disable' === $behavior && ! $has_exclusion ) ) {
						return false;
					}
				}
			}

			return true;

		}

		/**
		 * Get vendor options
		 *
		 * @param   $value   mixed
		 * @param   $post_id integer
		 * @param   $option  string
		 *
		 * @return  mixed
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_vendor_option( $value, $post_id, $option ) {

			$vendor = yith_get_vendor( $post_id, 'product' );

			if ( $vendor->is_valid() && $this->admin_override_check( $vendor ) ) {
				$opt_val = get_option( $option . '_' . $vendor->id );
				$value   = ( '' !== $opt_val ) ? $opt_val : $value;
			}

			return $value;

		}

		/**
		 * Get vendor postmeta
		 *
		 * @param   $value   mixed
		 * @param   $post_id integer
		 * @param   $option  string
		 *
		 * @return  mixed
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_vendor_postmeta( $value, $post_id, $option ) {

			$vendor = yith_get_vendor( $post_id, 'product' );

			if ( $vendor->is_valid() && $this->admin_override_check( $vendor ) ) {
				$product = wc_get_product( $post_id );
				$opt_val = $product->get_meta( $option . '_' . $vendor->id );
				$value   = ( '' !== $opt_val ) ? $opt_val : $value;
			}

			return $value;

		}

		/**
		 * Get vendor termmeta
		 *
		 * @param   $value   mixed
		 * @param   $post_id integer
		 * @param   $term_id integer
		 * @param   $option  string
		 *
		 * @return  mixed
		 * @return  string
		 * @since   2.0.0
		 * @author  Alberto Ruggiero <alberto.ruggiero@yithemes.com>
		 */
		public function get_vendor_termmeta( $value, $post_id, $term_id, $option ) {

			$vendor = yith_get_vendor( $post_id, 'product' );

			if ( $vendor->is_valid() && $this->admin_override_check( $vendor ) ) {
				$opt_val = get_term_meta( $term_id, $option . '_' . $vendor->id, true );
				$value   = ( '' !== $opt_val ) ? $opt_val : $value;
			}

			return $value;

		}

	}

	new YWCTM_Multi_Vendor();

}
