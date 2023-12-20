<?php

if ( defined( 'DOING_AJAX' ) ) {
	add_action( 'wp_ajax_seedprod_pro_get_site_logo', 'seedprod_pro_get_site_logo' );
}

/**
 * Return Site Logo HTML
 */
function seedprod_pro_get_site_logo() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		echo do_shortcode( "[seedprod tag='the_custom_logo']" );
		exit;
	}
}
