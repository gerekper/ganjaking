<?php
global $post, $product, $woocommerce_loop;

if ( empty( $label ) ) {
	$label = __( 'Customers also viewed these products', 'wc_recommender' );
	$label = get_option( 'wc_recommender_label_rbpv', $label );
}

$product_to_compare = isset( $product_to_compare ) ? $product_to_compare : false;

if ( empty( $product_to_compare ) && empty( $product ) ) {
	_doing_it_wrong(
		__FUNCTION__,
		__( 'Shortcode called without a product to compare outside of a single product page.', 'wc_recommender' ),
		'3.2.3'
	);

	return;
}

$product_to_compare = empty($product_to_compare) ? $product->get_id() : $product_to_compare;

$simularity_scores = woocommerce_recommender_get_simularity( $product_to_compare, $activity_types );
$related           = array();

if ( $simularity_scores ) {
	$related = array_keys( $simularity_scores );
}

if ( sizeof( $related ) == 0 ) {
	return;
}

$args = apply_filters( 'woocommerce_related_products_args', array(
	'post_type'           => 'product',
	'ignore_sticky_posts' => 1,
	'no_found_rows'       => 1,
	'posts_per_page'      => - 1,
	'orderby'             => $orderby,
	'post__in'            => $related,
) );


$woocommerce_loop['columns'] = $columns;

$products = get_posts( $args );

if ( $products && is_array( $products ) && count( $products ) ) :
	woocommerce_recommender_sort_also_viewed( $products, $simularity_scores );

	if ( $posts_per_page ) {
		$parts    = array_chunk( $products, $posts_per_page );
		$products = $parts[0];
	}
	?>

    <div style="clear:both;"></div>
    <div class="related products">

		<?php echo apply_filters( 'woocommerce_recommendation_engine_label_also_viewed', '<h2>' . $label . '</h2>' ); ?>

		<?php woocommerce_product_loop_start(); ?>
		<?php
		foreach ( $products as $post ) :
			setup_postdata( $post );
			?>
			<?php wc_get_template_part( 'content', 'product' ); ?>
		<?php endforeach; // end of the loop.
		?>
		<?php wp_reset_postdata(); ?>
		<?php woocommerce_product_loop_end(); ?>

    </div>
    <div style="clear:both;"></div>
<?php
endif;
wp_reset_postdata();
