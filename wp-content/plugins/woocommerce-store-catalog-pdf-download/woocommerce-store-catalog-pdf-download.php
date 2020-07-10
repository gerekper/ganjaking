<?php
/**
 * Plugin Name: WooCommerce Store Catalog PDF Download
 * Plugin URI: https://woocommerce.com/products/woocommerce-store-catalog-pdf-download/
 * Description: A WooCommerce plugin/extension that adds ability for users to download a PDF of your store catalog.
 * Version: 1.10.25
 * WC requires at least: 2.6
 * WC tested up to: 4.2
 * Tested up to: 5.3
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: woocommerce-store-catalog-pdf-download
 * Domain Path: /languages
 *
 * Woo: 675790:79ca7aadafe706364e2d738b7c1090c4
 *
 * @package woocommerce-store-catalog-pdf-download
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

register_activation_hook( __FILE__, 'activate_woocommerce_store_catalog_pdf_download' );
register_deactivation_hook( __FILE__, 'deactivate_woocommerce_store_catalog_pdf_download' );

/**
 * Run on activate
 *
 * @since 1.0.0
 * @return bool
 */
function activate_woocommerce_store_catalog_pdf_download() {
	wp_schedule_event( current_time( 'timestamp' ), 'daily', 'woocommerce_store_catalog_pdf_download_run_cron' );

	// Create directory.
	$upload_dir = wp_upload_dir();
	$pdf_path   = $upload_dir['basedir'] . '/woocommerce-store-catalog-pdf-download/';

	if ( ! is_dir( $pdf_path ) ) {
		@mkdir( $pdf_path );
	}

	return true;
}

/**
 * Run on deactivate
 *
 * @since 1.0.0
 * @return bool
 */
function deactivate_woocommerce_store_catalog_pdf_download() {
	wp_clear_scheduled_hook( 'woocommerce_store_catalog_pdf_download_run_cron' );

	return true;
}

if ( ! class_exists( 'WC_Store_Catalog_PDF_Download' ) ) :

	define( 'WC_STORE_CATALOG_PDF_DOWNLOAD_VERSION', '1.10.25' ); // WRCS: DEFINED_VERSION.

	/**
	 * Main class.
	 *
	 * @package  WC_Store_Catalog_PDF_Download
	 */
	class WC_Store_Catalog_PDF_Download {

		/**
		 * Init
		 *
		 * @since 1.0.0
		 * @return bool
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

			add_action( 'woocommerce_store_catalog_pdf_download_run_cron', array( $this, 'remove_pdfs' ) );

			if ( class_exists( 'WooCommerce' ) ) {
				if ( is_admin() ) {
					include_once 'includes/class-wc-store-catalog-pdf-download-admin.php';
					include_once 'includes/class-wc-store-catalog-pdf-download-ajax.php';
				} else {
					include_once 'includes/class-wc-store-catalog-pdf-download-frontend.php';
				}
			} else {
				add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
			}

			return true;
		}

		/**
		 * Load the plugin text domain for translation.
		 *
		 * @since 1.0.0
		 * @return bool
		 */
		public function load_plugin_textdomain() {
			$locale = apply_filters( 'wc_store_catalog_pdf_download_locale', get_locale(), 'woocommerce-store-catalog-pdf-download' );

			load_textdomain( 'woocommerce-store-catalog-pdf-download', trailingslashit( WP_LANG_DIR ) . 'woocommerce-store-catalog-pdf-download/woocommerce-store-catalog-pdf-download' . '-' . $locale . '.mo' );

			load_plugin_textdomain( 'woocommerce-store-catalog-pdf-download', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

			return true;
		}

		/**
		 * WooCommerce fallback notice.
		 */
		public function woocommerce_missing_notice() {
			/* translators: %s: WooCommerce link */
			echo '<div class="error"><p>' . sprintf( esc_html__( 'WooCommerce Store Catalog PDF Download Plugin requires WooCommerce to be installed and active. You can download %s here.', 'woocommerce-store-catalog-pdf-download' ), '<a href="https://woocommerce.com" target="_blank">WooCommerce</a>' ) . '</p></div>';
		}

		/**
		 * Remove pdfs from upload folder
		 *
		 * @return bool
		 */
		public function remove_pdfs() {
			// Remove pdf files.
			$upload_dir = wp_upload_dir();
			$pdf_path   = $upload_dir['basedir'] . '/woocommerce-store-catalog-pdf-download';

			if ( is_dir( $pdf_path ) ) {
				$files = glob( $pdf_path . '/*' );

				// Remove each file.
				foreach ( $files as $file ) {
					if ( is_file( $file ) ) {
						@unlink( $file );
					}
				}
			}

			return true;
		}
	}

	add_action( 'plugins_loaded', 'woocommerce_store_catalog_pdf_download_init', 0 );

	/**
	 * Init
	 *
	 * @package WC_Store_Catalog_PDF_Download
	 * @since 1.0.0
	 * @return bool
	 */
	function woocommerce_store_catalog_pdf_download_init() {
		new WC_Store_Catalog_PDF_Download();

		return true;
	}

endif;
