<?php

if ( empty( $atts ) ) {
	return;
}

if ( ! empty( $atts['font_size'] ) ) {
	$unit = trim( preg_replace( '/[0-9.]/', '', $atts['font_size'] ) );
	if ( ! $unit ) {
		$atts['font_size'] .= 'px';
	}
	echo '.single-product .woocommerce-product-rating .star-rating {font-size:' . esc_html( $atts['font_size'] ) . '}';
}
if ( ! empty( $atts['bgcolor'] ) ) {
	echo '.single-product .woocommerce-product-rating .star-rating:before {color:' . esc_html( $atts['bgcolor'] ) . '}';
}
if ( ! empty( $atts['color'] ) ) {
	echo '.single-product .woocommerce-product-rating .star-rating span:before {color:' . esc_html( $atts['color'] ) . '}';
}
