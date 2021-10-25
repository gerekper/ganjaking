<?php

if(!defined('ABSPATH')) {
	exit;
}
if (!function_exists('gt3pg_maybe_shorten_edd_filename')) {
	add_filter('upgrader_pre_download', 'gt3pg_maybe_shorten_edd_filename', 10, 4);

	function gt3pg_maybe_shorten_edd_filename($return, $package){
		if(strpos($package, '/edd-sl/package_download/') !== false) {
			add_filter('wp_unique_filename', 'gt3pg_shorten_edd_filename', 10, 2);
		}

		return $return;
	}

	function gt3pg_shorten_edd_filename($filename, $ext){
		$filename = md5($filename).$ext;
		remove_filter('wp_unique_filename', 'gt3pg_shorten_edd_filename', 10);

		return $filename;
	}
}

