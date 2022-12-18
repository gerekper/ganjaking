<?php

global $product;
if ( empty( $product ) ) {
	return;
}

$wrap_cls = 'tb-woo-desc';
if ( ! empty( $atts['el_class'] ) && wp_is_json_request() ) {
	$wrap_cls .= ' ' . trim( $atts['el_class'] );
}
if ( ! empty( $atts['className'] ) ) {
	$wrap_cls .= ' ' . trim( $atts['className'] );
}

echo '<div class="' . esc_attr( apply_filters( 'porto_elements_wrap_css_class', $wrap_cls, $atts, 'porto-tb/porto-woo-desc' ) ) . '">';

if ( function_exists( 'wc_get_template_html' ) && is_object( $product ) ) {
	echo wc_get_template_html( 'single-product/short-description.php' );
}
echo '</div>';
