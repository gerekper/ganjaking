<?php
/**
 * Compare related products template
 *
 * @author YITH <plugins@yithemes.com>
 * @package YITH\Compare
 * @version 1.0.0
 */

defined( 'YITH_WOOCOMPARE' ) || exit; // Exit if accessed directly.

global $product;

/**
 * APPLY_FILTERS: yith_woocompare_css_class_for_related_products_wrapper
 *
 * Filters the CSS class for the container of the related products section in the comparison table.
 *
 * @param string $css_class CSS class.
 *
 * @return string
 */
$css_class_for_related_products_wrapper = apply_filters( 'yith_woocompare_css_class_for_related_products_wrapper', 'related-products' );

?>
<div id="yith-woocompare-related" class="woocommerce yith-woocompare-related" data-iframe="<?php echo esc_attr( $iframe ); ?>">
	<h3 class="yith-woocompare-related-title"><?php echo esc_html( $related_title ); ?></h3>
	<div class="yith-woocompare-related-wrapper">
		<ul class="<?php echo $css_class_for_related_products_wrapper; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
			<?php
			foreach ( $products as $product_id ) :
				$product = wc_get_product( $product_id );
				?>
				<li class="related-product">
					<?php
					/**
					 * DO_ACTION: yith_woocompare_before_single_related_product
					 *
					 * Allows to render some content before the single product in the related products section in the comparison table.
					 */
					do_action( 'yith_woocompare_before_single_related_product' );
					?>
					<a href="<?php echo esc_url( $product->get_permalink() ); ?>" target="_parent">
						<div class="product-image">
							<?php
							wc_get_template( 'loop/sale-flash.php' );
							echo $product->get_image( 'shop_catalog' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
						</div>

						<h3 class="product-title"><?php echo esc_html( $product->get_title() ); ?></h3>

						<div class="product-price">
							<?php echo wp_kses_post( $product->get_price_html() ); ?>
						</div>
					</a>

					<div class="woocommerce add-to-cart">
						<?php woocommerce_template_loop_add_to_cart(); ?>
					</div>

					<?php echo do_shortcode( '[yith_compare_button product="' . $product_id . ' type="button"]' ); ?>

					<?php
					/**
					 * DO_ACTION: yith_woocompare_after_single_related_product
					 *
					 * Allows to render some content after the single product in the related products section in the comparison table.
					 */
					do_action( 'yith_woocompare_after_single_related_product' );
					?>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if ( count( $products ) >= get_option( 'yith-woocompare-related-visible-num', 4 ) && 'yes' === get_option( 'yith-woocompare-related-navigation', 'yes' ) ) : ?>
			<div class="related-slider-nav">
				<div class="related-slider-nav-prev"></div>
				<div class="related-slider-nav-next"></div>
			</div>
		<?php endif ?>

	</div>
</div>
