<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

add_action( 'wp', function() {
	if ( vc_mode() !== 'page' || ! is_singular() ) {
		return;
	}

	require_once vc_path_dir( 'CORE_DIR', 'class-vc-seo.php' );

	$vc_seo = new Vc_Seo();
	$vc_seo->set_plugin_seo_post_meta();
	if ( ! $vc_seo->post_seo_meta ) {
		return;
	}

	add_filter( 'wp_title', [ $vc_seo, 'filter_title' ], 15 );
	add_filter( 'pre_get_document_title', [ $vc_seo, 'filter_title' ], 15 );
	add_filter( 'wp_head', [ $vc_seo, 'add_seo_head' ], 15 );
});

add_action( 'wp_ajax_wpb_seo_check_key_phrase', function () {
	require_once vc_path_dir( 'CORE_DIR', 'class-vc-seo.php' );

	$vc_seo = new Vc_Seo();
	$is_key_phrase_in_other_posts = $vc_seo->check_key_phrase_in_other_posts();

	wp_send_json_success( $is_key_phrase_in_other_posts );
} );
