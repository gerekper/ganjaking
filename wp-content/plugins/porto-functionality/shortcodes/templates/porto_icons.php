<?php

$align = $el_class = '';
extract(
	shortcode_atts(
		array(
			'align'     => '',
			'el_class'  => '',
			'css_icon'  => '',
			'className' => '',
		),
		$atts
	)
);

$icon_design_css = '';
if ( defined( 'VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG' ) ) {
	$icon_design_css = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css_icon, ' ' ), 'porto_icons', $atts );
}

if ( $className ) {
	if ( $el_class ) {
		$el_class .= ' ' . $className;
	} else {
		$el_class = $className;
	}
}

$classes = 'porto-u-icons';
if ( $icon_design_css ) {
	$classes .= ' ' . esc_attr( $icon_design_css );
}
if ( $align ) {
	$classes .= ' ' . esc_attr( $align );
}
if ( $el_class ) {
	$classes .= ' ' . esc_attr( $el_class );
}
$output  = '<div class="' . $classes . '">';
$output .= do_shortcode( $content );
$output .= '</div>';

echo porto_filter_output( $output );
