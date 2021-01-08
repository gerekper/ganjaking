<?php

extract(
	shortcode_atts(
		array(
			'action' => '',
		),
		$atts
	)
);

if ( $action ) {
	do_action( $action );
}
