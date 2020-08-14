<?php
/**
 * Popup related products template
 *
 * @author  YITH
 * @package YITH WooCommerce Added to Cart Popup
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WACP' ) ) {
	exit; // Exit if accessed directly.
}

global $product, $woocommerce_loop;

$loop = 0;
$args = apply_filters(
	'yith_wacp_related_products_args',
	array(
		'post_type'           => array( 'product', 'product_variation' ),
		'post_status'         => 'publish',
		'ignore_sticky_posts' => 1,
		'no_found_rows'       => 1,
		'posts_per_page'      => $posts_per_page,
		'post__in'            => $items,
		'post__not_in'        => array( $current_product_id ),
		'orderby'             => 'rand',
	)
);

$products = new WP_Query( $args );

if ( $products->have_posts() ) : ?>

	<div class="woocommmerce yith-wacp-related">

		<h3><?php echo esc_html( $title ); ?></h3>

		<ul class="products columns-<?php echo esc_attr( $columns ); ?>">

			<?php

			// Extra post classes.
			$classes = array( 'yith-wacp-related-product' );
			// Set columns.
			$woocommerce_loop['loop']    = 0;
			$woocommerce_loop['columns'] = $columns;

			while ( $products->have_posts() ) :
				$products->the_post();
				?>

				<li <?php post_class( $classes ); ?>>

					<?php do_action( 'yith_wacp_before_related_item' ); ?>

					<a href="<?php the_permalink(); ?>">

						<div class="product-image">
							<?php
							wc_get_template( 'loop/sale-flash.php' );
							$image_size = apply_filters( 'yith_wacp_suggested_product_image_size', 'shop_catalog' );
							echo woocommerce_get_product_thumbnail( $image_size ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
						</div>

						<h3 class="product-title">
							<?php the_title(); ?>
						</h3>

						<div class="product-price">
							<?php wc_get_template( 'loop/price.php' ); ?>
						</div>

						<?php
						if ( $show_add_to_cart ) {
							echo do_shortcode( '[add_to_cart id="' . get_the_ID() . '" style="" show_price="false"]' );
						}
						?>

					</a>

					<?php do_action( 'yith_wacp_after_related_item' ); ?>

				</li>

			<?php endwhile; // end of the loop. ?>

		</ul>

	</div>

	<?php
endif;

wp_reset_postdata();
