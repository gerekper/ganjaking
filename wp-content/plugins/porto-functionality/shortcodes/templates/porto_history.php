<?php
$output = $image_url = $image = $year = $history = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(
	shortcode_atts(
		array(
			'image_url'          => '',
			'image'              => '',
			'year'               => '',
			'history'            => '',
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

$output = '<div class="porto-history wpb_content_element ' . $el_class . '"';
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

if ( ! $image_url && $image ) {
	$img_id = preg_replace( '/[^\d]/', '', $image );
	$img    = porto_shortcode_get_image_by_size(
		array(
			'attach_id'  => $img_id,
			'thumb_size' => '145x145',
		)
	);
	if ( $img ) {
		$output .= '<div class="thumb">' . $img['thumbnail'] . '</div>';
	}
} elseif ( $image_url ) {
	$image_url = str_replace( array( 'http:', 'https:' ), '', $image_url );
	$output   .= '<div class="thumb"><img alt="' . esc_attr( $year ) . '" src="' . esc_url( $image_url ) . '"></div>';
}

$output .= '<div class="featured-box"><div class="box-content">';
if ( $year ) {
	$output .= '<h4 class="heading-primary"><strong>' . $year . '</strong></h4>';
}
$output .= function_exists( 'wpb_js_remove_wpautop' ) ? wpb_js_remove_wpautop( $content ? $content : $history, true ) : do_shortcode( $content ? $content : $history );
$output .= '</div></div>';
$output .= '</div>';

echo porto_filter_output( $output );
