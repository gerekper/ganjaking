<?php namespace NinjaTables\Classes;

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://wpmanageninja.com
 * @since      1.0.0
 *
 * @package    ninja-tables
 * @subpackage ninja-tables/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    ninja-tables
 * @subpackage ninja-tables/includes
 * @author     Shahjahan Jewel <cep.jewel@gmail.com>
 */
class NinjaTablesI18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'ninja-tables',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
