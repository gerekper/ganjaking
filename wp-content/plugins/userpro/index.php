<?php
/*
Plugin Name: UserPro
Plugin URI: http://codecanyon.net/user/DeluxeThemes/portfolio?ref=DeluxeThemes
Description: The ultimate user profiles and community plugin for WordPress.
Version: 4.9.38
Author: Deluxe Themes
Author URI: http://codecanyon.net/user/DeluxeThemes/portfolio?ref=DeluxeThemes
*/
update_option( 'userpro_trial', 0 );
update_option( 'userpro_activated', 1 );
// Define UserPro files.
if ( ! defined( 'UP_PLUGIN_FILE' ) ) {
    define( 'UP_PLUGIN_FILE', __FILE__ );
}

// Include the main UserPro file
if ( ! class_exists( 'UserPro' ) ) {
    include_once dirname( __FILE__ ) . '/includes/class-userpro.php';
}

function userpro_init()
{
    return UserPro::instance();
}

userpro_init();

$GLOBALS['userpro'] = new userpro_api();

