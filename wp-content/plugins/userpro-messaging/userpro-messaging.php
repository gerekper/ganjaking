<?php
/*
Plugin Name: Private Messages Add-on for UserPro
Plugin URI: http://codecanyon.net/user/DeluxeThemes/portfolio?ref=DeluxeThemes
Description: Allow users to send a message to each other, view/manage messages, and block users.
Version: 4.9.2
Author: Deluxe Themes
Author URI: http://codecanyon.net/user/DeluxeThemes/portfolio?ref=DeluxeThemes
*/

define('userpro_msg_url',plugin_dir_url(__FILE__ ));
define('userpro_msg_path',plugin_dir_path(__FILE__ ));
require_once userpro_msg_path . 'functions/shortcode-pop-message.php';
	/* init */
	function userpro_msg_init() {
		load_plugin_textdomain('userpro-msg', false, dirname(plugin_basename(__FILE__)) . '/languages');
	}
	add_action('init', 'userpro_msg_init');
	
	if (!function_exists('autolink')) {
		require_once userpro_msg_path . 'lib/autolink.php';
	}

	/* functions */
	foreach (glob(userpro_msg_path . 'functions/*.php') as $filename) { require_once $filename; }

	/* administration */
    
	if (is_admin()){
		foreach (glob(userpro_msg_path . 'admin/*.php') as $filename) { include $filename; }
		//new WPUpdatesPluginUpdater_1031( 'http://wp-updates.com/api/2/plugin', plugin_basename(__FILE__));
	}
    
