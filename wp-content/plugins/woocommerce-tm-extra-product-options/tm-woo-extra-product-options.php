<?php
/**
 * Plugin Name: WooCommerce TM Extra Product Options
 * Plugin URI: https://epo.themecomplete.com/
 * Description: A WooCommerce plugin for adding extra product options.
 * Author: ThemeComplete
 * Author URI: https://themecomplete.com/
 *
 * Version: 6.0
 * Requires at least: 5.0
 * Tested up to: 7.0
 * WC requires at least: 4.1
 * WC tested up to: 7.0
 * Copyright: © 2022 THEMECOMPLETE LTD
 *
 * @package  WooCommerce TM Extra Product Options
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

// Include the main Extra_Product_Options Setup class.
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
