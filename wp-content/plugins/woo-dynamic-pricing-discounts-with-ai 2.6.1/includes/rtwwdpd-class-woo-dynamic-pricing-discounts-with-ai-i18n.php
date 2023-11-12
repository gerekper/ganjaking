<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       www.redefiningtheweb.com
 * @since      1.0.0
 *
 * @package    Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai
 * @subpackage Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai
 * @subpackage Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai/includes
 * @author     RedefiningTheWeb <developer@redefiningtheweb.com>
 */
class Rtwwdpd_Woo_Dynamic_Pricing_Discounts_With_Ai_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function rtwwdpd_load_plugin_textdomain() {

		load_plugin_textdomain(
			'rtwwdpd-woo-dynamic-pricing-discounts-with-ai',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}
}
