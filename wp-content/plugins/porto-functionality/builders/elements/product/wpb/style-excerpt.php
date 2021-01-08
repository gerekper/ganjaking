<?php
if ( empty( $atts ) ) {
	return;
}
$shortcode_css = '';
if ( $atts['font_size'] ) {
	$unit = trim( preg_replace( '/[0-9.]/', '', $atts['font_size'] ) );
	if ( ! $unit ) {
		$atts['font_size'] .= 'px';
	}
	$shortcode_css .= 'font-size:' . esc_html( $atts['font_size'] ) . ';';
}
if ( ! empty( $atts['font_weight'] ) ) {
	$shortcode_css .= 'font-weight:' . esc_html( $atts['font_weight'] ) . ';';
}
if ( ! empty( $atts['line_height'] ) ) {
	$unit = trim( preg_replace( '/[0-9.]/', '', $atts['line_height'] ) );
	if ( ! $unit && (int) $atts['line_height'] > 3 ) {
		$atts['line_height'] .= 'px';
	}
	$shortcode_css .= 'line-height:' . esc_attr( $atts['line_height'] ) . ';';
}
if ( ! empty( $atts['ls'] ) ) {
	$unit = trim( preg_replace( '/[0-9.-]/', '', $atts['ls'] ) );
	if ( ! $unit ) {
		$atts['ls'] .= 'px';
	}
	$shortcode_css .= 'letter-spacing:' . esc_html( $atts['ls'] ) . ';';
}
if ( ! empty( $atts['color'] ) ) {
	$shortcode_css .= 'color:' . esc_html( $atts['color'] );
}
if ( $shortcode_css ) {
	echo '.woocommerce-product-details__short-description p, .single-product .product-summary-wrap .description p {' . $shortcode_css . '}';
}
