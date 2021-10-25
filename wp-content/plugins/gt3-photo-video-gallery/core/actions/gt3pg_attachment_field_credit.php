<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	} // Exit if accessed directly

	function gt3pg_attachment_field_credit( $form_fields, $post ) {
		if (strpos($post->post_mime_type,'image') === false) return $form_fields;

		$helps                        = apply_filters( 'gt3pg_attachment_field_helps', __( 'Lite version supports only Youtube and Vimeo' ), $post );
		$form_fields['gt3-video-url'] = array(
			'label' => __( 'Video Url', 'gt3pg' ),
			'input' => 'text',
			'value' => get_post_meta( $post->ID, 'gt3_video_url', true ),
			'helps' => $helps
		);
		$form_fields['gt3-external-link-url'] = array(
			'label' => __( 'External Link', 'gt3pg' ),
			'input' => 'text',
			'value' => get_post_meta( $post->ID, 'gt3_external_link_url', true ),
		);

		return $form_fields;
	}
