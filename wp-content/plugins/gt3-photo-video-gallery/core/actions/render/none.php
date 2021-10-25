<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	} // Exit if accessed directly

	add_filter('gt3pg_before_render_link', function ($link, $atts){
		return in_array( $atts['link'], array(
			'none',
		) ) ? $atts['link'] : $link;

	}, 10, 2 );

