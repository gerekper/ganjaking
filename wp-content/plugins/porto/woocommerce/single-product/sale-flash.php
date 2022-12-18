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
if ( ! empty( $porto_settings['product-hot'] ) || ( ! empty( $porto_settings['product-labels'] ) && in_array( 'hot', $porto_settings['product-labels'] ) ) ) {
	$featured = $product->is_featured();
	if ( $featured ) {
		$labels .= '<div class="onhot">' . ( ( isset( $porto_settings['product-hot-label'] ) && $porto_settings['product-hot-label'] ) ? esc_html( $porto_settings['product-hot-label'] ) : esc_html__( 'Hot', 'porto' ) ) . '</div>';
	}
}

if ( ( ! empty( $porto_settings['product-sale'] ) || ( ! empty( $porto_settings['product-labels'] ) && in_array( 'sale', $porto_settings['product-labels'] ) ) ) && $product->is_on_sale() ) {
	$percentage = 0;
	$reg_p      = floatval( $product->get_regular_price() );
	if ( $reg_p ) {
		$percentage = - round( ( ( $reg_p - $product->get_sale_price() ) / $reg_p ) * 100 );
	}
	if ( $porto_settings['product-sale-percent'] && $percentage ) {
		$labels .= '<div class="onsale">' . $percentage . '%</div>';
	} else {
		$labels .= apply_filters( 'woocommerce_sale_flash', '<span class="onsale">' . ( ( isset( $porto_settings['product-sale-label'] ) && $porto_settings['product-sale-label'] ) ? esc_html( $porto_settings['product-sale-label'] ) : esc_html__( 'Sale', 'porto' ) ) . '</span>', $post, $product );
	}
}

// display new label
$new_period = empty( $porto_settings['product-new-days'] ) ? 7 : (int) $porto_settings['product-new-days'];
if ( ! empty( $porto_settings['product-labels'] ) && in_array( 'new', $porto_settings['product-labels'] ) && strtotime( $product->get_date_created() ) > strtotime( '-' . $new_period . ' day' ) ) {
	$labels .= '<label class="onnew">' . ( empty( $porto_settings['product-new-label'] ) ? esc_html__( 'New', 'porto' ) : esc_html( $porto_settings['product-new-label'] ) ) . '</label>';
}

echo '<div class="labels">';
echo porto_filter_output( $labels );
echo '</div>';

