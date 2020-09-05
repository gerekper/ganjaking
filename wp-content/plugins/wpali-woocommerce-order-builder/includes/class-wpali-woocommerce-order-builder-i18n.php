<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://wpali.com
 * @since      1.0.0
 *
 * @package    Wpali_Woocommerce_Order_Builder
 * @subpackage Wpali_Woocommerce_Order_Builder/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wpali_Woocommerce_Order_Builder
 * @subpackage Wpali_Woocommerce_Order_Builder/includes
 * @author     ALI KHALLAD <ali@wpali.com>
 */
class Wpali_Woocommerce_Order_Builder_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wpali-woocommerce-order-builder',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
