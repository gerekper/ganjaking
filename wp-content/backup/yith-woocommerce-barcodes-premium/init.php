<?php
/**
 * Plugin Name: YITH WooCommerce Barcodes Premium
 * Plugin URI: https://yithemes.com/themes/plugins/yith-woocommerce-barcodes-and-qr-codes/
 * Description: <code><strong>YITH WooCommerce Barcodes</strong></code> allows you to generate and display barcode and QR code for your orders and products. <a href="https://yithemes.com/" target="_blank">Get more plugins for your e-commerce shop on <strong>YITH</strong></a>.
 * Version: 2.0.0
 * Author: YITH
 * Author URI: https://yithemes.com/
 * Text Domain: yith-woocommerce-barcodes
 * Domain Path: /languages/
 * WC requires at least: 3.3.0
 * WC tested up to: 4.2
 **/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/** Define plugin constants */
defined( 'YITH_YWBC_FILE' ) || define( 'YITH_YWBC_FILE', __FILE__ );
defined( 'YITH_YWBC_PREMIUM' ) || define( 'YITH_YWBC_PREMIUM', '1' );
defined( 'YITH_YWBC_SLUG' ) || define( 'YITH_YWBC_SLUG', 'yith-woocommerce-barcodes' );
defined( 'YITH_YWBC_SECRET_KEY' ) || define( 'YITH_YWBC_SECRET_KEY', 'r5Bl7QvX1UtvEGs0viOS' );
defined( 'YITH_YWBC_INIT' ) || define( 'YITH_YWBC_INIT', plugin_basename( YITH_YWBC_FILE ) );
defined( 'YITH_YWBC_BASENAME' ) || define( 'YITH_YWBC_BASENAME', plugin_basename( YITH_YWBC_FILE ) );
defined( 'YITH_YWBC_VERSION' ) || define( 'YITH_YWBC_VERSION', '2.0.0' );
defined( 'YITH_YWBC_DB_VERSION' ) || define( 'YITH_YWBC_DB_VERSION', '1.0.0' );
defined( 'YITH_YWBC_DIR' ) || define( 'YITH_YWBC_DIR', plugin_dir_path( YITH_YWBC_FILE ) );
defined( 'YITH_YWBC_URL' ) || define( 'YITH_YWBC_URL', plugins_url( '/', YITH_YWBC_FILE ) );
defined( 'YITH_YWBC_ASSETS_URL' ) || define( 'YITH_YWBC_ASSETS_URL', YITH_YWBC_URL . 'assets' );
defined( 'YITH_YWBC_ASSETS_DIR' ) || define( 'YITH_YWBC_ASSETS_DIR', YITH_YWBC_DIR . 'assets' );
defined( 'YITH_YWBC_SCRIPTS_URL' ) || define( 'YITH_YWBC_SCRIPTS_URL', YITH_YWBC_URL . 'assets/js/' );
defined( 'YITH_YWBC_TEMPLATE_PATH' ) || define( 'YITH_YWBC_TEMPLATE_PATH', YITH_YWBC_DIR . 'templates' );
defined( 'YITH_YWBC_TEMPLATES_DIR' ) || define( 'YITH_YWBC_TEMPLATES_DIR', YITH_YWBC_DIR . '/templates/' );
defined( 'YITH_YWBC_ASSETS_IMAGES_URL' ) || define( 'YITH_YWBC_ASSETS_IMAGES_URL', YITH_YWBC_ASSETS_URL . '/images/' );
defined( 'YITH_YWBC_INCLUDES_DIR' ) || define( 'YITH_YWBC_INCLUDES_DIR', YITH_YWBC_DIR . '/includes/' );

$wp_upload_dir = wp_upload_dir();
defined( 'YITH_YWBC_UPLOAD_DIR' ) || define( 'YITH_YWBC_UPLOAD_DIR', $wp_upload_dir['basedir'] . '/yith-barcodes' );
defined( 'YITH_YWBC_UPLOAD_URL' ) || define( 'YITH_YWBC_UPLOAD_URL', $wp_upload_dir['baseurl'] . '/yith-barcodes' );

if ( ! file_exists( YITH_YWBC_UPLOAD_DIR ) ) {
	wp_mkdir_p( YITH_YWBC_UPLOAD_DIR );
}

require_once YITH_YWBC_DIR . 'functions.php';
yith_initialize_plugin_fw( YITH_YWBC_DIR );

/* Plugin Framework Version Check */
yit_maybe_plugin_fw_loader( plugin_dir_path( __FILE__ ) );

/* Register the plugin when activated */
register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

/* Start the plugin on plugins_loaded */
add_action( 'plugins_loaded', 'yith_ywbc_install', 11 );
