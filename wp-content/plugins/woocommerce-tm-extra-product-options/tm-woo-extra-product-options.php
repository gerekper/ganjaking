<?php
/**
 * Plugin Name: WooCommerce TM Extra Product Options
 * Plugin URI: https://epo.themecomplete.com/
 * Description: A WooCommerce plugin for adding extra product options.
 * Author: themeComplete
 * Author URI: https://themecomplete.com/
 *
 * Version: 5.0.12.9
 *
 * Requires at least: 4.4
 * Tested up to: 5.6
 *
 * WC requires at least: 3.0
 * WC tested up to: 4.6
 *
 * @package  WooCommerce TM Extra Product Options
 * @category Core
 * @author   themecomplete
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

// Include the main Extra_Product_Options Setup class.
require_once dirname( __FILE__ ) . '/include/class-extra-product-options-setup.php';

/**
 * Main instance of Extra Product Options.
 *
 * @since  4.8
 * @return Extra Product Options
 */
function THEMECOMPLETE_EXTRA_PRODUCT_OPTIONS_SETUP() {
	return Themecomplete_Extra_Product_Options_Setup::instance();
}

// Setup the plugin.
THEMECOMPLETE_EXTRA_PRODUCT_OPTIONS_SETUP();
