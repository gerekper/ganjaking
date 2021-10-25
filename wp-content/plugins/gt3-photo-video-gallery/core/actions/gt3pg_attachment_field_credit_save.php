<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	} // Exit if accessed directly

	function gt3pg_attachment_field_credit_save( $post, $attachment ) {
		if ( isset( $attachment['gt3-video-url'] ) ) {
			update_post_meta( $post['ID'], 'gt3_video_url', $attachment['gt3-video-url'] );
		}
		if ( isset( $attachment['gt3-external-link-url'] ) ) {
			update_post_meta( $post['ID'], 'gt3_external_link_url', $attachment['gt3-external-link-url'] );
		}

		return $post;
	}
