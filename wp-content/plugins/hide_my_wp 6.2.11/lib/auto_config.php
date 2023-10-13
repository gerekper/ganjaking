<?php

$prefix ='';
if (is_multisite() && $this->is_subdir_mu)
    $prefix =  $this->blog_path;



/* Reference:

private $post_replace_old=array();
private $post_replace_new=array();
private $post_preg_replace_new=array();
private $post_preg_replace_old=array();

private $top_replace_old=array();
private $top_replace_new=array();

private $replace_old=array();
private $replace_new=array();

private $preg_replace_old=array();
private $preg_replace_new=array();
$auto_config_replace_urls = array(); //strings with ==
private $auto_config_inline_css ='';
private $auto_config_inline_js = '';*/



//Gravity Form
if (class_exists( 'GFForms') && $this->opt('new_plugin_path')){
    include_once ('auto_config/gravity-forms.php');
}


if (class_exists( 'Jetpack') && $this->opt('new_plugin_path')){
    include_once('auto_config/jetpack.php');
}

//order ext etx in init (and load if class)
if (class_exists( 'WooCommerce') && $this->opt('new_plugin_path')){
    include_once ('auto_config/woo-commerce.php');
}

if (class_exists( 'Elementor\Plugin') && $this->opt('new_plugin_path')){
    include_once ('auto_config/elmntr.php');
}

if (class_exists('WPBakeryShortCode')) {
	$this->top_replace_old[]='<meta name="generator" content="Powered by Visual Composer - drag and drop page builder for WordPress."/>';
	$this->top_replace_new[]="";
}

//Yoast SEO
if (defined('WPSEO_VERSION')) {
	$this->preg_replace_old[]='|<!-- This site is optimized with the Yoast SEO plugin .*? -->|';
	$this->preg_replace_new[]="";

	$this->top_replace_old[]='<!-- / Yoast SEO plugin. -->';
    $this->top_replace_new[]="";
}

//remove the REST API HTTP headers
remove_action('wp_head', 'rest_output_link_header', 10);
remove_action('template_redirect', 'rest_output_link_header', 11);
remove_action('wp_head', 'rest_output_link_wp_head');


function hmwp_wprocket_hide($string) {
	$string .= " "; //appending a space helps! - Vikas
	return $string;
}


if (defined('WP_ROCKET_VERSION')) {
	//called when wp rocket is trying to see if wp rocket is white labelled or not
	//this works at - wp-rocket/inc/functions/files.php @ get_rocket_footprint
	add_filter( 'get_rocket_option_wl_author', 'hmwp_wprocket_hide');
	
	remove_filter('rocket_buffer', 'rocket_wpengine_add_footprint', 50);
}