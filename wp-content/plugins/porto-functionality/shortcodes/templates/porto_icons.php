<?php

$align = $el_class = '';
extract(
	shortcode_atts(
		array(
			'align'        => '',
			'hover_effect' => '',
			'el_class'     => '',
			'css_icon'     => '',
			'className'    => '',
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
	$classes .= ' ' . $icon_design_css;
}
if ( $align ) {
	$classes .= ' ' . $align;
}
if ( $hover_effect ) {
	$classes .= ' has-effect ' . $hover_effect;
}
if ( $el_class ) {
	$classes .= ' ' . $el_class;
}
$output  = '<div class="' . esc_attr( $classes ) . '">';
$output .= do_shortcode( $content );
$output .= '</div>';

echo porto_filter_output( $output );
