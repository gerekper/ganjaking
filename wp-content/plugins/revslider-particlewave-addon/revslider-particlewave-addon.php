<?php
/*
Plugin Name: Slider Revolution Particle Wave Add-On
Plugin URI: http://www.themepunch.com/
Description: Add Awesome Particle Wave Effects to your Layers
Author: ThemePunch
Version: 1.1.0
Author URI: http://themepunch.com
*/

/*

SCRIPT HANDLES:
	
	'rs-particlewave-admin'
	'rs-particlewave-front'

*/

// If this file is called directly, abort.
if(!defined('WPINC')) die;

define('RS_PARTICLEWAVE_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('RS_PARTICLEWAVE_PLUGIN_URL', str_replace('index.php', '', plugins_url( 'index.php', __FILE__)));

require_once(RS_PARTICLEWAVE_PLUGIN_PATH . 'includes/base.class.php');

/**
* handle everything by calling the following function *
**/
function rs_particlewave_init(){

	new RsParticleWaveBase();
	
}

/**
* call all needed functions on plugins loaded *
**/
add_action('plugins_loaded', 'rs_particlewave_init');
register_activation_hook( __FILE__, 'rs_particlewave_init');

//build js global var for activation
add_filter( 'revslider_activate_addon', array('RsAddOnParticleWaveBase','get_data'),10,2);

// get help definitions on-demand.  merges AddOn definitions with core revslider definitions
add_filter( 'revslider_help_directory', array('RsAddOnParticleWaveBase','get_help'),10,1);

?>