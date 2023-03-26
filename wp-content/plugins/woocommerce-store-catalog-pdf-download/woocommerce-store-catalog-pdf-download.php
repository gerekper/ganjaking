<?php
/**
 * Plugin Name: WooCommerce Store Catalog PDF Download
 * Plugin URI: https://woocommerce.com/products/woocommerce-store-catalog-pdf-download/
 * Description: A WooCommerce plugin/extension that adds ability for users to download a PDF of your store catalog.
 * Version: 2.1.0
 * Author: Themesquad
 * Author URI: https://themesquad.com/
 * Requires PHP: 7.1
 * Requires at least: 4.7
 * Tested up to: 6.2
 * Text Domain: woocommerce-store-catalog-pdf-download
 * Domain Path: /languages
 *
 * WC requires at least: 3.5
 * WC tested up to: 7.5
 * Woo: 675790:79ca7aadafe706364e2d738b7c1090c4
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package woocommerce-store-catalog-pdf-download
 */

defined( 'ABSPATH' ) || exit;

// Load the class autoloader.
require __DIR__ . '/src/Autoloader.php';

if ( ! \Themesquad\WC_Store_Catalog_PDF_Download\Autoloader::init() ) {
	return;
}

// Plugin requirements.
\Themesquad\WC_Store_Catalog_PDF_Download\Requirements::init();

if ( ! \Themesquad\WC_Store_Catalog_PDF_Download\Requirements::are_satisfied() ) {
	return;
}

// Define plugin file constant.
if ( ! defined( 'WC_STORE_CATALOG_PDF_DOWNLOAD_FILE' ) ) {
	define( 'WC_STORE_CATALOG_PDF_DOWNLOAD_FILE', __FILE__ );
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

	/**
	 * Main class.
	 *
	 * @package  WC_Store_Catalog_PDF_Download
	 */
	final class WC_Store_Catalog_PDF_Download extends Themesquad\WC_Store_Catalog_PDF_Download\Plugin {

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 * @since 2.0.0 Changed visibility to protected.
		 */
		protected function __construct() {
			parent::__construct();

			add_action( 'woocommerce_store_catalog_pdf_download_run_cron', array( $this, 'remove_pdfs' ) );

			if ( is_admin() ) {
				include_once 'includes/class-wc-store-catalog-pdf-download-admin.php';
				include_once 'includes/class-wc-store-catalog-pdf-download-ajax.php';
			} else {
				include_once 'includes/class-wc-store-catalog-pdf-download-frontend.php';
			}
		}

		/**
		 * Load the plugin text domain for translation.
		 *
		 * @since 1.0.0
		 */
		public function load_plugin_textdomain() {
			$locale = apply_filters( 'wc_store_catalog_pdf_download_locale', get_locale(), 'woocommerce-store-catalog-pdf-download' );

			load_textdomain( 'woocommerce-store-catalog-pdf-download', trailingslashit( WP_LANG_DIR ) . 'woocommerce-store-catalog-pdf-download/woocommerce-store-catalog-pdf-download' . '-' . $locale . '.mo' );

			parent::load_plugin_textdomain();
		}

		/**
		 * WooCommerce fallback notice.
		 *
		 * @deprecated 2.0.0
		 */
		public function woocommerce_missing_notice() {
			wc_deprecated_function( __FUNCTION__, '2.0.0' );

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
	 * Init.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	function woocommerce_store_catalog_pdf_download_init() {
		WC_Store_Catalog_PDF_Download::instance();

		return true;
	}

endif;
