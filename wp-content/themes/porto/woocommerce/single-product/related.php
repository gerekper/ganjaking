<?php
/**
 * Related Products
 *
 * @version     3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product, $porto_settings, $porto_woocommerce_loop, $porto_product_layout;

if ( empty( $product ) || ! $product->exists() ) {
	return;
}
$related = wc_get_related_products( $product->get_id(), $porto_settings['product-related-count'] );
if ( sizeof( $related ) === 0 || ! $porto_settings['product-related'] ) {
	return;
}

$args = apply_filters(
	'woocommerce_related_products_args',
	array(
		'post_type'           => 'product',
		'ignore_sticky_posts' => 1,
		'no_found_rows'       => 1,
		'posts_per_page'      => $porto_settings['product-related-count'],
		'orderby'             => $orderby,
		'post__in'            => $related,
		'post__not_in'        => array( $product->get_id() ),
	)
);

$products = new WP_Query( $args );

$porto_woocommerce_loop['columns'] = isset( $porto_settings['product-related-cols'] ) ? $porto_settings['product-related-cols'] : $porto_settings['product-cols'];

if ( ! $porto_woocommerce_loop['columns'] ) {
	$porto_woocommerce_loop['columns'] = 4;
}

if ( 'left_sidebar' == $porto_product_layout ) {
	$container_class = '';
} elseif ( porto_is_wide_layout() ) {
	$container_class = 'container-fluid';
} else {
	$container_class = 'container';
}

if ( $products->have_posts() ) : ?>
	<div class="related products">
		<div class="<?php echo esc_attr( $container_class ); ?>">
			<?php
				$heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'Related products', 'woocommerce' ) );

			if ( $heading ) :
				?>
				<h2 class="slider-title"><?php echo esc_html( $heading ); ?></h2>
			<?php endif; ?>

			<div class="slider-wrapper">

				<?php
				$porto_woocommerce_loop['view']       = 'products-slider';
				$porto_woocommerce_loop['navigation'] = false;
				$porto_woocommerce_loop['pagination'] = true;
				$porto_woocommerce_loop['el_class']   = 'show-dots-title-right';

				woocommerce_product_loop_start();
				?>

				<?php
				while ( $products->have_posts() ) :
					$products->the_post();
					?>

					<?php wc_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

				<?php
				woocommerce_product_loop_end();
				?>
			</div>
		</div>
	</div>
	<?php
endif;

wp_reset_postdata();
