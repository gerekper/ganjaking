<?php
/*
Plugin Name: Slider Revolution Whiteboard Add-on
Plugin URI: https://www.themepunch.com/
Description: Create Hand-Drawn Presentations that are understandable, memorable & engaging
Author: ThemePunch
Version: 2.2.3
Author URI: https://themepunch.com
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WHITEBOARD_PLUGIN_URL', str_replace('index.php','',plugins_url( 'index.php', __FILE__ )));
define( 'WHITEBOARD_PLUGIN_PATH', plugin_dir_path(__FILE__) );
define( 'WHITEBOARD_FILE_PATH', __FILE__ );
define( 'WHITEBOARD_VERSION', '2.2.3');


function rs_whiteboard_init(){
	
	require_once plugin_dir_path( __FILE__ ) . 'includes/verify-addon.php';
	
	$verify = new Revslider_Whiteboard_Addon_Verify();
	if($verify->is_verified()) {
		
		require_once(WHITEBOARD_PLUGIN_PATH.'includes/base.class.php');
		$wb_base = new rs_whiteboard_base();
		
	}
	
}

//build js global var for activation
add_filter( 'revslider_activate_addon', array('rs_whiteboard_base','get_data'),10,2);

// get help definitions on-demand.  merges AddOn definitions with core revslider definitions
add_filter( 'revslider_help_directory', array('rs_whiteboard_base','get_help'),10,1);

add_action('plugins_loaded', 'rs_whiteboard_init');
register_activation_hook( __FILE__, 'rs_whiteboard_init');

?>