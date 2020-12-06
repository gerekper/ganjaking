<?php
/**
 * Plugin Name: WooCommerce Checkout Field Editor
 * Plugin URI: https://woocommerce.com/products/woocommerce-checkout-field-editor/
 * Description: Add, remove and modifiy fields shown on your WooCommerce checkout page.
 * Version: 1.5.38
 * Author: WooCommerce
 * Author URI: https://woocommerce.com
 * Tested up to: 5.6
 * WC tested up to: 4.7
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

define( 'WC_CHECKOUT_FIELD_EDITOR_VERSION', '1.5.38' ); // WRCS: DEFINED_VERSION.
define( 'WC_CHECKOUT_FIELD_EDITOR_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

// Plugin init hook.
add_action( 'plugins_loaded', 'wc_checkout_fields_load' );

// Plugin action links.
add_filter( 'plugin_action_links', 'wc_checkout_fields_plugin_action_links', 10, 2 );

// Plugin meta links.
add_filter( 'plugin_row_meta', 'wc_checkout_fields_plugin_row_meta', 10, 2 );

// Subscribe to automated translations.
add_filter( 'woocommerce_translations_updates_for_woocommerce-checkout-field-editor', '__return_true' );


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

/**
 * Add plugin action links.
 *
 * @param  array  $actions The original array of plugin action links.
 * @param  string $plugin_file The path to the plugin file.
 * @return array $actions The updated array of plugin action links.
 * @since 1.5.36
 * @see https://developer.wordpress.org/reference/hooks/plugin_action_links_plugin_file/
 */
function wc_checkout_fields_plugin_action_links( $actions, $plugin_file ) {
	if ( strpos( $plugin_file, basename( __FILE__ ) ) ) {
		array_unshift( $actions, '<a href="admin.php?page=checkout_field_editor">' . esc_html__( 'Settings', 'woocommerce-checkout-field-editor' ) . '</a>' );
	}
	
	return $actions;
}

/**
 * Add plugin meta links.
 *
 * @param  array  $plugin_meta The original array with the plugin's metadata.
 * @param  string $plugin_file The path to the plugin file.
 * @return array $plugin_meta The updated array with the plugin's metadata.
 * @since 1.5.36
 * @see https://developer.wordpress.org/reference/hooks/plugin_row_meta/
 */
function wc_checkout_fields_plugin_row_meta( $plugin_meta, $plugin_file ) {
	if ( strpos( $plugin_file, basename( __FILE__ ) ) ) {
		$plugin_meta[] = '<a href="https://docs.woocommerce.com/document/checkout-field-editor/">' . esc_html__( 'Docs', 'woocommerce-checkout-field-editor' ) . '</a>';
		$plugin_meta[] = '<a href="https://woocommerce.com/my-account/create-a-ticket?select=184594">' . esc_html__( 'Support', 'woocommerce-checkout-field-editor' ) . '</a>';
	}
 
	return $plugin_meta;
}
