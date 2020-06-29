<?php
/**
 * Wishlist page template - Modern layout
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $wishlist                      \YITH_WCWL_Wishlist Current wishlist
 * @var $wishlist_items                array Array of items to show for current page
 * @var $is_default                    bool Whether current wishlist is default
 * @var $wishlist_token                string Current wishlist token
 * @var $wishlist_id                   int Current wishlist id
 * @var $users_wishlists               array Array of current user wishlists
 * @var $page_title                    string Page title
 * @var $current_page                  int Current page
 * @var $page_links                    array Array of page links
 * @var $is_user_owner                 bool Whether current user is wishlist owner
 * @var $show_price                    bool Whether to show price column
 * @var $show_dateadded                bool Whether to show item date of addition
 * @var $show_stock_status             bool Whether to show product stock status
 * @var $show_add_to_cart              bool Whether to show Add to Cart button
 * @var $show_remove_product           bool Whether to show Remove button
 * @var $show_price_variations         bool Whether to show price variation over time
 * @var $show_variation                bool Whether to show variation attributes when possible
 * @var $show_cb                       bool Whether to show checkbox column
 * @var $show_quantity                 bool Whether to show input quantity or not
 * @var $show_ask_estimate_button      bool Whether to show Ask an Estimate form
 * @var $show_last_column              bool Whether to show last column (calculated basing on previous flags)
 * @var $move_to_another_wishlist      bool Whether to show Move to another wishlist select
 * @var $move_to_another_wishlist_type string Whether to show a select or a popup for wishlist change
 * @var $additional_info               bool Whether to show Additional info textarea in Ask an estimate form
 * @var $price_excl_tax                bool Whether to show price excluding taxes
 * @var $enable_drag_n_drop            bool Whether to enable drag n drop feature
 * @var $repeat_remove_button          bool Whether to repeat remove button in last column
 * @var $available_multi_wishlist      bool Whether multi wishlist is enabled and available
 * @var $form_action                   string Action for the wishlist form
 * @var $no_interactions               bool
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly
?>

<!-- WISHLIST GRID -->
<ul class="wishlist_table wishlist_view shop_table cart images_grid responsive products columns-<?php echo esc_attr( apply_filters('yith_wcwl_wishlist_view_images_columns', wc_get_loop_prop( 'columns' ) ) ); ?> <?php echo $no_interactions ? 'no-interactions' : ''; ?> <?php echo $enable_drag_n_drop ? 'sortable' : ''; ?>" data-pagination="<?php echo esc_attr( $pagination ); ?>" data-per-page="<?php echo esc_attr( $per_page ); ?>" data-page="<?php echo esc_attr( $current_page ); ?>" data-id="<?php echo esc_attr( $wishlist_id );?>" data-token="<?php echo esc_attr( $wishlist_token ); ?>">
	<?php
	if( $wishlist && $wishlist->has_items() ) :
		foreach( $wishlist_items as $item ) :
			/**
			 * @var $item \YITH_WCWL_Wishlist_Item
			 */
			global $product;

			$product = $item->get_product();
			$availability = $product->get_availability();
			$stock_status = isset( $availability['class'] ) ? $availability['class'] : false;

			if( $product && $product->exists() ) :
				?>
				<li id="yith-wcwl-row-<?php echo esc_attr( $item->get_product_id() ); ?>" <?php wc_product_class( '', $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-row-id="<?php echo esc_attr( $item->get_product_id() ); ?>">
					<div class="item-wrapper">
						<div class="product-thumbnail">
							<?php if( $show_cb ) : ?>
								<div class="product-checkbox">
									<input type="checkbox" value="yes" name="items[<?php echo esc_attr( $item->get_product_id() ); ?>][cb]" />
								</div>
							<?php endif ?>

							<a href="<?php echo esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product', $item->get_product_id() ) ) ); ?>">
								<?php woocommerce_template_loop_product_thumbnail(); ?>
							</a>
						</div>
						<div class="item-details">
							<div class="item-details-wrapper">
								<a class="close" href="#" title="<?php esc_html_e( 'Close', 'yith-woocommerce-wishlist' ); ?>">&times;</a>
								<h3 class="product-name"><?php echo esc_html( apply_filters( 'woocommerce_in_cartproduct_obj_title', $product->get_title(), $product ) ); ?></h3>

								<?php do_action( 'yith_wcwl_table_after_product_name', $item ); ?>

								<?php if( $show_variation || $show_dateadded || $show_price || $show_quantity || $show_stock_status ): ?>
									<table class="item-details-table">

										<?php if( $show_variation && $product->is_type( 'variation' ) ): ?>
											<?php
											/**
											 * @var $product \WC_Product_Variation
											 */
											$attributes = $product->get_attributes();

											if( ! empty( $attributes ) ):
												foreach( $attributes as $name => $value ):
													if( ! taxonomy_exists( $name ) ){
														continue;
													}

													$term = get_term_by( 'slug', $value, $name );

													if ( ! is_wp_error( $term ) && ! empty( $term->name ) ) {
														$value = $term->name;
													}
													?>
													<tr>
														<td class="label">
															<?php echo esc_html( wc_attribute_label( $name, $product ) ); ?>:
														</td>
														<td class="value">
															<?php echo esc_html( rawurldecode( $value ) ); ?>
														</td>
													</tr>
												<?php
												endforeach;
											endif;
											?>
										<?php endif; ?>

										<?php if( $show_dateadded && $item->get_date_added() ): ?>
											<tr class="date-added">
												<td class="label">
													<?php esc_html_e( 'Added on:', 'yith-woocommerce-wishlist' ); ?>
												</td>
												<td class="value">
													<?php echo '<span class="dateadded">' . esc_html( $item->get_date_added_formatted() ) . '</span>' ?>
												</td>
											</tr>
										<?php endif; ?>

										<?php if( $show_price || $show_price_variations ): ?>
											<tr class="product-price">
												<td class="label">
													<?php esc_html_e( 'Price:', 'yith-woocommerce-wishlist' ); ?>
												</td>
												<td class="value">
													<?php
													if( $show_price ) {
														echo $item->get_formatted_product_price(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
													}

													if( $show_price_variations ){
														echo $item->get_price_variation(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
													}
													?>
												</td>
											</tr>
										<?php endif; ?>

										<?php if( $show_quantity ): ?>
											<tr class="product-quantity">
												<td class="label">
													<?php esc_html_e( 'Quantity:', 'yith-woocommerce-wishlist' ); ?>
												</td>
												<td class="value">
													<?php if( ! $no_interactions && $wishlist->current_user_can( 'update_quantity' ) ): ?>
														<input type="number" min="1" step="1" name="items[<?php echo esc_attr( $item->get_product_id() ); ?>][quantity]" value="<?php echo esc_attr( $item->get_quantity() ); ?>" />
													<?php else: ?>
														<?php echo esc_html( $item->get_quantity() ); ?>
													<?php endif; ?>
												</td>
											</tr>
										<?php endif; ?>

										<?php if( $show_stock_status ): ?>
											<tr class="product-stock-status">
												<td class="label">
													<?php esc_html_e( 'Stock:', 'yith-woocommerce-wishlist' ); ?>
												</td>
												<td class="value">
													<?php echo $stock_status === 'out-of-stock' ? '<span class="wishlist-out-of-stock">' . esc_html__( 'Out of stock', 'yith-woocommerce-wishlist' ) . '</span>' : '<span class="wishlist-in-stock">' . esc_html__( 'In Stock', 'yith-woocommerce-wishlist' ) . '</span>'; ?>
												</td>
											</tr>
										<?php endif; ?>

									</table>
								<?php endif; ?>

								<?php if( $show_add_to_cart && isset( $stock_status ) && $stock_status != 'out-of-stock' ): ?>
									<div class="product-add-to-cart">
										<?php woocommerce_template_loop_add_to_cart( array( 'quantity' => $show_quantity ? $item->get_quantity() : 1 ) ); ?>
									</div>
								<?php endif ?>

								<?php if( $move_to_another_wishlist && $available_multi_wishlist && count( $users_wishlists ) > 1 ): ?>
									<div class="move-to-another-wishlist">
										<?php if( 'select' === $move_to_another_wishlist_type ): ?>
											<select class="change-wishlist selectBox">
												<option value=""><?php esc_html_e( 'Move', 'yith-woocommerce-wishlist' ); ?></option>
												<?php
												foreach( $users_wishlists as $wl ):
													/**
													 * @var $wl \YITH_WCWL_Wishlist
													 */
													if( $wl->get_token() === $wishlist_token ){
														continue;
													}
													?>
													<option value="<?php echo esc_attr( $wl->get_token() ); ?>">
														<?php echo esc_html( sprintf( '%s - %s', $wl->get_formatted_name(), $wl->get_formatted_privacy() ) ); ?>
													</option>
												<?php
												endforeach;
												?>
											</select>
										<?php else: ?>
											<a href="#move_to_another_wishlist" class="move-to-another-wishlist-button" data-rel="prettyPhoto[move_to_another_wishlist]">
												<?php echo esc_html( apply_filters( 'yith_wcwl_move_to_another_list_label', __( 'Move to another list &rsaquo;', 'yith-woocommerce-wishlist' ) ) ); ?>
											</a>
										<?php endif; ?>
									</div>
								<?php endif; ?>

								<?php if( $show_remove_product ): ?>
									<div class="product-remove">
										<a href="<?php echo esc_url( add_query_arg( 'remove_from_wishlist', $item->get_product_id() ) ); ?>" class="remove_from_wishlist" title="<?php echo esc_html( apply_filters( 'yith_wcwl_remove_product_wishlist_message_title', __( 'Remove this product', 'yith-woocommerce-wishlist' ) ) ); ?>"><i class="fa fa-trash"></i></a>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</li>
			<?php
			endif;
		endforeach;
	else:
		?>
		<li class="wishlist-empty">
			<?php echo esc_html( apply_filters( 'yith_wcwl_no_product_to_remove_message', __( 'No products added to the wishlist', 'yith-woocommerce-wishlist' ) ) ); ?>
		</li>
	<?php endif; ?>
</ul>

<?php if ( ! empty( $page_links ) ) :
	?>
	<nav class="wishlist-pagination">
		<?php echo $page_links; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	</nav>
<?php endif ?>