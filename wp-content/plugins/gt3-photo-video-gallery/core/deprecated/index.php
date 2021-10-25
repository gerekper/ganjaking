<?php

defined('ABSPATH') OR exit;

if(!function_exists('get_plugins')) {
	require_once ABSPATH.'wp-admin/includes/plugin.php';
}
$plugins  = get_plugins();
$pro_slug = 'gt3-photo-video-gallery-pro/gt3-photo-video-gallery-pro.php';
if(key_exists($pro_slug, $plugins)) {
	$pro = $plugins[$pro_slug];
	if(version_compare($pro['Version'], '1.7.0.0', '<')) {
		require_once __DIR__.'/GT3_EDD_SL_Plugin_Updater.php';
		require_once __DIR__.'/gt3pg_updater.php';
		require_once __DIR__.'/notice.php';
	}
}

