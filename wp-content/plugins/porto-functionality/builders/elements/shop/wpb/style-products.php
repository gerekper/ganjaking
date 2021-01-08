<?php
if ( empty( $atts ) ) {
	return;
}
$shortcode_css = '';
if ( ( ! isset( $atts['title_use_theme_fonts'] ) || 'yes' !== $atts['title_use_theme_fonts'] ) && ! empty( $atts['title_google_font'] ) ) {
	$google_fonts_data = porto_sc_parse_google_font( $atts['title_google_font'] );
	$styles            = porto_sc_google_font_styles( $google_fonts_data );
	$shortcode_css    .= esc_attr( $shortcode_css );
}
if ( $atts['title_font_size'] ) {
	$unit = trim( preg_replace( '/[0-9.]/', '', $atts['title_font_size'] ) );
	if ( ! $unit ) {
		$atts['title_font_size'] .= 'px';
	}
	$shortcode_css .= 'font-size:' . esc_html( $atts['title_font_size'] ) . ';';
}
if ( ! empty( $atts['title_font_weight'] ) ) {
	$shortcode_css .= 'font-weight:' . esc_html( $atts['title_font_weight'] ) . ';';
}
if ( ! empty( $atts['title_line_height'] ) ) {
	$unit = trim( preg_replace( '/[0-9.]/', '', $atts['title_line_height'] ) );
	if ( ! $unit && (int) $atts['title_line_height'] > 3 ) {
		$atts['title_line_height'] .= 'px';
	}
	$shortcode_css .= 'line-height:' . esc_attr( $atts['title_line_height'] ) . ';';
}
if ( ! empty( $atts['title_ls'] ) ) {
	$unit = trim( preg_replace( '/[0-9.-]/', '', $atts['title_ls'] ) );
	if ( ! $unit ) {
		$atts['title_ls'] .= 'px';
	}
	$shortcode_css .= 'letter-spacing:' . esc_html( $atts['title_ls'] ) . ';';
}
if ( ! empty( $atts['title_color'] ) ) {
	$shortcode_css .= 'color:' . esc_html( $atts['title_color'] );
}
if ( $shortcode_css ) {
	echo 'div.archive-products li.product-col h3 {' . $shortcode_css . '}';
}
