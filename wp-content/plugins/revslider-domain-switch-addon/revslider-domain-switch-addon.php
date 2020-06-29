<?php
/*
Plugin Name: Slider Revolution Domain Switch Add-on
Plugin URI: https://www.themepunch.com/
Description: Switch Image URLs in Sliders from an old to a new Domain
Author: ThemePunch
Version: 1.0.1
Author URI: https://themepunch.com
*/

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'RS_DOMAIN_SWITCH_PLUGIN_URL', str_replace('index.php','',plugins_url( 'index.php', __FILE__ )));
define( 'RS_DOMAIN_SWITCH_PLUGIN_PATH', plugin_dir_path(__FILE__) );
define( 'RS_DOMAIN_SWITCH_FILE_PATH', __FILE__ );
define( 'RS_DOMAIN_SWITCH_VERSION', '1.0.1');


function rs_domain_switch_init(){
	
	require_once plugin_dir_path( __FILE__ ) . 'includes/verify-addon.php';
	
	$verify = new Revslider_Domain_Switch_Addon_Verify();
	if($verify->is_verified()) {
		require_once(RS_DOMAIN_SWITCH_PLUGIN_PATH.'includes/base.class.php');
		$wb_base = new rs_domain_switch_base();
	}
	
}

//build js global var for activation

add_action('plugins_loaded', 'rs_domain_switch_init');
register_activation_hook( __FILE__, 'rs_domain_switch_init');

?>