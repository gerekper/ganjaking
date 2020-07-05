<?php
/**
 * Plugin Name: WooCommerce Checkout Field Editor
 * Plugin URI: https://woocommerce.com/products/woocommerce-checkout-field-editor/
 * Description: Add, remove and modifiy fields shown on your WooCommerce checkout page.
 * Version: 1.5.35
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Tested up to: 5.2
 * WC tested up to: 4.2
 * WC requires at least: 2.6
 *
 * Text Domain: woocommerce-checkout-field-editor
 * Domain Path: /languages
 *
 * Copyright: © 2020 WooCommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * Woo: 184594:2b8029f0d7cdd1118f4d843eb3ab43ff
 *
 * @package woocommerce-checkout-field-editor
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

define( 'WC_CHECKOUT_FIELD_EDITOR_VERSION', '1.5.35' ); // WRCS: DEFINED_VERSION.
define( 'WC_CHECKOUT_FIELD_EDITOR_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

// Plugin init hook.
add_action( 'plugins_loaded', 'wc_checkout_fields_load' );

/**
 * Initialize plugin.
 */
function wc_checkout_fields_load() {

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'wc_checkout_fields_woocommerce_deactivated' );
		return;
	}

	require_once WC_CHECKOUT_FIELD_EDITOR_PATH . '/includes/wc-checkout-field-functions.php';

	// Maybe run plugin install.
	add_action( 'admin_init', 'wc_checkout_fields_install' );

	// Initialize the Checkout Field Editor.
	add_action( 'init', 'woocommerce_init_checkout_field_editor' );

	// Load Export Handler later as init priority 10 is too soon.
	add_action( 'init', 'woocommmerce_init_cfe_export_handler', 99 );

	// Modify Shipping and Billing fields.
	// Use Priority 1 so that the changes from Checkout Field Editor apply first. 3rd party plugins may add extra fields later.
	add_filter( 'woocommerce_billing_fields', 'wc_checkout_fields_modify_billing_fields', 1 );
	add_filter( 'woocommerce_shipping_fields', 'wc_checkout_fields_modify_shipping_fields', 1 );

	// Modify order fields.
	add_filter( 'woocommerce_checkout_fields', 'wc_checkout_fields_modify_order_fields', 1000 );

	// Maybe disable order comments.
	add_action( 'wc_checkout_fields_disable_order_comments', 'wc_checkout_fields_maybe_hide_additional_info_header' );

	// Enqueue scripts for checkout fields.
	add_action( 'wp_enqueue_scripts', 'wc_checkout_fields_scripts' );

	// Filter form fields output.
	add_filter( 'woocommerce_form_field_radio', 'wc_checkout_fields_radio_field', 10, 4 );
	add_filter( 'woocommerce_form_field_date', 'wc_checkout_fields_date_picker_field', 10, 4 );
	add_filter( 'woocommerce_form_field_multiselect', 'wc_checkout_fields_multiselect_field', 10, 4 );
	add_filter( 'woocommerce_form_field_heading', 'wc_checkout_fields_heading_field', 10, 4 );

	// Validate checkout fields.
	add_action( 'woocommerce_after_checkout_validation', 'wc_checkout_fields_validation' );

	// Add custom billing and shipping fields in admin order area.
	add_action( 'woocommerce_admin_order_data_after_billing_address', 'wc_display_custom_billing_fields_admin_order', 20, 1 );
	add_action( 'woocommerce_admin_order_data_after_shipping_address', 'wc_display_custom_shipping_fields_admin_order', 20, 1 );

	// Dequeue WooCommerce scripts.
	add_action( 'wp_enqueue_scripts', 'wc_checkout_fields_dequeue_address_i18n', 15 );
}

/**
 * WooCommerce Deactivated Notice.
 */
function wc_checkout_fields_woocommerce_deactivated() {
	/* translators: %s: WooCommerce link */
	echo '<div class="error"><p>' . sprintf( esc_html__( 'WooCommerce Checkout Field Editor requires %s to be installed and active.', 'woocommerce-checkout-field-editor' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</p></div>';
}

// Plugin activation hook.
register_activation_hook( __FILE__, 'wc_checkout_fields_activate' );

/**
 * Performs processes when plugin is activated.
 *
 * @since 1.5.6
 * @version 1.5.6
 */
function wc_checkout_fields_activate() {
	require_once WC_CHECKOUT_FIELD_EDITOR_PATH . '/includes/wc-checkout-field-functions.php';
	wc_checkout_fields_update_plugin_version();
}
