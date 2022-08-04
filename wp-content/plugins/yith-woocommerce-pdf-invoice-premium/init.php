<?php
/**
 * Plugin Name: YITH WooCommerce PDF Invoices & Packing Slips Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-pdf-invoice/
 * Description: <code><strong>YITH WooCommerce PDF Invoices & Packing Slips</strong></code> generate PDF invoices, credit notes, pro-forma invoices and packing slips for WooCommerce orders. Set manual or automatic invoice generation, fully customizable document template and sync with your Dropbox and Google Drive accounts. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>.
 * Version: 3.4.0
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-pdf-invoice
 * Domain Path: /languages/
 * WC requires at least: 5.8
 * WC tested up to: 6.0
 *
 * @author  YITH
 * @package YITH\PDFInvoice
 **/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.


if ( ! function_exists( 'is_plugin_active' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}

if ( ! function_exists( 'yith_ywpi_premium_install_woocommerce_admin_notice' ) ) {
	/**
	 * Show notice if WooCommerce is not active
	 *
	 * @author YITH
	 * @since  1.0.0
	 */
	function yith_ywpi_premium_install_woocommerce_admin_notice() {
		?>
		<div class="error">

			<p><?php esc_html_e( 'YITH WooCommerce PDF Invoice is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-pdf-invoice' ); ?></p>
		</div>
		<?php
	}
}
/**
 * Check if a free version is currently active and try disabling before activating this one
 */
if ( ! function_exists( 'yit_deactive_free_version' ) ) {
	require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YITH_YWPI_FREE_INIT', plugin_basename( __FILE__ ) );


if ( ! function_exists( 'yith_plugin_registration_hook' ) ) {
	require_once 'plugin-fw/yit-plugin-registration-hook.php';
}
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

defined( 'YITH_YWPI_INIT' ) || define( 'YITH_YWPI_INIT', plugin_basename( __FILE__ ) );
defined( 'YITH_YWPI_PREMIUM' ) || define( 'YITH_YWPI_PREMIUM', '1' );
defined( 'YITH_YWPI_SLUG' ) || define( 'YITH_YWPI_SLUG', 'yith-woocommerce-pdf-invoice' );
defined( 'YITH_YWPI_SECRET_KEY' ) || define( 'YITH_YWPI_SECRET_KEY', '123456' );
defined( 'YITH_YWPI_VERSION' ) || define( 'YITH_YWPI_VERSION', '3.4.0' );
defined( 'YITH_YWPI_ENQUEUE_VERSION' ) || define( 'YITH_YWPI_ENQUEUE_VERSION', '3.4.0' );
defined( 'YITH_YWPI_FILE' ) || define( 'YITH_YWPI_FILE', __FILE__ );
defined( 'YITH_YWPI_DIR' ) || define( 'YITH_YWPI_DIR', plugin_dir_path( __FILE__ ) );
defined( 'YITH_YWPI_URL' ) || define( 'YITH_YWPI_URL', plugins_url( '/', __FILE__ ) );
defined( 'YITH_YWPI_ASSETS_URL' ) || define( 'YITH_YWPI_ASSETS_URL', YITH_YWPI_URL . 'assets' );
defined( 'YITH_YWPI_ASSETS_DIR' ) || define( 'YITH_YWPI_ASSETS_DIR', YITH_YWPI_DIR . 'assets' );
defined( 'YITH_YWPI_TEMPLATE_DIR' ) || define( 'YITH_YWPI_TEMPLATE_DIR', YITH_YWPI_DIR . 'templates/' );
defined( 'YITH_YWPI_INVOICE_TEMPLATE_URL' ) || define( 'YITH_YWPI_INVOICE_TEMPLATE_URL', YITH_YWPI_URL . 'templates/invoice/' );
defined( 'YITH_YWPI_INVOICE_TEMPLATE_DIR' ) || define( 'YITH_YWPI_INVOICE_TEMPLATE_DIR', YITH_YWPI_DIR . 'templates/invoice/' );
defined( 'YITH_YWPI_ASSETS_IMAGES_URL' ) || define( 'YITH_YWPI_ASSETS_IMAGES_URL', YITH_YWPI_ASSETS_URL . '/images/' );
defined( 'YITH_YWPI_ASSETS_IMAGES_DIR' ) || define( 'YITH_YWPI_ASSETS_IMAGES_DIR', YITH_YWPI_ASSETS_DIR . '/images/' );
defined( 'YITH_YWPI_LIB_DIR' ) || define( 'YITH_YWPI_LIB_DIR', YITH_YWPI_DIR . 'lib/' );
defined( 'YITH_YWPI_INC_DIR' ) || define( 'YITH_YWPI_INC_DIR', YITH_YWPI_DIR . 'includes/' );
defined( 'YITH_YWPI_VIEWS_PATH' ) || define( 'YITH_YWPI_VIEWS_PATH', YITH_YWPI_DIR . 'views/' );

$wp_upload_dir = wp_upload_dir();

defined( 'YITH_YWPI_DOCUMENT_SAVE_DIR' ) || define( 'YITH_YWPI_DOCUMENT_SAVE_DIR', $wp_upload_dir['basedir'] . '/ywpi-pdf-invoice/' );
defined( 'YITH_YWPI_SAVE_INVOICE_URL' ) || define( 'YITH_YWPI_SAVE_INVOICE_URL', $wp_upload_dir['baseurl'] . '/ywpi-pdf-invoice/' );
defined( 'YITH_YWPI_INVOICE_LOGO_PATH' ) || define( 'YITH_YWPI_INVOICE_LOGO_PATH', YITH_YWPI_DOCUMENT_SAVE_DIR . 'invoice-logo.jpg' );

/* Plugin Framework Version Check */
if ( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_YWPI_DIR . 'plugin-fw/init.php' ) ) {
	require_once YITH_YWPI_DIR . 'plugin-fw/init.php';
}
yit_maybe_plugin_fw_loader( YITH_YWPI_DIR );

if ( ! function_exists( 'yith_ywpi_premium_init' ) ) {
	/**
	 * Init the plugin
	 *
	 * @author YITH
	 * @since  1.0.0
	 */
	function yith_ywpi_premium_init() {

		/* Load YWPI text domain */
		load_plugin_textdomain( 'yith-woocommerce-pdf-invoice', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

		// Load required classes and functions.
		require_once YITH_YWPI_INC_DIR . 'class.yith-ywpi-plugin-fw-loader.php';
		require_once YITH_YWPI_INC_DIR . 'class.yith-checkout-addon.php';
		require_once YITH_YWPI_INC_DIR . 'class.yith-woocommerce-pdf-invoice.php';
		require_once YITH_YWPI_INC_DIR . 'class.yith-ywpi-backend.php';
		require_once YITH_YWPI_INC_DIR . 'class.yith-woocommerce-pdf-invoice-premium.php';
		require_once YITH_YWPI_INC_DIR . 'class.yith-pdf-invoice-dropbox.php';
		require_once YITH_YWPI_INC_DIR . 'class.yith-pdf-invoice-google-drive.php';
		require_once YITH_YWPI_INC_DIR . 'class.yith-electronic-invoice.php';
		require_once YITH_YWPI_INC_DIR . 'class.yith-documents-bulk.php';
		require_once YITH_YWPI_INC_DIR . 'class.yith-ywpi-list-tables.php';

		/*
		Documents
		*/
		require_once YITH_YWPI_INC_DIR . 'documents/class-yith-document.php';
		require_once YITH_YWPI_INC_DIR . 'documents/class-yith-invoice.php';
		require_once YITH_YWPI_INC_DIR . 'documents/class-yith-pro-forma.php';
		require_once YITH_YWPI_INC_DIR . 'documents/class-yith-credit-note.php';
		require_once YITH_YWPI_INC_DIR . 'documents/class-yith-shipping.php';
		// require_once YITH_YWPI_INC_DIR . 'documents/class-yith-xml.php'; .

		require_once YITH_YWPI_INC_DIR . 'class.yith-invoice-details.php';
		require_once YITH_YWPI_INC_DIR . 'functions-yith-ywpi.php';
		require_once YITH_YWPI_DIR . 'functions.php';

		YITH_YWPI_Plugin_FW_Loader::get_instance();
		YITH_PDF_Invoice();
		YITH_Electronic_Invoice();
		YITH_YWPI_List_Tables::get_instance();

		if ( class_exists( 'YITH_Documents_Bulk' ) ) {
			YITH_Documents_Bulk::get_instance();
		}

		add_action( 'init', 'ywpi_start_plugin_compatibility', 20 );
	}
}
add_action( 'yith_ywpi_premium_init', 'yith_ywpi_premium_init' );



if ( ! function_exists( 'YITH_PDF_Invoice' ) ) {
	/**
	 * Retrieve the instance of the plugin main class
	 *
	 * @return YITH_WooCommerce_Pdf_Invoice_Premium
	 * @author YITH
	 * @since  1.0.0
	 */
	function YITH_PDF_Invoice() { // phpcs:ignore
		return YITH_WooCommerce_Pdf_Invoice_Premium::get_instance();
	}
}

if ( ! function_exists( 'yith_ywpi_premium_install' ) ) {
	/**
	 * Install the plugin
	 *
	 * @author YITH
	 * @since  1.0.0
	 */
	function yith_ywpi_premium_install() {

		if ( ! function_exists( 'WC' ) ) {
			add_action( 'admin_notices', 'yith_ywpi_premium_install_woocommerce_admin_notice' );
		} else {
			do_action( 'yith_ywpi_premium_init' );
		}
		if ( ! get_option( 'yith_wc_pdf_invoice_check_folder_already_protected' ) ) {
			yith_ywpi_protect_folder();
		}

	}
}
add_action( 'plugins_loaded', 'yith_ywpi_premium_install', 11 );

register_activation_hook( __FILE__, 'yith_ywpi_protect_folder' );

if ( ! function_exists( 'yith_ywpi_protect_folder' ) ) {
	/**
	 * Create files/directories to protect upload folders
	 */
	function yith_ywpi_protect_folder() {

		$files = array(
			array(
				'base'    => YITH_YWPI_DOCUMENT_SAVE_DIR,
				'file'    => 'index.html',
				'content' => '',
			),
			array(
				'base'    => YITH_YWPI_DOCUMENT_SAVE_DIR,
				'file'    => '.htaccess',
				'content' => 'deny from all',
			),
		);

		foreach ( $files as $file ) {
			if ( wp_mkdir_p( $file['base'] ) && ! file_exists( trailingslashit( $file['base'] ) . $file['file'] ) ) {
				if ( $file_handle = @fopen( trailingslashit( $file['base'] ) . $file['file'], 'w' ) ) { //phpcs:ignore
					fwrite( $file_handle, $file['content'] ); //phpcs:ignore
					fclose( $file_handle ); //phpcs:ignore
				}
			}
		}

		// Updating the option not to execute the function 'yith_ywpi_protect_folder' again.
		update_option( 'yith_wc_pdf_invoice_check_folder_already_protected', true );
	}
}

if ( ! function_exists( 'yith_plugin_onboarding_registration_hook' ) ) {
	include_once 'plugin-upgrade/functions-yith-licence.php';
}
register_activation_hook( __FILE__, 'yith_plugin_onboarding_registration_hook' );
