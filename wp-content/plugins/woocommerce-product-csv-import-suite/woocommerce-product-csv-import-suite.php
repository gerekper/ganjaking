<?php
/**
 * Plugin Name: WooCommerce Product CSV Import Suite
 * Plugin URI: https://woocommerce.com/products/product-csv-import-suite/
 * Description: Import and export products and variations straight from WordPress admin. Go to WooCommerce > CSV Import Suite to get started. Supports post fields, product data, custom post types, taxonomies, and images.
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Version: 1.10.60
 * WC requires at least: 7.2
 * Requires at least: 6.1
 * Requires PHP: 7.2
 * WC tested up to: 7.8.0
 * Tested up to: 6.2
 * Text Domain: woocommerce-product-csv-import-suite
 * Domain Path: /languages
 *
 * Copyright: © 2023 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Adapted from the WordPress post importer by the WordPress team
 *
 * Woo: 18680:7ac9b00a1fe980fb61d28ab54d167d0d
 *
 * @package WooCommerce Product CSV Import Suite
 *
 * phpcs:disable WordPress.Files.FileName
 */

if ( ! defined( 'ABSPATH' ) || ! is_admin() ) {
	return;
}

/**
 * WooCommerce fallback notice.
 *
 * @since 1.10.33
 */
function woocommerce_product_csv_import_suite_missing_wc_notice() {
	/* translators: %s WC download URL link. */
	echo '<div class="error"><p><strong>' . sprintf( esc_html__( 'Product CSV Import Suite requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-product-csv-import-suite' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</strong></p></div>';
}

if ( ! class_exists( 'WC_Product_CSV_Import_Suite' ) ) :
	define( 'WC_PCSVIS_FILE', __FILE__ );
	define( 'WC_PCSVIS_VERSION', '1.10.60' ); // WRCS: DEFINED_VERSION.

	/**
	 * Main CSV Import class
	 */
	class WC_Product_CSV_Import_Suite {

		/**
		 * Logging class
		 *
		 * @var WC_Logger
		 */
		private static $logger = false;

		/**
		 * Constructor
		 */
		public function __construct() {
			add_filter( 'woocommerce_screen_ids', array( $this, 'woocommerce_screen_ids' ) );
			add_action( 'init', array( $this, 'catch_export_request' ), 20 );
			add_action( 'admin_init', array( $this, 'register_importers' ) );
			add_action( 'before_woocommerce_init', array( $this, 'declare_hpos_compatibility' ) );

			include_once 'includes/wc-pcsvis-functions.php';
			include_once 'includes/class-wc-pcsvis-system-status-tools.php';
			include_once 'includes/class-wc-pcsvis-admin-screen.php';
			include_once 'includes/importer/class-wc-pcsvis-importer.php';

			if ( defined( 'DOING_AJAX' ) ) {
				include_once 'includes/class-wc-pcsvis-ajax-handler.php';
			}
		}

		/**
		 * Add screen ID
		 *
		 * @param array $ids Screen IDs.
		 */
		public function woocommerce_screen_ids( $ids ) {
			$ids[] = 'admin'; // For import screen.
			return $ids;
		}

		/**
		 * Catches an export request and exports the data. This class is only loaded in admin.
		 */
		public function catch_export_request() {
			if ( ! is_user_logged_in() ) {
				return;
			}

			if ( ! current_user_can( 'manage_woocommerce' ) ) {
				return;
			}

			if ( ! empty( $_GET['action'] ) && ! empty( $_GET['page'] ) && 'woocommerce_csv_import_suite' === $_GET['page'] ) {
				switch ( $_GET['action'] ) {
					case 'export':
						include_once 'includes/exporter/class-wc-pcsvis-exporter.php';
						WC_PCSVIS_Exporter::do_export( 'product' );
						break;
					case 'export_variations':
						include_once 'includes/exporter/class-wc-pcsvis-exporter.php';
						WC_PCSVIS_Exporter::do_export( 'product_variation' );
						break;
				}
			}
		}

		/**
		 * Register importers for use
		 */
		public function register_importers() {
			register_importer( 'woocommerce_csv', 'WooCommerce Products (CSV)', __( 'Import <strong>products</strong> to your store via a csv file.', 'woocommerce-product-csv-import-suite' ), 'WC_PCSVIS_Importer::product_importer' );
			register_importer( 'woocommerce_variation_csv', 'WooCommerce Product Variations (CSV)', __( 'Import <strong>product variations</strong> to your store via a csv file.', 'woocommerce-product-csv-import-suite' ), 'WC_PCSVIS_Importer::variation_importer' );
		}

		/**
		 * Get meta data direct from DB, avoiding get_post_meta and caches
		 *
		 * @param string $message Log message.
		 */
		public static function log( $message ) {
			if ( ! self::$logger ) {
				self::$logger = new WC_Logger();
			}
			self::$logger->add( 'csv-import', $message );
		}

		/**
		 * Get meta data direct from DB, avoiding get_post_meta and caches
		 *
		 * @param int    $post_id Post ID.
		 * @param string $meta_key Meta key.
		 *
		 * @return string
		 */
		public static function get_meta_data( $post_id, $meta_key ) {
			global $wpdb;
			$value = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value from $wpdb->postmeta WHERE post_id = %d and meta_key = %s", $post_id, $meta_key ) );
			return maybe_unserialize( $value );
		}

		/**
		 * Declares HPOS compatibility
		 *
		 * @since 1.10.50
		 */
		public function declare_hpos_compatibility() {
			if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
				\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
			}
		}
	}
endif;

add_action( 'plugins_loaded', 'woocommerce_product_csv_import_suite_init' );

// Subscribe to automated translations.
add_filter( 'woocommerce_translations_updates_for_' . basename( __FILE__, '.php' ), '__return_true' );

/**
 * Initializes the extension.
 *
 * @since 1.10.33
 * @return void
 */
function woocommerce_product_csv_import_suite_init() {
	load_plugin_textdomain( 'woocommerce-product-csv-import-suite', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'woocommerce_product_csv_import_suite_missing_wc_notice' );
		return;
	}

	new WC_Product_CSV_Import_Suite();
}
