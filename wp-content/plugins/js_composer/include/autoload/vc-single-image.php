<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

if ( 'vc_edit_form' === vc_post_param( 'action' ) ) {
	add_filter( 'vc_edit_form_fields_attributes_vc_single_image', 'vc_single_image_convert_old_link_to_new' );
}
/**
 * Backward compatibility
 *
 * @param $atts
 * @return mixed
 * @since 4.6
 */
function vc_single_image_convert_old_link_to_new( $atts ) {
	if ( empty( $atts['onclick'] ) && isset( $atts['img_link_large'] ) && 'yes' === $atts['img_link_large'] ) {
		$atts['onclick'] = 'img_link_large';
		unset( $atts['img_link_large'] );
	} elseif ( empty( $atts['onclick'] ) && ( ! isset( $atts['img_link_large'] ) || 'yes' !== $atts['img_link_large'] ) ) {
		unset( $atts['img_link_large'] );
	}

	if ( empty( $atts['onclick'] ) && ! empty( $atts['link'] ) ) {
		$atts['onclick'] = 'custom_link';
	}

	return $atts;
}

add_action( 'wp_ajax_wpb_single_image_src', 'vc_single_image_src' );

/**
 * Get Single Image source URL.
 *
 * @return string
 */
function vc_single_image_src() {
	vc_user_access()->checkAdminNonce()->validateDie()->wpAny( 'edit_posts', 'edit_pages' )->validateDie();

	$image_id = (int) vc_post_param( 'content' );
	$params = vc_post_param( 'params' );
	$post_id = (int) vc_post_param( 'post_id' );
	$img_size = vc_post_param( 'size' );

	if ( ! empty( $params['source'] ) ) {
		$source = $params['source'];
	} else {
		$source = 'media_library';
	}

	$image_data = wpb_get_image_data_by_source( $source, $post_id, $image_id, $img_size );

	echo esc_url( $image_data['image_src'] );
	die();
}

add_action( 'wp_ajax_wpb_single_image_data', 'vc_single_image_data' );

/**
 * Get single image data  (source URL, alt attribute).
 *
 * @since 7.4
 */
function vc_single_image_data() {
	vc_user_access()->checkAdminNonce()->validateDie()->wpAny( 'edit_posts', 'edit_pages' )->validateDie();

	$image_id = (int) vc_post_param( 'content' );
	$params = vc_post_param( 'params' );
	$post_id = (int) vc_post_param( 'post_id' );
	$img_size = vc_post_param( 'size' );

	if ( ! empty( $params['source'] ) ) {
		$source = $params['source'];
	} else {
		$source = 'media_library';
	}

	$image_data = wpb_get_image_data_by_source( $source, $post_id, $image_id, $img_size );
	wp_send_json_success( $image_data );
}
