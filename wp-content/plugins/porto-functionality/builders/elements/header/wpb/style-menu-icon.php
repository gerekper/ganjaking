<?php

if ( empty( $atts ) ) {
	return;
}

if ( ! empty( $atts['size'] ) || ! empty( $atts['bg_color'] ) || ! empty( $atts['color'] ) ) {
	echo '#header .mobile-toggle {';
	if ( ! empty( $atts['size'] ) ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $atts['size'] ) );
		if ( ! $unit ) {
			$atts['size'] .= 'px';
		}
		echo 'font-size:' . esc_html( $atts['size'] ) . ';';
	}
	if ( ! empty( $atts['bg_color'] ) ) {
		echo 'background-color:' . esc_html( $atts['bg_color'] ) . ';';
	}
	if ( ! empty( $atts['color'] ) ) {
		echo 'color:' . esc_html( $atts['color'] );
	}
	echo '}';
}
