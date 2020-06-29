<?php
/*
Plugin Name: BeTheme Header Builder
Description: Header Builder for BeTheme - The biggest WordPress Theme ever.
Author: Muffin group
Author URI: https://muffingroup.com
Version: 1.0.5
*/

if (! defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

define('MFN_HB_VERSION', '1.0.5');

if (is_admin()) {

	// backend

	require_once dirname(__FILE__) .'/admin/class-mfn-hb-admin.php';

	function mfn_hb_admin()
	{
		$Mfn_HB_Admin = new Mfn_HB_Admin();
	}
	add_action('init', 'mfn_hb_admin');

} else {

	// frontend

	require_once dirname(__FILE__) .'/functions/class-mfn-hb-front.php';

	function mfn_hb_front()
	{
		$Mfn_HB_Front = new Mfn_HB_Front();
	}
	add_action('init', 'mfn_hb_front');
}
