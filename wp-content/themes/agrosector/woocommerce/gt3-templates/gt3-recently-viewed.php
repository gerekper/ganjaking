<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$viewed_products = ! empty( $_COOKIE['gt3_product_recently_viewed'] ) ? (array) explode( '|', $_COOKIE['gt3_product_recently_viewed'] ) : array();
$viewed_products = array_reverse( array_filter( array_map( 'absint', $viewed_products ) ) );

if ( empty($viewed_products) ) return;

add_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_single_excerpt', 5);

$orderby = '';
$columns = wc_get_loop_prop( 'columns' );
$viewed_products_orderby = gt3_option('viewed_products_orderby');
$orderby = $viewed_products_orderby == '1' ? 'rand' : 'post__in';

$query_args = array(
	'posts_per_page' => $columns,
	'no_found_rows'  => 1,
	'post_status'    => 'publish',
	'post_type'      => 'product',
	'post__in'       => $viewed_products,
	'orderby'        => $orderby,
	'tax_query'      => array(array(
		'taxonomy' => 'product_visibility',
		'field'    => 'name',
		'terms'    => array( 'exclude-from-catalog'),
		'operator' => 'NOT IN',
	)),

);
if ( 'yes' === get_option( 'woocommerce_hide_out_of_stock_items' ) ) {
	$query_args['tax_query'][0]['terms'][] = 'outofstock';
}
wc_set_loop_prop( 'loop', 0 );
$query_products = new WP_Query( $query_args );

if ( $query_products->have_posts() ) {
	echo '<div class="gt3-products-additional-area">';
		echo '<h4>'.esc_html__('Recently Viewed', 'agrosector').'</h4>';
		echo '<a onclick="gt3_clear_recently_products(this)" href="javascript:void(0);" class="clear_recently_products">'.esc_html__('Clear All', 'agrosector').'</a>';
		echo '<ul class="products columns-'.esc_attr($columns).' ywcps-products gt3-products-additional-area">';

		while ( $query_products->have_posts() ) {
			$query_products->the_post();
			wc_get_template_part( 'content', 'product' );
		}

		echo '</ul>';
	echo '</div><!-- gt3-products-additional-area -->';
}

woocommerce_reset_loop();
wp_reset_postdata();