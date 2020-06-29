<?php
/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.themepunch.com
 * @since      1.0.0
 *
 * @package    Revslider_Maintenance_Addon
 * @subpackage Revslider_Maintenance_Addon/includes
 * @author     ThemePunch <info@themepunch.com>
 */

if(!defined('ABSPATH')) exit();

class Revslider_Maintenance_Addon_i18n {

	/**
	 * Load the plugin text domain for translation.
	 */
	public function load_plugin_textdomain(){
		load_plugin_textdomain('revslider-maintenance-addon', false, dirname(dirname(plugin_basename(__FILE__))) . '/languages/');
	}
}
