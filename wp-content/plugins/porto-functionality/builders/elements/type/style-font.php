<?php

if ( ! empty( $settings['textAlign'] ) || ! empty( $settings['fontFamily'] ) || ! empty( $settings['fontSize'] ) || ! empty( $settings['fontWeight'] ) || ! empty( $settings['textTransform'] ) || ! empty( $settings['lineHeight'] ) || ! empty( $settings['letterSpacing'] ) || ! empty( $settings['color'] ) ) {
	echo ( empty( $settings['color'] ) ? '' : '.page-wrapper ' ) . porto_filter_output( $settings['selector'] ) . '{';
	if ( ! empty( $settings['fontFamily'] ) ) {
		echo 'font-family:' . esc_html( $settings['fontFamily'] ) . ';';
	}
	if ( ! empty( $settings['fontSize'] ) ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $settings['fontSize'] ) );
		if ( ! $unit ) {
			$settings['fontSize'] .= 'px';
		}
		echo 'font-size:' . esc_html( $settings['fontSize'] ) . ';';
	}
	if ( ! empty( $settings['fontWeight'] ) ) {
		echo 'font-weight:' . esc_html( $settings['fontWeight'] ) . ';';
	}
	if ( ! empty( $settings['textTransform'] ) ) {
		echo 'text-transform:' . esc_html( $settings['textTransform'] ) . ';';
	}
	if ( ! empty( $settings['lineHeight'] ) ) {
		$unit = trim( preg_replace( '/[0-9.]/', '', $settings['lineHeight'] ) );
		if ( ! $unit && (int) $settings['lineHeight'] > 3 ) {
			$settings['lineHeight'] .= 'px';
		}
		echo 'line-height:' . esc_attr( $settings['lineHeight'] ) . ';';
	}
	if ( ! empty( $settings['letterSpacing'] ) ) {
		$unit = trim( preg_replace( '/[0-9.-]/', '', $settings['letterSpacing'] ) );
		if ( ! $unit ) {
			$settings['letterSpacing'] .= 'px';
		}
		echo 'letter-spacing:' . esc_html( $settings['letterSpacing'] ) . ';';
	}
	if ( ! empty( $settings['textAlign'] ) ) {
		echo 'text-align:' . esc_html( $settings['textAlign'] ) . ';';
	}
	if ( ! empty( $settings['color'] ) ) {
		echo 'color:' . esc_html( $settings['color'] );
	}
	echo '}';
}