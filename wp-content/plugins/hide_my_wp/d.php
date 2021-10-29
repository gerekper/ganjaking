<?php
$token = isset($_GET['token']) ? $_GET['token'] : false;

// exit;
define( 'WP_USE_THEMES', false ); 
require( '../../../wp-load.php' );
require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// echo get_option('hmwp_reset_token');
if($token == false || $token != get_option('hmwp_reset_token')) {
	// die('');
	exit;
}


// update_option('hmwp_temp_admin_path', 'wp-admin');
// $previous['new_admin_path'] = trim($this->opt('new_admin_path'), ' /');
// update_option(self::slug, $previous);
$slug = 'hide_my_wp';

$opts = (array)get_option($slug);
$opts['new_admin_path'] = 'wp-admin';
update_option($slug, $opts);

deactivate_plugins( 'hide_my_wp/hide-my-wp.php' );

echo "Hide my is deactivated now, log back in normally";