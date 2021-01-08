<?php

if ( empty( $atts ) ) {
	return;
}

$shortcode_css = '';
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
if ( ! empty( $atts['color'] ) ) {
	$shortcode_css .= 'color:' . esc_html( $atts['color'] );
}
if ( $shortcode_css ) {
	echo '.single-product-price .price {' . $shortcode_css . '}';
}
