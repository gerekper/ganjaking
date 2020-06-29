<?php
/**
 * shortcode template
 *
 * @author  YITH
 * @package YITH WooCommerce Frequently Bought Together Premium
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WFBT' ) ) {
	exit;
} // Exit if accessed directly

if ( empty( $args['products'] ) ) {
	return;
}

// enqueue style
wp_enqueue_style( 'yith-wfbt-carousel-style' );
wp_enqueue_script( 'yith-wfbt-carousel-js' );
wp_enqueue_style( 'yith-wfbt-style' );

$title = get_option( 'yith-wfbt-slider-title', __( 'Customers who bought the items in your wishlist also purchased', 'yith-woocommerce-frequently-bought-together' ) );

?>
<div class="woocommerce yith-wfbt-slider-wrapper">

	<?php if ( $title ) : ?>
		<h3><?php echo esc_html( $title ); ?></h3>
	<?php endif; ?>

	<div class="yith-wfbt-slider">

		<ul class="yith-wfbt-products-list products">
			<?php foreach ( $args['products'] as $product_id ) : $product = wc_get_product( $product_id ); ?>
				<?php
				if ( is_bool( $product ) ) :
					continue;
				endif;
				?>

				<li class="yith-wfbt-single-product product">

					<?php if ( get_option( 'yith-wfbt-slider-product-image' ) == 'yes' ) : ?>
						<div class="yith-wfbt-product-image">
							<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
								<?php echo $product->get_image( 'shop_catalog' ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>
							</a>
						</div>
					<?php endif; ?>

					<div class="yith-wfbt-product-info">

						<?php if ( get_option( 'yith-wfbt-slider-product-title' ) == 'yes' ) : ?>
							<h3 class="product-title">
								<a href="<?php echo esc_url( $product->get_permalink() ) ?>">
									<?php echo esc_html( $product->get_title() );  ?>
								</a>
							</h3>
						<?php endif; ?>

						<?php if ( $product->is_type( 'variation' ) && get_option( 'yith-wfbt-slider-product-variation' ) == 'yes' ) : ?>
							<div class="product-attributes">
								<?php echo esc_html( implode( ',', $product->get_variation_attributes() ) ); ?>
							</div>
						<?php endif; ?>

						<?php echo get_option( 'yith-wfbt-slider-product-price' ) == 'yes' ? '<div class="product-price">' . $product->get_price_html() . '</div>' : ''; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>

						<?php if ( get_option( 'yith-wfbt-slider-product-rating' ) == 'yes' ) {
							echo wc_get_rating_html( $product->get_average_rating() ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
						} ?>

					</div>


					<?php
					//build add_to_cart url
					$product_base_id = yit_get_base_product_id( $product );
					$url             = add_query_arg( 'add-to-cart', $product_base_id );
					if ( $product->is_type( 'variation' ) ) {
						/**
						 * Prevent error on array_merge if variation_data isn't an array
						 */
						$variation_data = yit_get_prop( $product, 'variation_data', true );
						$query_args     = array( 'variation_id' => $product_id );
						$query_args     = is_array( $variation_data ) ? array_merge( $query_args, $variation_data ) : $query_args;
						$url            = add_query_arg( $query_args, $url );
					}

					$url        = add_query_arg( 'yith_wfbt_shortcode', 1, $url );
					$label_buy  = esc_html( get_option( 'yith-wfbt-slider-buy-button' ) );
					$label_wish = esc_html( get_option( 'yith-wfbt-slider-wishlist-button' ) );
					?>

					<div class="yith-wfbt-product-actions">
						<a class="button yith-wfbt-add-to-cart alt" href="<?php echo esc_url_raw( $url ) ?>"
							data-product_id="<?php echo esc_attr( ( $product->is_type( 'variation' ) ) ? $product_id : $product_base_id ); ?>">
							<?php echo esc_html( $label_buy ); ?>
						</a>
						<a href="#" class="button yith-wfbt-add-wishlist"
							data-product-id="<?php echo esc_attr( $product_base_id ); ?>">
							<?php echo esc_html( $label_wish ); ?>
						</a>
					</div>

				</li>

			<?php endforeach; ?>
		</ul>

		<div class="yith-wfbt-slider-nav">
			<div class="yith-wfbt-nav-prev"></div>
			<div class="yith-wfbt-nav-next"></div>
		</div>

	</div>

</div>