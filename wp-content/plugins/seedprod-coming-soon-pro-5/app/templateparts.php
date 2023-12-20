<?php

/**
 * Get Theme Template Parts
 *
 * @return array Template part names.
 */
function seedprod_pro_get_template_parts() {

	global $wpdb;

	$tablename      = $wpdb->prefix . 'posts';
	$meta_tablename = $wpdb->prefix . 'postmeta';

	$sql  = "SELECT * FROM $tablename p LEFT JOIN $meta_tablename pm ON (pm.post_id = p.ID)";
	$sql .= ' WHERE 1 = 1 AND post_type = "seedprod" AND meta_key = "_seedprod_page_template_type" AND (meta_value = "part" || meta_value = "header" || meta_value = "footer")';
	$sql .= ' AND  (post_status = "publish" || post_status = "draft") ';

	$landing_parts = $wpdb->get_results( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

	return ! empty( $landing_parts ) ? $landing_parts : array();

}
add_action( 'wp_ajax_seedprod_pro_get_template_parts_result', 'seedprod_pro_get_template_parts_result' );


/**
 * Get Theme Template Parts
 *
 * @return void
 */
function seedprod_pro_get_template_parts_result() {

	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		$id = absint( filter_input( INPUT_GET, 'parts_id' ) );

		if ( empty( $id ) ) {
			echo '';
			wp_die();
		}

		$post = get_post( $id );
		if ( 'seedprod' === $post->post_type ) {
			// Do NOT use get_the_content, breaks system
			echo do_shortcode( $post->post_content );
		}
		wp_die();
	}
}
