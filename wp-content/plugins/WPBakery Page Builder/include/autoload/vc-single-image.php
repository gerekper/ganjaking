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

function vc_single_image_src() {
	vc_user_access()->checkAdminNonce()->validateDie()->wpAny( 'edit_posts', 'edit_pages' )->validateDie();

	$image_id = (int) vc_post_param( 'content' );
	$params = vc_post_param( 'params' );
	$post_id = (int) vc_post_param( 'post_id' );
	$img_size = vc_post_param( 'size' );
	$img = '';

	if ( ! empty( $params['source'] ) ) {
		$source = $params['source'];
	} else {
		$source = 'media_library';
	}

	switch ( $source ) {
		case 'media_library':
		case 'featured_image':
			if ( 'featured_image' === $source ) {
				if ( $post_id && has_post_thumbnail( $post_id ) ) {
					$img_id = get_post_thumbnail_id( $post_id );
				} else {
					$img_id = 0;
				}
			} else {
				$img_id = preg_replace( '/[^\d]/', '', $image_id );
			}

			if ( ! $img_size ) {
				$img_size = 'thumbnail';
			}

			if ( $img_id ) {
				$img = wp_get_attachment_image_src( $img_id, $img_size );
				if ( $img ) {
					$img = $img[0];
				}
			}

			break;

		case 'external_link':
			if ( ! empty( $params['custom_src'] ) ) {
				$img = $params['custom_src'];
			}
			break;
	}

	echo esc_url( $img );
	die();
}
