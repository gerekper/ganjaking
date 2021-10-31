<?php

$output = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(
	shortcode_atts(
		array(
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
			'mouse_parallax'         => 'no',
			'mouse_parallax_inverse' => 'no',
			'mouse_parallax_speed'   => '0.5',
		),
		$atts
	)
);
// Mouse Parallax
$mpx_opts      = array();
$mpx_attr_html = '';
if ( 'yes' == $mouse_parallax ) {
	if ( 'yes' == $mouse_parallax_inverse ) {
		$mpx_opts['invertX'] = true;
		$mpx_opts['invertY'] = true;
	} else {
		$mpx_opts['invertX'] = false;
		$mpx_opts['invertY'] = false;
	}

	wp_enqueue_script( 'jquery-parallax' );
	$mpx_opts = array(
		'data-plugin'         => 'mouse-parallax',
		'data-options'        => json_encode( $mpx_opts ),
		'data-floating-depth' => empty( $mouse_parallax_speed ) ? 0.5 : floatval( $mouse_parallax_speed ),
	);

	foreach ( $mpx_opts as $key => $value ) {
		if ( 'data-options' == $key ) {
			$value = "'" . $value . "'";
		} else {
			$value = '"' . $value . '"';
		}
		$mpx_attr_html .= $key . '=' . $value . ' ';
	}
}

$el_class = porto_shortcode_extract_class( $el_class );

$output = '<div class="porto-animation' . esc_attr( $el_class ) . '" ' . $mpx_attr_html;
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
if ( 'yes' == $mouse_parallax ) {
	$output .= '<div class="layer">';
}

$output .= do_shortcode( $content );

if ( 'yes' == $mouse_parallax ) {
	$output .= '</div>';
}

$output .= '</div>';

echo porto_filter_output( $output );
