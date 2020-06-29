<?php
$output = $container = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(
	shortcode_atts(
		array(
			'container'          => false,
			'customize'          => false,
			'image'              => '',
			'gap'                => 164,
			'min_height'         => 400,
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

$output = '<div class="porto-map-section ' . esc_attr( $el_class ) . '"';
if ( $animation_type ) {
	$output .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
	if ( $animation_delay ) {
		$output .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
	}
	if ( $animation_duration && 1000 != $animation_duration ) {
		$output .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
	}
}

if ( $customize ) {
	$img_id  = preg_replace( '/[^\d]/', '', $image );
	$img_url = wp_get_attachment_url( $img_id );
	$img_url = str_replace( array( 'http:', 'https:' ), '', $img_url );
	$gap     = (int) $gap;
	$output .= ' style="background-image:url(' . esc_url( str_replace( array( 'http:', 'https:' ), '', $img_url ) ) . ');' . ( 164 != $gap ? 'padding-top:' . esc_attr( $gap ) . 'px' : '' ) . '"';
}

$output .= '>';

$output .= '<section class="map-content"' . ( $customize && 400 != $min_height ? ' style="min-height:' . esc_attr( $min_height ) . 'px"' : '' ) . '>';
if ( $container ) {
	$output .= '<div class="container">';
}
$output .= do_shortcode( $content );
if ( $container ) {
	$output .= '</div>';
}
$output .= '</section>';
$output .= '</div>';

echo porto_filter_output( $output );
