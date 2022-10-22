<?php
namespace Perfmatters;

use WP_Admin_Bar;

class Config
{
	public static $options;
	public static $tools;

	//initialize config
	public static function init()
	{
		//load plugin options
		self::$options = get_option('perfmatters_options');
		self::$tools = get_option('perfmatters_tools');

		//actions
		add_action('admin_bar_menu', array('Perfmatters\Config', 'admin_bar_menu'), 500);
	}

	//setup admin bar menu
	public static function admin_bar_menu(WP_Admin_Bar $wp_admin_bar) {

		if(!current_user_can('manage_options') || !perfmatters_network_access() || !empty(self::$tools['hide_admin_bar_menu'])) {
			return;
		}

		//add top level menu item
		$wp_admin_bar->add_menu(array(
			'id'    => 'perfmatters',
			'title' => 'Perfmatters',
			'href'  => admin_url('options-general.php?page=perfmatters')
		));
	}
}