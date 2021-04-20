<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Automattic\WooCommerce\Admin\Features\Navigation\Menu;

class WC_PCSVIS_Admin_Screen {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'woocommerce_screen_ids', array( $this, 'screen_id' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_print_styles', array( $this, 'admin_scripts' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );

		// Register menu items in the new WooCommerce navigation.
		add_action( 'admin_menu', array( $this, 'register_navigation_items' ) );
	}

	/**
	 * Add screen id
	 * @param  array $ids
	 * @return array
	 */
	public function screen_id( $ids ) {
		$wc_screen_id = sanitize_title( __( 'WooCommerce', 'woocommerce' ) );
		$ids[]        = $wc_screen_id . '_page_woocommerce_csv_import_suite';
		return $ids;
	}

	/**
	 * Notices in admin
	 */
	public function admin_notices() {
		if ( ! function_exists( 'mb_detect_encoding' ) ) {
			echo '<div class="error"><p>' . __( 'CSV Import Suite requires the function <code>mb_detect_encoding</code> to import and export CSV files. Please ask your hosting provider to enable this function.', 'woocommerce-product-csv-import-suite' ) . '</p></div>';
		}
	}

	/**
	 * Admin Menu
	 */
	public function admin_menu() {
		$page = add_submenu_page( 'woocommerce', __( 'CSV Import Suite', 'woocommerce-product-csv-import-suite' ), __( 'CSV Import Suite', 'woocommerce-product-csv-import-suite' ), apply_filters( 'woocommerce_csv_product_role', 'manage_woocommerce' ), 'woocommerce_csv_import_suite', array( $this, 'output' ) );
	}

	/**
	 * Register the navigation items in the WooCommerce navigation.
	 */
	public function register_navigation_items() {
		if ( ! method_exists( Menu::class, 'add_plugin_item' ) ) {
			return;
		}

		Menu::add_plugin_item(
			array(
				'id'         => 'woocommerce_csv_import_suite',
				'title'      => __( 'CSV Import Suite', 'woocommerce-product-csv-import-suite' ),
				'capability' => 'manage_woocommerce',
				'url'        => 'woocommerce_csv_import_suite',
			)
		);
	}

	/**
	 * Admin Scripts
	 */
	public function admin_scripts() {
		wp_enqueue_style( 'woocommerce-product-csv-importer', plugins_url( basename( plugin_dir_path( WC_PCSVIS_FILE ) ) . '/assets/css/style.css', basename( __FILE__ ) ), '', WC_PCSVIS_VERSION, 'screen' );
	}

	/**
	 * Admin Screen output
	 */
	public function output() {
		$tab = ! empty( $_GET['tab'] ) && $_GET['tab'] == 'export' ? 'export' : 'import';
		include( 'views/html-admin-screen.php' );
	}

	/**
	 * Admin page for importing
	 */
	public function admin_import_page() {
		include( 'views/html-getting-started.php' );
		include( 'views/import/html-import-products.php' );
		include( 'views/import/html-import-variations.php' );
	}

	/**
	 * Admin Page for exporting
	 */
	public function admin_export_page() {
		$post_columns = include( 'exporter/data/data-post-columns.php' );
		include( 'views/export/html-export-products.php' );
		$variation_columns = include( 'exporter/data/data-variation-columns.php' );
		include( 'views/export/html-export-variations.php' );
	}
}

new WC_PCSVIS_Admin_Screen();
