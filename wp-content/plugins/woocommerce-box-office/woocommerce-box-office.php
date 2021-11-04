<?php
/**
 * Plugin Name: WooCommerce Box Office
 * Version: 1.1.36
 * Plugin URI: https://woocommerce.com/products/woocommerce-box-office/
 * Description: The ultimate event ticket management system, built right on top of WooCommerce.
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * License: GPL-2.0+
 * Requires at least: 4.4
 * Tested up to: 5.8
 * Text Domain: woocommerce-box-office
 * Domain Path: /languages
 * WC tested up to: 5.8
 * WC requires at least: 2.6
 *
 * Woo: 1628717:e704c9160de318216a8fa657404b9131
 *
 * Copyright: © 2021 WooCommerce
 *
 * @package woocommerce-box-office
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WOOCOMMERCE_BOX_OFFICE_VERSION', '1.1.36' ); // WRCS: DEFINED_VERSION.

// Plugin init hook.
add_action( 'plugins_loaded', 'wc_box_office_init', 5 );

/**
 * Initialize plugin.
 */
function wc_box_office_init() {

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'wc_box_office_woocommerce_deactivated' );
		return;
	}

	// Load main plugin class.
	require_once 'includes/class-wc-box-office.php';
	require_once 'includes/wcbo-functions.php';
	WCBO()->init();
}

// Plugin activation.
register_activation_hook( __FILE__, 'wc_box_office_maybe_install' );

/**
 * Plugin update.
 */
function wc_box_office_maybe_install() {
	require_once 'includes/class-wc-box-office.php';
	require_once 'includes/class-wc-box-office-updater.php';
	require_once 'includes/wcbo-functions.php';

	$updater = new WC_Box_Office_Updater();
	$updater->install();
}

/**
 * WooCommerce Deactivated Notice.
 */
function wc_box_office_woocommerce_deactivated() {
	/* translators: %s: WooCommerce link */
	echo '<div class="error"><p>' . sprintf( esc_html__( 'WooCommerce Box Office requires %s to be installed and active.', 'woocommerce-box-office' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</p></div>';
}
