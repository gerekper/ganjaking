<?php
/**
 * Wishlist items widget
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $before_widget          string HTML to print before widget
 * @var $after_widget           string HTML to print after widget
 * @var $instance               array Array of widget options
 * @var $products               array Array of items that were added to lists; each item refers to a product, and contains product object, wishlist items, and quantity count
 * @var $items                  array Array of raw items
 * @var $wishlist_url           string Url to wishlist page
 * @var $multi_wishlist_enabled bool Whether MultiWishlist is enabled or not
 * @var $default_wishlist       YITH_WCWL_Wishlist Default list
 * @var $add_all_to_cart_url    string Url to add all items to cart
 * @var $fragments_options      array Array of options to be used for fragments generation
 * @var $heading_icon           string Heading icon HTML tag
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly
?>

<?php echo apply_filters( 'yith_wcwl_before_wishlist_widget', $before_widget ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

<?php if ( ! empty( $instance['title'] ) ): ?>
	<h3 class="widget-title"><?php echo esc_html( $instance['title'] ); ?></h3>
<?php endif; ?>

	<div class="content <?php echo esc_attr( $instance['style'] ); ?> yith-wcwl-items-<?php echo esc_html( $instance['unique_id'] ); ?> woocommerce wishlist-fragment on-first-load" data-fragment-options="<?php echo esc_attr( json_encode( $fragments_options ) ); ?>">

		<div class="heading">
			<div class="items-counter">
				<?php if ( 'mini' === $instance['style'] ) : ?>
					<a href="<?php echo esc_url( $wishlist_url ); ?>">
				<?php endif; ?>

				<span class="heading-icon">
					<?php echo apply_filters( 'yith_wcwl_widget_items_extended_heading_icon', $heading_icon ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</span>

				<?php if ( ! $instance['ajax_loading'] && isset( $instance['show_count'] ) && 'yes' === $instance['show_count'] ) : ?>
					<span class="items-count">
						<?php echo esc_html( count( $products ) ); ?>
					</span>
				<?php endif; ?>

				<?php if ( 'mini' === $instance['style'] ) : ?>
					</a>
				<?php endif; ?>
			</div>

			<?php if ( isset( $instance['style'] ) && 'extended' == $instance['style'] ) : ?>
				<h3 class="heading-title"><?php echo esc_html( apply_filters( 'yith_wcwl_widget_items_extended_title', __( 'Wishlist', 'yith-eoocommerce-wishlist' ) ) ); ?></h3>
			<?php endif; ?>
		</div>

		<div class="list">
			<?php if ( ! $instance['ajax_loading'] && isset( $instance['show_count'] ) && 'yes' === $instance['show_count'] && 'mini' === $instance['style'] && count( $products ) ) : ?>
				<p class="items-count"><?php echo esc_html( sprintf( __( '%d items in wishlist', 'yith-woocommerce-wishlist' ), count( $products ) ) ); ?></p>
			<?php endif; ?>

			<?php if ( ! $instance['ajax_loading'] && ! empty( $products ) ) : ?>
				<ul class="cart_list product_list_widget">
					<?php
					foreach ( $products as $product_id => $info ) :
						/**
						 * @var $product \WC_Product
						 */
						$product = $info['product'];
						?>
						<li>
							<a href="<?php echo esc_url( add_query_arg( 'remove_from_wishlist', $product_id ) ); ?>" class="remove_from_all_wishlists" data-product-id="<?php echo esc_attr( $product_id ); ?>" data-wishlist-id="<?php echo 'yes' === $instance['show_default_only'] ? esc_attr( $default_wishlist->get_id() ) : '' ?>">&times;</a>
							<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="image-thumb">
								<?php echo $product->get_image(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
							<div class="mini-cart-item-info">
								<a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo esc_html( $product->get_title() ); ?></a>

								<span class="min-cart-subtotal">
									<?php printf( '%d x %s', $info['quantity'], $product->get_price_html() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</span>

								<?php if ( 'yes' != $instance['show_default_only'] ) : ?>
									<small class="mini-cart-wishlist-info">
										<?php printf( '%s: %s', esc_html__( 'In', 'yith-woocommerce-wishlist' ), implode( ', ', $info['in_list'] ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</small>
								<?php endif; ?>
							</div>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php else: ?>
				<p class="empty-wishlist">
					<?php echo esc_html( apply_filters( 'yith_wcwl_widget_items_empty_list', __( 'Please, add your first item to the wishlist', 'yith-woocommerce-wishlist' ) ) ); ?>
				</p>
			<?php endif; ?>

			<?php if ( count( $products ) && 'yes' === $instance['show_view_link'] ) : ?>
				<a class="show-wishlist" href="<?php echo esc_url( $wishlist_url ); ?> "><?php esc_html_e( 'View your wishlist &rsaquo;', 'yith-woocommerce-wishlist' ); ?></a>
			<?php endif; ?>

			<?php if ( count( $products ) && 'yes' === $instance['show_add_all_to_cart'] ) : ?>
				<a class="btn button alt add_all_to_cart" href="<?php echo esc_url( $add_all_to_cart_url ); ?>"><?php esc_html_e( 'Add all to cart', 'yith-woocommerce-wishlist' ); ?></a>
			<?php endif; ?>
		</div>

	</div>

<?php echo apply_filters( 'yith_wcwl_after_wishlist_widget', $after_widget ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>