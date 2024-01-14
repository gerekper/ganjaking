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
		add_action('wp', array('Perfmatters\Config', 'queue'));
	}

	//setup admin bar menu
	public static function admin_bar_menu(WP_Admin_Bar $wp_admin_bar) 
	{

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

	//run the queue
	public static function queue() {

		//inital checks
        if(is_admin() || perfmatters_is_dynamic_request() || perfmatters_is_page_builder() || isset($_GET['perfmatters']) || isset($_GET['perfmattersoff'])) {
            return;
        }

        //logged in check
        if(!empty(self::$tools['disable_logged_in']) && is_user_logged_in()) {
            return;
        }

        //user agent check
        if(!empty($_SERVER['HTTP_USER_AGENT'])) {
            $excluded_agents = array(
                'usercentrics'
            );
            foreach($excluded_agents as $agent) {
                if(stripos($_SERVER['HTTP_USER_AGENT'], $agent) !== false) {
                    return;
                }
            }
        }

		do_action('perfmatters_queue');
	}
}