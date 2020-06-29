<?php
/**
 * Single Product Sale Flash
 *
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $product, $porto_settings;

$labels = '';
if ( $porto_settings['product-hot'] ) {
	$featured = $product->is_featured();
	if ( $featured ) {
		$labels .= '<div class="onhot">' . ( ( isset( $porto_settings['product-hot-label'] ) && $porto_settings['product-hot-label'] ) ? esc_html( $porto_settings['product-hot-label'] ) : esc_html__( 'Hot', 'porto' ) ) . '</div>';
	}
}

if ( $porto_settings['product-sale'] && $product->is_on_sale() ) {
	$percentage = 0;
	if ( $product->get_regular_price() ) {
		$percentage = - round( ( ( $product->get_regular_price() - $product->get_sale_price() ) / $product->get_regular_price() ) * 100 );
	}
	if ( $porto_settings['product-sale-percent'] && $percentage ) {
		$labels .= '<div class="onsale">' . $percentage . '%</div>';
	} else {
		$labels .= apply_filters( 'woocommerce_sale_flash', '<span class="onsale">' . ( ( isset( $porto_settings['product-sale-label'] ) && $porto_settings['product-sale-label'] ) ? esc_html( $porto_settings['product-sale-label'] ) : esc_html__( 'Sale', 'porto' ) ) . '</span>', $post, $product );
	}
}
echo '<div class="labels">';
echo porto_filter_output( $labels );
echo '</div>';

