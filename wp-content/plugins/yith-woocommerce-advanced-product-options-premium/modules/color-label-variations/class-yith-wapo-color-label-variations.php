<?php
/**
 * Main Color and Label Variations Module class
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\ProductAddOns
 * @version 2.0.0
 */

defined( 'YITH_WAPO' ) || exit; // Exit if accessed directly.

if ( ! class_exists( 'YITH_WAPO_Color_Label_Variations' ) ) {
	/**
	 * Color and Label Variations module for WAPO plugin.
	 *
	 * @since 2.0.0
	 */
	class YITH_WAPO_Color_Label_Variations {

		/**
		 * Single instance of the class
		 *
		 * @since 2.0.0
		 * @var YITH_WAPO_Color_Label_Variations
		 */
		protected static $instance;

		/**
		 * Returns single instance of the class
		 *
		 * @since 2.0.0
		 * @return YITH_WAPO_Color_Label_Variations
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
		 * @since 2.0.0
		 * @return void
		 */
		public function __construct() {
			$this->define_constant();
			$this->load_required();

			// Create DB table if needed.
			$this->create_table();

			// add new attribute types.
			add_filter( 'product_attributes_type_selector', array( $this, 'attribute_types' ), 10, 1 );
		}

		/**
		 * Define module constant
		 *
		 * @since  2.0.0
		 */
		protected function define_constant() {
			! defined( 'YITH_WCCL_DB_VERSION' ) && define( 'YITH_WCCL_DB_VERSION', '1.0.0' );
			! defined( 'YITH_WAPO_WCCL' ) && define( 'YITH_WAPO_WCCL', true );
			! defined( 'YITH_WAPO_WCCL_DIR' ) && define( 'YITH_WAPO_WCCL_DIR', YITH_WAPO_DIR . 'modules/color-label-variations/' );
			! defined( 'YITH_WAPO_WCCL_ASSETS_URL' ) && define( 'YITH_WAPO_WCCL_ASSETS_URL', YITH_WAPO_URL . 'modules/color-label-variations/assets/' );
		}

		/**
		 * Load required file
		 *
		 * @since  2.0.0
		 */
		protected function load_required() {
			// Class admin.
			if ( $this->is_admin() ) {
				// require classes.
				require_once 'includes/class-yith-wapo-color-label-variations-admin.php';
			} else {
				// require classes.
				require_once 'includes/class-yith-wapo-color-label-variations-frontend.php';
			}
		}

		/**
		 * Create DB table if missing
		 *
		 * @since  2.0.0
		 */
		protected function create_table() {
			global $wpdb;

			$installed_ver = get_option( 'yith_wccl_db_version' );

			if ( YITH_WCCL_DB_VERSION !== $installed_ver ) {

				$table_name = $wpdb->prefix . 'yith_wccl_meta';

				$charset_collate = $wpdb->get_charset_collate();

				$sql = "CREATE TABLE IF NOT EXISTS $table_name (
		meta_id bigint(20) NOT NULL AUTO_INCREMENT,
		wc_attribute_tax_id bigint(20) NOT NULL,
		meta_key varchar(255) DEFAULT '',
		meta_value longtext DEFAULT '',
		PRIMARY KEY (meta_id)
		) $charset_collate;";

				require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				dbDelta( $sql );

				add_option( 'yith_wccl_db_version', YITH_WCCL_DB_VERSION );
			}
		}

		/**
		 * Check if context is admin
		 *
		 * @since  2.0.0
		 * @return boolean
		 */
		public function is_admin() {
			$is_frontend = isset( $_REQUEST['context'] ) && 'frontend' === $_REQUEST['context']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return apply_filters( 'yith_wapo_color_label_load_admin_class', ( is_admin() && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX && $is_frontend ) ) );
		}

		/**
		 * Add new attribute types to standard WooCommerce
		 *
		 * @since  2.0.0
		 * @param array $default_type Default type.
		 * @return array
		 */
		public function attribute_types( $default_type ) {
			$custom = self::get_custom_attribute_types();
			return is_array( $custom ) ? array_merge( $default_type, $custom ) : $default_type;
		}

		/**
		 * Get custom attribute types
		 *
		 * @since  2.0.0
		 * @return array
		 */
		public static function get_custom_attribute_types() {
			return array(
				'colorpicker' => _x( 'Color picker', 'Products > Attributes > Types', 'yith-woocommerce-product-add-ons' ),
				'image'       => _x( 'Image', 'Products > Attributes > Types', 'yith-woocommerce-product-add-ons' ),
				'label'       => _x( 'Label', 'Products > Attributes > Types', 'yith-woocommerce-product-add-ons' ),
			);
		}

		/**
		 * Get variation gallery
		 *
		 * @since 2.0.0
		 * @param WC_Product | WP_Post $variation Variation object.
		 */
		public static function get_variation_gallery( $variation ) {

			global $sitepress;

			if ( ! ( $variation instanceof WC_Product ) ) {
				$variation = wc_get_product( $variation->ID );
			}

			if ( ! $variation ) {
				return array();
			}

			$gallery = $variation->get_meta( '_yith_wccl_gallery', true );
			if ( empty( $gallery ) && function_exists( 'wpml_object_id_filter' ) && ! empty( $sitepress ) && apply_filters( 'yith_wccl_use_parent_gallery_for_translated_products', true ) ) {
				$parent_id = wpml_object_id_filter( $variation->get_id(), 'product_variation', false, $sitepress->get_default_language() );
				if ( ! empty( $parent_id ) ) {
					$variation = wc_get_product( $parent_id );
					if ( $variation ) {
						$gallery = $variation->get_meta( '_yith_wccl_gallery', true );
					}
				}
			}

			return $gallery;
		}
	}
}

/**
 * Unique access to instance of YITH_WAPO_Color_Label_Variations class
 *
 * @since 2.0.0
 * @return YITH_WAPO_Color_Label_Variations
 */
function YITH_WAPO_Color_Label_Variations() { // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
	return YITH_WAPO_Color_Label_Variations::get_instance();
}

YITH_WAPO_Color_Label_Variations();
