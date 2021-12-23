<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

add_action( 'plugins_loaded', 'wpb_init_vendor_rank_math', 16 );
function wpb_init_vendor_rank_math() {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); // Require class-vc-wxr-parser-plugin.php to use is_plugin_active() below
	if ( is_plugin_active( 'seo-by-rank-math/rank-math.php' ) || class_exists( 'RankMath' ) ) {
		add_action( 'vc_backend_editor_render', 'wpb_enqueue_rank_math_assets' );
	}
}

function wpb_enqueue_rank_math_assets() {
	wp_enqueue_script( 'vc_vendor_seo_js', vc_asset_url( 'js/vendors/seo.js' ), array(
		'jquery-core',
		'underscore',
	), WPB_VC_VERSION, true );
}
