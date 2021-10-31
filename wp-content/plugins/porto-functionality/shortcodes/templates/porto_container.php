<?php
$output = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(
	shortcode_atts(
		array(
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'is_half'            => '',
			'is_full_md'         => '',
			'el_class'           => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

$cls = '';
if ( ! empty( $is_half ) ) {
	$cls = 'col-half-section half-container';
	if ( ! empty( $is_full_md ) ) {
		$cls .= ' col-fullwidth-md';
	}
} else {
	$cls = 'porto-container container';
}
if ( $animation_type ) {
	$cls .= ' appear-animation';
}
if ( $el_class ) {
	$cls .= ' ' . esc_attr( trim( $el_class ) );
}


$output = '<div class="' . $cls . '"';
if ( $animation_type ) {
	$output .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
	if ( $animation_delay ) {
		$output .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
	}
	if ( $animation_duration && 1000 != $animation_duration ) {
		$output .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
	}
}
$output .= '>';

$output .= do_shortcode( $content );

$output .= '</div>';

echo porto_filter_output( $output );
