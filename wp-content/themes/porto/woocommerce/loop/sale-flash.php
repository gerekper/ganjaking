<?php
/**
 * Product loop sale flash
 *
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post, $product, $porto_settings;

$labels = '';

// display hot label
if ( ! empty( $porto_settings['product-hot'] ) || ( ! empty( $porto_settings['product-labels'] ) && in_array( 'hot', $porto_settings['product-labels'] ) ) ) {
	$featured = $product->is_featured();
	if ( $featured ) {
		$hot_html = '<div class="onhot">' . ( ( isset( $porto_settings['product-hot-label'] ) && $porto_settings['product-hot-label'] ) ? esc_html( $porto_settings['product-hot-label'] ) : __( 'Hot', 'porto' ) ) . '</div>';
		$labels  .= $hot_html;
	}
}

// display sale label
if ( ! empty( $porto_settings['product-sale'] ) || ( ! empty( $porto_settings['product-labels'] ) && in_array( 'sale', $porto_settings['product-labels'] ) ) ) {
	if ( $product->is_on_sale() ) {
		$percentage = 0;
		$reg_p      = floatval( $product->get_regular_price() );
		if ( $reg_p ) {
			$percentage = - round( ( ( $reg_p - $product->get_sale_price() ) / $reg_p ) * 100 );
		} elseif ( 'variable' == $product->get_type() && $product->get_variation_regular_price() ) {
			$percentage = - round( ( ( $product->get_variation_regular_price() - $product->get_variation_sale_price() ) / $product->get_variation_regular_price() ) * 100 );
		}
		if ( $porto_settings['product-sale-percent'] && $percentage ) {
			$sales_html = '<div class="onsale">' . $percentage . '%</div>';
		} else {
			$sales_html = apply_filters( 'woocommerce_sale_flash', '<div class="onsale">' . ( ( isset( $porto_settings['product-sale-label'] ) && $porto_settings['product-sale-label'] ) ? esc_html( $porto_settings['product-sale-label'] ) : esc_html__( 'Sale', 'porto' ) ) . '</div>', $post, $product );
		}
		$labels .= $sales_html;
	}
}

// display new label
$new_period = empty( $porto_settings['product-new-days'] ) ? 7 : (int) $porto_settings['product-new-days'];
if ( ! empty( $porto_settings['product-labels'] ) && in_array( 'new', $porto_settings['product-labels'] ) && strtotime( $product->get_date_created() ) > strtotime( '-' . $new_period . ' day' ) ) {
	$labels .= '<label class="onnew">' . ( empty( $porto_settings['product-new-label'] ) ? esc_html__( 'New', 'porto' ) : esc_html( $porto_settings['product-new-label'] ) ) . '</label>';
}

if ( $labels ) {
	echo '<div class="labels">' . porto_filter_output( $labels ) . '</div>';
}

$availability = $product->get_availability();
if ( 'out-of-stock' == $availability['class'] ) {
	if ( $porto_settings['product-stock'] ) {
		echo apply_filters( 'woocommerce_stock_html', '<div class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</div>', $availability['availability'] );
	}
} else {
	if ( empty( $porto_settings['add-to-cart-notification'] ) ) {
		echo '<div data-link="' . esc_url( get_permalink( wc_get_page_id( 'cart' ) ) ) . '" class="viewcart' . ' viewcart-' . $product->get_id() . '" title="' . esc_attr__( 'View cart', 'woocommerce' ) . '"></div>';
	}
}
