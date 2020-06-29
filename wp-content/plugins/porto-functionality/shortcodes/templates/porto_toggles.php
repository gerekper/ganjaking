<?php
$output = $type = $size = $one_toggle = $color = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(
	shortcode_atts(
		array(
			'type'               => '',
			'size'               => false,
			'one_toggle'         => false,
			'skin'               => 'custom',
			'color'              => '',
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

if ( $type ) {
	$el_class .= ' ' . $type;
}

if ( $size ) {
	$el_class .= ' ' . $size;
}

if ( 'custom' == $skin && $color ) {
	$sc_class  = 'toggles' . rand();
	$el_class .= ' ' . $sc_class;
	$output   .= '<style>';
	if ( 'toggle-simple' == $type ) {
		$output .= '.' . $sc_class . '.toggle-simple .toggle > label:after { background-color: ' . $color . ' }';
	} else {
		$output .= '.' . $sc_class . ' .toggle label { border-left-color: ' . $color . '; border-right-color: ' . $color . '; color: ' . $color . ' }';
		$output .= '.' . $sc_class . ' .toggle.active > label { background-color: ' . $color . '; border-color: ' . $color . '; color: #ffffff }';
		$output .= '.' . $sc_class . ' .toggle > label:after { background-color: ' . $color . ' }';
	}
	$output .= '</style>';
}

if ( 'custom' != $skin ) {
	$el_class .= ' toggle-' . $skin;
}

$output .= '<div class="porto-toggles wpb_content_element ' . esc_attr( $el_class ) . '"';
if ( $animation_type ) {
	$output .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
	if ( $animation_delay ) {
		$output .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
	}
	if ( $animation_duration && 1000 != $animation_duration ) {
		$output .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
	}
}
$output .= ( $one_toggle ? ' data-view="one-toggle"' : '' ) . '>';

$output .= do_shortcode( $content );

$output .= '</div>';

echo porto_filter_output( $output );
