<?php

$output = $title = $subtitle = $title_color = $subtitle_color = $el_class = '';

extract(
	shortcode_atts(
		array(
			'type'           => 'schedule',
			'is_horizontal'  => '',
			'title'          => '',
			'subtitle'       => '',
			'circle_type'    => '',
			'title_color'    => '',
			'subtitle_color' => '',
			'el_class'       => '',
		),
		$atts
	)
);

if ( 'schedule' == $type ) {
	if ( $title || $subtitle ) {

		$el_class = porto_shortcode_extract_class( $el_class );

		if ( 'simple' == $circle_type ) {
			$circle_type_classes = 'background-color-light circle-light text-color-dark';
		} else {
			$circle_type_classes = 'background-color-primary border-transparent no-box-shadow text-color-light';
		}

		$text_color = 'simple' == $circle_type ? 'dark' : 'light';

		$output             .= '<div class="timeline-circle ' . $circle_type_classes . ' center m-b-lg ' . $el_class . ' ">';
			$output         .= '<div class="circle-dotted">';
				$output     .= '<div class="circle-center">';
					$output .= '<span' . ( $title_color ? ' style="color:' . esc_attr( $title_color ) . ' !important"' : '' ) . ' class="text-color-' . $text_color . ' font-weight-bold m-b-none">' . $title . '</span><span' . ( $subtitle_color ? ' style="color:' . esc_attr( $subtitle_color ) . ' !important"' : '' ) . ' class="text-color-' . $text_color . '">' . $subtitle . '</span>';
				$output     .= '</div>';
			$output         .= '</div>';
		$output             .= '</div>';
	}

	$output .= do_shortcode( $content );
} elseif ( 'history' == $type ) {
	global $porto_schedule_timeline_count;
	$porto_schedule_timeline_count = 0;
	$output                       .= '<section class="timeline">';
		$output                   .= '<div class="timeline-body">';
			$output               .= do_shortcode( $content );
		$output                   .= '</div>';
	$output                       .= '</section>';
	$porto_schedule_timeline_count = '';
	unset( $GLOBALS['porto_schedule_timeline_count'] );
} elseif ( 'step' == $type ) {
	$el_class = porto_shortcode_extract_class( $el_class );
	global $porto_schedule_step_count;
	$porto_schedule_step_count = 0;
	$output .= '<div class="porto-process' . ( $is_horizontal ? ' process-horizontal' : '' ) . ( $el_class ? esc_attr( $el_class ) : '' ) . '">';
	$output .= do_shortcode( $content );
	$output .= '</div>';
	$porto_schedule_step_count = '';
	unset( $GLOBALS['porto_schedule_step_count'] );
}

echo porto_filter_output( $output );
