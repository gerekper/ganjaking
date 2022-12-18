<?php
/**
 * Single Product Up-Sells
 *
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product, $porto_settings;

$upsells = $product->get_upsell_ids();

if ( sizeof( $upsells ) === 0 || empty( $porto_settings['product-upsells'] ) ) {
	return;
}

$meta_query = WC()->query->get_meta_query();

$args = array(
	'post_type'           => 'product',
	'ignore_sticky_posts' => 1,
	'no_found_rows'       => 1,
	'posts_per_page'      => $porto_settings['product-upsells-count'],
	'orderby'             => $orderby,
	'post__in'            => $upsells,
	'post__not_in'        => array( $product->get_id() ),
	'meta_query'          => $meta_query,
);

$products = new WP_Query( $args );

if ( $products->have_posts() ) :
	global $porto_woocommerce_loop;

	$porto_woocommerce_loop['columns'] = isset( $porto_settings['product-upsells-cols'] ) ? $porto_settings['product-upsells-cols'] : ( isset( $porto_settings['product-cols'] ) ? $porto_settings['product-cols'] : 3 );

	if ( ! $porto_woocommerce_loop['columns'] ) {
		$porto_woocommerce_loop['columns'] = 4;
	}
	?>

	<div class="upsells products">

		<h2 class="slider-title"><span class="inline-title"><?php esc_html_e( 'You may also like&hellip;', 'woocommerce' ); ?></span><span class="line"></span></h2>

		<div class="slider-wrapper">

			<?php

			$porto_woocommerce_loop['view'] = 'products-slider';

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

	<?php
endif;

wp_reset_postdata();
