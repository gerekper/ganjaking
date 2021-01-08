<?php

if ( empty( $atts ) || empty( $atts['type'] ) ) {
	return;
}

$selector = 'currency-switcher' == $atts['type'] ? '#header .currency-switcher > li.menu-item > a' : '#header .view-switcher > li.menu-item > a';
if ( ! empty( $atts['font_size'] ) || ! empty( $atts['font_weight'] ) || ! empty( $atts['text_transform'] ) || ! empty( $atts['line_height'] ) || ! empty( $atts['letter_spacing'] ) || ! empty( $atts['color'] ) ) {
	echo porto_filter_output( $selector ) . '{';
	if ( ! empty( $atts['font_size'] ) ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $atts['font_size'] ) );
		if ( ! $unit ) {
			$atts['font_size'] .= 'px';
		}
		echo 'font-size:' . esc_html( $atts['font_size'] ) . ';';
	}
	if ( ! empty( $atts['font_weight'] ) ) {
		echo 'font-weight:' . esc_html( $atts['font_weight'] ) . ';';
	}
	if ( ! empty( $atts['text_transform'] ) ) {
		echo 'text-transform:' . esc_html( $atts['text_transform'] ) . ';';
	}
	if ( ! empty( $atts['line_height'] ) ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $atts['line_height'] ) );
		if ( ! $unit && (int) $atts['line_height'] > 3 ) {
			$atts['line_height'] .= 'px';
		}
		echo 'line-height:' . esc_attr( $atts['line_height'] ) . ';';
	}
	if ( ! empty( $atts['letter_spacing'] ) ) {
		$unit = trim( preg_replace( '/[0-9.-]/', '', $atts['letter_spacing'] ) );
		if ( ! $unit ) {
			$atts['letter_spacing'] .= 'px';
		}
		echo 'letter-spacing:' . esc_html( $atts['letter_spacing'] ) . ';';
	}
	if ( ! empty( $atts['color'] ) ) {
		echo 'color:' . esc_html( $atts['color'] );
	}
	echo '}';
}

if ( ! empty( $atts['hover_color'] ) ) {
	$selector = 'currency-switcher' == $atts['type'] ? '#header .currency-switcher > li.menu-item:hover > a' : '#header .view-switcher > li.menu-item:hover > a';
	echo porto_filter_output( $selector ) . '{color:' . esc_html( $atts['hover_color'] ) . '}';
}
