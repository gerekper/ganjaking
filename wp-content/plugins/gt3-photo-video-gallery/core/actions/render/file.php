<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	} // Exit if accessed directly
	add_filter( 'gt3pg_render_image_output_file', function ( $return, $id, $atts, $img_alt, $media_class, $media_url, $attachment ) {

		$return = gt3_el::Create( 'a' )
		                ->addAttrs( array( 'href' => wp_get_attachment_image_url( $id, $atts['size'] ) ) )
		                ->addClasses( array( 'gt3pg_swipebox', $media_class ) )
		                ->addData( 'description', $attachment->post_content )
		                ->addContent( $return );

		return $return;

	}, 10, 7 );

	add_filter( 'gt3pg_before_render_link', function ( $link, $atts ) {
		return in_array( $atts['link'], array(
			'file',
		) ) ? $atts['link'] : $link;

	}, 10, 2 );

