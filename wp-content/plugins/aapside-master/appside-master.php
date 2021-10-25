<?php
/*
Plugin Name: Aapside Master
Plugin URI: https://themeforest.net/user/ir-tech/portfolio
Description: Plugin to contain short codes, custom post types, Elementor Widgets, Custom Widget and more of the Aapside theme.
Author: Ir-Tech
Author URI:https://themeforest.net/user/ir-tech
Version: 2.0.4
Text Domain: appside-master
*/

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
//Check appside active or not
$theme_name_array   = array( 'Aapside', 'Aapside Child' );
$current_theme      = wp_get_theme();
$current_theme_name = $current_theme->get( 'Name' );
define( 'APPSIDE_THEME_ACTIVE', in_array( $current_theme_name, $theme_name_array ) ? true : false );

//plugin dir path
define( 'APPSIDE_MASTER_ENV', true );
define( 'APPSIDE_MASTER_ROOT_PATH', plugin_dir_path( __FILE__ ) );
define( 'APPSIDE_MASTER_ROOT_URL', plugin_dir_url( __FILE__ ) );
define( 'APPSIDE_MASTER_SELF_PATH', 'appside-master/appside-master.php' );
define( 'APPSIDE_MASTER_VERSION', '2.0.2' );
define( 'APPSIDE_MASTER_INC', APPSIDE_MASTER_ROOT_PATH .'/inc');
define( 'APPSIDE_MASTER_LIB', APPSIDE_MASTER_ROOT_PATH .'/lib');
define( 'APPSIDE_MASTER_ELEMENTOR', APPSIDE_MASTER_ROOT_PATH .'/elementor');
define( 'APPSIDE_MASTER_DEMO_IMPORT', APPSIDE_MASTER_ROOT_PATH .'/demo-data-import');
define( 'APPSIDE_MASTER_ADMIN', APPSIDE_MASTER_ROOT_PATH .'/admin');
define( 'APPSIDE_MASTER_ADMIN_ASSETS', APPSIDE_MASTER_ROOT_URL .'admin/assets');
define( 'APPSIDE_MASTER_WP_WIDGETS', APPSIDE_MASTER_ROOT_PATH .'/wp-widgets');
define( 'APPSIDE_MASTER_ASSETS', APPSIDE_MASTER_ROOT_URL .'assets/');
define( 'APPSIDE_MASTER_CSS', APPSIDE_MASTER_ASSETS .'css');
define( 'APPSIDE_MASTER_JS', APPSIDE_MASTER_ASSETS .'js');
define( 'APPSIDE_MASTER_IMG', APPSIDE_MASTER_ASSETS .'img');


if (file_exists( APPSIDE_MASTER_INC .'/class-appside-master-helper-functions.php')){
    require_once APPSIDE_MASTER_INC . '/class-appside-master-helper-functions.php';
    if (!function_exists('appside_master')){
        function appside_master(){
            return class_exists('Appside_Master_Helper_Functions') ? new Appside_Master_Helper_Functions() : false;
        }
    }
}

//load codester framework functions
if ( !APPSIDE_THEME_ACTIVE) {
	if ( file_exists( APPSIDE_MASTER_ROOT_PATH . '/inc/csf-functions.php' ) ) {
		require_once APPSIDE_MASTER_ROOT_PATH . '/inc/csf-functions.php';
	}
}

//plugin init
if ( file_exists( APPSIDE_MASTER_ROOT_PATH . '/inc/class-appside-master-init.php' ) ) {
	require_once APPSIDE_MASTER_ROOT_PATH . '/inc/class-appside-master-init.php';
}
