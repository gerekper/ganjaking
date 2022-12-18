<?php

global $product;
if ( empty( $product ) ) {
	return;
}

$wrap_cls = 'tb-woo-stock';
if ( ! empty( $atts['el_class'] ) && wp_is_json_request() ) {
	$wrap_cls .= ' ' . trim( $atts['el_class'] );
}
if ( ! empty( $atts['className'] ) ) {
	$wrap_cls .= ' ' . trim( $atts['className'] );
}

echo '<div class="' . esc_attr( apply_filters( 'porto_elements_wrap_css_class', $wrap_cls, $atts, 'porto-tb/porto-woo-stock' ) ) . '">';

if ( function_exists( 'wc_get_stock_html' ) ) {
	if ( $product->is_type( 'simple' ) ) {
		remove_filter( 'woocommerce_get_stock_html', 'porto_woocommerce_stock_html', 10, 2 );
	}

	echo wc_get_stock_html( $product );

	if ( $product->is_type( 'simple' ) ) {
		add_filter( 'woocommerce_get_stock_html', 'porto_woocommerce_stock_html', 10, 2 );
	}
}
echo '</div>';