<?php
/**
 * Plugin Name: Extra Product Options & Add-Ons for WooCommerce
 * Plugin URI: https://epo.themecomplete.com/
 * Description: <code><strong>Extra Product Options</strong></code> enables you to create extra add-ons and options to a WooCommerce product.
 * Author: ThemeComplete
 * Author URI: https://themecomplete.com/
 *
 * Version: 6.3
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Tested up to: 7.0
 * WC requires at least: 4.1
 * WC tested up to: 8.0
 * Copyright: © 2023 THEMECOMPLETE LTD
 *
 * @package  Extra Product Options & Add-Ons for WooCommerce
 * @category Core
 * @author   ThemeComplete
 */

defined( 'ABSPATH' ) || exit;

// Define THEMECOMPLETE_EPO_PLUGIN_FILE.
if ( ! defined( 'THEMECOMPLETE_EPO_PLUGIN_FILE' ) ) {
	define( 'THEMECOMPLETE_EPO_PLUGIN_FILE', __FILE__ );
}

// Check if another plugin is overwriting our classes.
if ( class_exists( 'Themecomplete_Extra_Product_Options_Setup' ) ) {
	return;
}

// Include the main Extra Product Options Setup class.
require_once dirname( __FILE__ ) . '/includes/class-themecomplete-extra-product-options-setup.php';

/**
 * Main instance of Themecomplete_Extra_Product_Options_Setup.
 *
 * @since  4.8
 * @return Themecomplete_Extra_Product_Options_Setup
 */
function themecomplete_extra_product_options_setup() {
	return Themecomplete_Extra_Product_Options_Setup::instance();
}

// Setup the plugin.
themecomplete_extra_product_options_setup();
