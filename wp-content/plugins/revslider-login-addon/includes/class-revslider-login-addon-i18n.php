<?php
/**
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.themepunch.com
 * @package    Revslider_Login_Addon
 * @subpackage Revslider_Login_Addon/includes
 * @author     ThemePunch <info@themepunch.com>
 */
class Revslider_Login_Addon_i18n {
	
	/**
	 * Load the plugin text domain for translation.
	 */
	public function load_plugin_textdomain(){
		load_plugin_textdomain('revslider-login-addon', false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/');
	}
}
