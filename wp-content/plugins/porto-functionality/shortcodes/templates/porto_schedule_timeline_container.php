<?php

$result = $title = $subtitle = $title_color = $subtitle_color = $el_class = '';

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
			'step_item_list' => '',
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

		$result             .= '<div class="timeline-circle ' . $circle_type_classes . ' center m-b-lg ' . $el_class . ' ">';
			$result         .= '<div class="circle-dotted">';
				$result     .= '<div class="circle-center">';
					$result .= '<span' . ( $title_color ? ' style="color:' . esc_attr( $title_color ) . ' !important"' : '' ) . ' class="step-title text-color-' . $text_color . ' font-weight-bold m-b-none">' . $title . '</span><span' . ( $subtitle_color ? ' style="color:' . esc_attr( $subtitle_color ) . ' !important"' : '' ) . ' class="step-subtitle text-color-' . $text_color . '">' . $subtitle . '</span>';
				$result     .= '</div>';
			$result         .= '</div>';
		$result             .= '</div>';
	}

	ob_start();
	include porto_shortcode_template( 'porto_step_item' );
	$result .= ob_get_clean();
} elseif ( 'history' == $type ) {
	global $porto_schedule_timeline_count;
	$porto_schedule_timeline_count = 0;
	$result                       .= '<section class="timeline">';
		$result                   .= '<div class="timeline-body">';
		ob_start();
		include porto_shortcode_template( 'porto_step_item' );
		$result                   .= ob_get_clean();
		$result                   .= '</div>';
	$result                       .= '</section>';
	$porto_schedule_timeline_count = '';
	unset( $GLOBALS['porto_schedule_timeline_count'] );
} elseif ( 'step' == $type ) {
	$el_class = porto_shortcode_extract_class( $el_class );
	global $porto_schedule_step_count;
	$porto_schedule_step_count = 0;
	$result                   .= '<div class="porto-process' . ( $is_horizontal ? ' process-horizontal' : '' ) . ( $el_class ? esc_attr( $el_class ) : '' ) . '">';
	ob_start();
	include porto_shortcode_template( 'porto_step_item' );
	$result                   .= ob_get_clean();
	$result                   .= '</div>';
	$porto_schedule_step_count = '';
	unset( $GLOBALS['porto_schedule_step_count'] );
}

echo porto_filter_output( $result );
