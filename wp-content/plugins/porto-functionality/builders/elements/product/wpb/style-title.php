<?php

if ( empty( $atts ) ) {
	return;
}

$shortcode_css = '';
if ( ! empty( $atts['font_family'] ) ) {
	$shortcode_css .= 'font-family:' . esc_html( $atts['font_family'] ) . ';';
}
if ( ! empty( $atts['font_size'] ) ) {
	$unit = trim( preg_replace( '/[0-9.]/', '', $atts['font_size'] ) );
	if ( ! $unit ) {
		$atts['font_size'] .= 'px';
	}
	$shortcode_css .= 'font-size:' . esc_html( $atts['font_size'] ) . ';';
}
if ( ! empty( $atts['font_weight'] ) ) {
	$shortcode_css .= 'font-weight:' . esc_html( $atts['font_weight'] ) . ';';
}
if ( ! empty( $atts['text_transform'] ) ) {
	$shortcode_css .= 'text-transform:' . esc_html( $atts['text_transform'] ) . ';';
}
if ( ! empty( $atts['line_height'] ) ) {
	$unit = trim( preg_replace( '/[0-9.]/', '', $atts['line_height'] ) );
	if ( ! $unit && $atts['line_height'] > 3 ) {
		$atts['line_height'] .= 'px';
	}
	$shortcode_css .= 'line-height:' . esc_html( $atts['line_height'] ) . ';';
}
if ( ! empty( $atts['letter_spacing'] ) ) {
	$shortcode_css .= 'letter-spacing:' . esc_html( $atts['letter_spacing'] ) . ';';
}
if ( ! empty( $atts['color'] ) ) {
	$shortcode_css .= 'color:' . esc_html( $atts['color'] );
}
if ( $shortcode_css ) {
	echo '.single-product .product_title {' . $shortcode_css . '}';
}
