<?php
$output = $delegate = $type = $gallery = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(
	shortcode_atts(
		array(
			'delegate'           => 'a',
			'type'               => 'image',
			'gallery'            => true,
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

$output = '<div class="porto-lightbox-container"';
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

$options                       = array();
$options['delegate']           = $delegate;
$options['type']               = $type;
$options['gallery']            = array();
$options['gallery']['enabled'] = $gallery;
$options                       = json_encode( $options );

$output .= '<div class="lightbox ' . esc_attr( $el_class ) . '" data-plugin-options="' . esc_attr( $options ) . '">';
$output .= do_shortcode( $content );
$output .= '</div>';

$output .= '</div>';

echo porto_filter_output( $output );
