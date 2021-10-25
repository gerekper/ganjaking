<?php
if(!defined('ABSPATH')) {
	exit;
}


add_action('wp_enqueue_scripts', function(){
	//wp_enqueue_style('gt3-core-frontend', plugin_dir_url(__FILE__).'css/frontend.css', array(),GT3_CORE_ELEMENTOR_VERSION);
	//wp_enqueue_script('gt3-core-frontend', plugin_dir_url(__FILE__).'js/frontend.js', array('jquery'),GT3_CORE_ELEMENTOR_VERSION,true);
});

add_action('admin_enqueue_scripts', function(){
	wp_enqueue_style('gt3-core-admin', plugin_dir_url(__FILE__).'css/admin.css', array(), GT3_CORE_ELEMENTOR_VERSION);
	wp_enqueue_script('gt3-core-admin', plugin_dir_url(__FILE__).'js/admin.js', array('jquery'),GT3_CORE_ELEMENTOR_VERSION,true);
});