<?php

$output = $el_class = '';

extract(
	shortcode_atts(
		array(
			'el_class' => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

$output         .= '<section class="timeline exp-timeline" id="exp-timeline">';
	$output     .= '<div class="timeline-body">';
		$output .= do_shortcode( $content );
		$output .= '<div class="timeline-bar"></div>';
	$output     .= '</div>';
$output         .= '</section>';

echo porto_filter_output( $output );
