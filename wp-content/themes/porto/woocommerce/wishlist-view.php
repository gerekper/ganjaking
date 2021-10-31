<?php
/**
 * Wishlist page template - Standard Layout
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Wishlist
 * @version 3.0.0
 */

/**
 * Template variables:
 *
 * @var $wishlist \YITH_WCWL_Wishlist Current wishlist
 * @var $wishlist_items array Array of items to show for current page
 * @var $wishlist_token string Current wishlist token
 * @var $wishlist_id int Current wishlist id
 * @var $users_wishlists array Array of current user wishlists
 * @var $current_page int Current page
 * @var $page_links array Array of page links
 * @var $is_user_owner bool Whether current user is wishlist owner
 * @var $show_price bool Whether to show price column
 * @var $show_dateadded bool Whether to show item date of addition
 * @var $show_stock_status bool Whether to show product stock status
 * @var $show_add_to_cart bool Whether to show Add to Cart button
 * @var $show_remove_product bool Whether to show Remove button
 * @var $show_price_variations bool Whether to show price variation over time
 * @var $show_variation bool Whether to show variation attributes when possible
 * @var $show_cb bool Whether to show checkbox column
 * @var $show_quantity bool Whether to show input quantity or not
 * @var $show_ask_estimate_button bool Whether to show Ask an Estimate form
 * @var $show_last_column bool Whether to show last column (calculated basing on previous flags)
 * @var $move_to_another_wishlist bool Whether to show Move to another wishlist select
 * @var $move_to_another_wishlist_type string Whether to show a select or a popup for wishlist change
 * @var $additional_info bool Whether to show Additional info textarea in Ask an estimate form
 * @var $price_excl_tax bool Whether to show price excluding taxes
 * @var $enable_drag_n_drop bool Whether to enable drag n drop feature
 * @var $repeat_remove_button bool Whether to repeat remove button in last column
 * @var $available_multi_wishlist bool Whether multi wishlist is enabled and available
 * @var $no_interactions bool
 */

if ( ! defined( 'YITH_WCWL' ) ) {
	exit;
} // Exit if accessed directly
?>

<!-- WISHLIST TABLE -->
<table class="shop_table cart wishlist_table wishlist_view traditional responsive" data-pagination="<?php echo esc_attr( $pagination ); ?>" data-per-page="<?php echo esc_attr( $per_page ); ?>" data-page="<?php echo esc_attr( $current_page ); ?>" data-id="<?php echo $wishlist_id; ?>" data-token="<?php echo $wishlist_token; ?>">

	<?php $column_count = 2; ?>

	<thead class="<?php echo $wishlist && $wishlist->has_items() ? '' : 'd-none'; ?>">
	<tr>
		<?php
		if ( $show_cb ) :
			$column_count ++;
			?>
			<th class="product-checkbox">
				<input type="checkbox" value="" name="" id="bulk_add_to_cart"/>
			</th>
		<?php endif; ?>

		<?php
		if ( $show_remove_product ) :
			$column_count ++;
			?>
		<?php endif; ?>

		<th class="product-thumbnail"></th>

		<th class="product-name">
			<span class="nobr">
				<?php echo esc_html( apply_filters( 'yith_wcwl_wishlist_view_name_heading', __( 'Product', 'yith-woocommerce-wishlist' ) ) ); ?>
			</span>
		</th>

		<?php
		if ( $show_price || $show_price_variations ) :
			$column_count ++;
			?>
			<th class="product-price">
				<span class="nobr">
					<?php echo esc_html( apply_filters( 'yith_wcwl_wishlist_view_price_heading', __( 'Price', 'yith-woocommerce-wishlist' ) ) ); ?>
				</span>
			</th>
		<?php endif; ?>

		<?php
		if ( $show_quantity ) :
			$column_count ++;
			?>
			<th class="product-quantity">
				<span class="nobr">
					<?php echo apply_filters( 'yith_wcwl_wishlist_view_quantity_heading', __( 'Quantity', 'yith-woocommerce-wishlist' ) ); ?>
				</span>
			</th>
		<?php endif; ?>

		<?php
		if ( $show_stock_status ) :
			$column_count ++;
			?>
			<th class="product-stock-status">
				<span class="nobr">
					<?php echo esc_html( apply_filters( 'yith_wcwl_wishlist_view_stock_heading', __( 'Stock status', 'yith-woocommerce-wishlist' ) ) ); ?>
				</span>
			</th>
		<?php endif; ?>

		<?php
		if ( $show_last_column ) :
			$column_count ++;
			?>
			<th class="product-add-to-cart">
				<span class="nobr">
					<?php esc_html_e( 'Actions', 'yith-woocommerce-wishlist' ); ?>
				</span>
			</th>
		<?php endif; ?>

		<?php
		if ( $enable_drag_n_drop ) :
			$column_count ++;
			?>
			<th class="product-arrange">
				<span class="nobr">
					<?php echo esc_html( apply_filters( 'yith_wcwl_wishlist_view_arrange_heading', __( 'Arrange', 'yith-woocommerce-wishlist' ) ) ); ?>
				</span>
			</th>
		<?php endif; ?>
	</tr>
	</thead>

	<tbody class="wishlist-items-wrapper">
	<?php
	if ( $wishlist && $wishlist->has_items() ) :
		foreach ( $wishlist_items as $item ) :
			/**
			 * Each of the wishlist items
			 *
			 * @var $item \YITH_WCWL_Wishlist_Item
			 */
			global $product;

			$product      = $item->get_product();
			$availability = $product->get_availability();
			$stock_status = isset( $availability['class'] ) ? $availability['class'] : false;

			if ( $product && $product->exists() ) :
				?>
				<tr id="yith-wcwl-row-<?php echo esc_attr( $item->get_product_id() ); ?>" data-row-id="<?php echo esc_attr( $item->get_product_id() ); ?>">
					<?php if ( $show_cb ) : ?>
						<td class="product-checkbox">
							<input type="checkbox" value="yes" name="items[<?php echo esc_attr( $item->get_product_id() ); ?>][cb]" />
						</td>
					<?php endif ?>

					<?php if ( $show_remove_product ) : ?>
					<?php endif; ?>

					<td class="product-thumbnail">
						<div class="position-relative">
							<a href="<?php echo esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product', $item->get_product_id() ) ) ); ?>">
								<?php echo $product->get_image(); ?>
							</a>
							<a href="<?php echo esc_url( add_query_arg( 'remove_from_wishlist', $item->get_product_id() ) ); ?>" class="remove remove_from_wishlist" title="<?php echo apply_filters( 'yith_wcwl_remove_product_wishlist_message_title', __( 'Remove this product', 'yith-woocommerce-wishlist' ) ); ?>"></a>
						</div>
					</td>

					<td class="product-name">
						<?php do_action( 'yith_wcwl_table_before_product_name', $item, $wishlist ); ?>

						<a href="<?php echo esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product', $item->get_product_id() ) ) ); ?>"><?php echo apply_filters( 'woocommerce_in_cartproduct_obj_title', $product->get_name(), $product ); ?></a>

						<?php
						if ( $show_variation && $product->is_type( 'variation' ) ) {
							/**
							 * Product is a Variation
							 *
							 * @var $product \WC_Product_Variation
							 */
							echo wp_kses_post( wc_get_formatted_variation( $product ) );
						}
						?>

						<?php do_action( 'yith_wcwl_table_after_product_name', $item, $wishlist ); ?>
					</td>

					<?php if ( $show_price || $show_price_variations ) : ?>
						<td class="product-price">
							<?php do_action( 'yith_wcwl_table_before_product_price', $item, $wishlist ); ?>

							<?php
							if ( $show_price ) {
								echo wp_kses_post( $item->get_formatted_product_price() );
							}

							if ( $show_price_variations ) {
								echo wp_kses_post( $item->get_price_variation() );
							}
							?>

							<?php do_action( 'yith_wcwl_table_after_product_price', $item, $wishlist ); ?>
						</td>
					<?php endif ?>

					<?php if ( $show_quantity ) : ?>
						<td class="product-quantity">
							<?php do_action( 'yith_wcwl_table_before_product_quantity', $item, $wishlist ); ?>

							<?php if ( ! $no_interactions && $wishlist->current_user_can( 'update_quantity' ) ) : ?>
								<input type="number" min="1" step="1" name="items[<?php echo esc_attr( $item->get_product_id() ); ?>][quantity]" value="<?php echo esc_attr( $item->get_quantity() ); ?>" />
							<?php else : ?>
								<?php echo esc_html( $item->get_quantity() ); ?>
							<?php endif; ?>

							<?php do_action( 'yith_wcwl_table_after_product_quantity', $item, $wishlist ); ?>
						</td>
					<?php endif; ?>

					<?php if ( $show_stock_status ) : ?>
						<td class="product-stock-status">
							<?php do_action( 'yith_wcwl_table_before_product_stock', $item, $wishlist ); ?>

							<?php echo 'out-of-stock' === $stock_status ? '<span class="wishlist-out-of-stock">' . esc_html( apply_filters( 'yith_wcwl_out_of_stock_label', __( 'Out of stock', 'yith-woocommerce-wishlist' ) ) ) . '</span>' : '<span class="wishlist-in-stock text-v-dark">' . esc_html( apply_filters( 'yith_wcwl_in_stock_label', __( 'In Stock', 'yith-woocommerce-wishlist' ) ) ) . '</span>'; ?>

							<?php do_action( 'yith_wcwl_table_after_product_stock', $item, $wishlist ); ?>
						</td>
					<?php endif ?>

					<?php if ( $show_last_column ) : ?>
						<td class="product-add-to-cart">
							<?php do_action( 'yith_wcwl_table_before_product_cart', $item, $wishlist ); ?>

							<!-- Date added -->
							<?php
							if ( $show_dateadded && $item->get_date_added() ) :
								// translators: date added label: 1 date added.
								echo '<span class="dateadded">' . esc_html( sprintf( __( 'Added on: %s', 'yith-woocommerce-wishlist' ), $item->get_date_added_formatted() ) ) . '</span>';
							endif;
							?>

							<?php do_action( 'yith_wcwl_table_product_before_add_to_cart', $item, $wishlist ); ?>

							<!-- Add to cart button -->
							<?php $show_add_to_cart = apply_filters( 'yith_wcwl_table_product_show_add_to_cart', $show_add_to_cart, $item, $wishlist ); ?>
							<?php if ( $show_add_to_cart && isset( $stock_status ) && 'out-of-stock' !== $stock_status ) : ?>
								<?php woocommerce_template_loop_add_to_cart( array( 'quantity' => $show_quantity ? $item->get_quantity() : 1 ) ); ?>
							<?php endif ?>

							<?php do_action( 'yith_wcwl_table_product_after_add_to_cart', $item, $wishlist ); ?>

							<!-- Change wishlist -->
							<?php $move_to_another_wishlist = apply_filters( 'yith_wcwl_table_product_move_to_another_wishlist', $move_to_another_wishlist, $item, $wishlist ); ?>
							<?php if ( $move_to_another_wishlist && $available_multi_wishlist && count( $users_wishlists ) > 1 ) : ?>
								<?php if ( 'select' === $move_to_another_wishlist_type ) : ?>
									<select class="change-wishlist selectBox">
										<option value=""><?php esc_html_e( 'Move', 'yith-woocommerce-wishlist' ); ?></option>
										<?php
										foreach ( $users_wishlists as $wl ) :
											/**
											 * Each of customer's wishlists
											 *
											 * @var $wl \YITH_WCWL_Wishlist
											 */
											if ( $wl->get_token() === $wishlist_token ) {
												continue;
											}
											?>
											<option value="<?php echo esc_attr( $wl->get_token() ); ?>">
												<?php echo sprintf( '%s - %s', esc_html( $wl->get_formatted_name() ), esc_html( $wl->get_formatted_privacy() ) ); ?>
											</option>
											<?php
										endforeach;
										?>
									</select>
								<?php else : ?>
									<a href="#move_to_another_wishlist" class="move-to-another-wishlist-button" data-rel="prettyPhoto[move_to_another_wishlist]">
										<?php echo esc_html( apply_filters( 'yith_wcwl_move_to_another_list_label', __( 'Move to another list &rsaquo;', 'yith-woocommerce-wishlist' ) ) ); ?>
									</a>
								<?php endif; ?>

								<?php do_action( 'yith_wcwl_table_product_after_move_to_another_wishlist', $item, $wishlist ); ?>

							<?php endif; ?>

							<!-- Remove from wishlist -->
							<?php if ( $repeat_remove_button ) : ?>
								<a href="<?php echo esc_url( $item->get_remove_url() ); ?>" class="remove_from_wishlist button" title="<?php echo esc_html( apply_filters( 'yith_wcwl_remove_product_wishlist_message_title', __( 'Remove this product', 'yith-woocommerce-wishlist' ) ) ); ?>"><?php esc_html_e( 'Remove', 'yith-woocommerce-wishlist' ); ?></a>
							<?php endif; ?>

							<?php do_action( 'yith_wcwl_table_after_product_cart', $item, $wishlist ); ?>
						</td>
					<?php endif; ?>

					<?php if ( $enable_drag_n_drop ) : ?>
						<td class="product-arrange ">
							<i class="fa fa-arrows"></i>
							<input type="hidden" name="items[<?php echo esc_attr( $item->get_product_id() ); ?>][position]" value="<?php echo esc_attr( $item->get_position() ); ?>" />
						</td>
					<?php endif; ?>
				</tr>
				<?php
			endif;
		endforeach;
	else :
		?>
		<tr class="border-0 py-0">
			<td colspan="<?php echo esc_attr( $column_count ); ?>" class="px-3 py-2 text-center"><i class="far fa-heart wishlist-empty"></i></td>
		</tr>
		<tr class="border-0 py-0">
			<td colspan="<?php echo esc_attr( $column_count ); ?>" class="px-3 py-2 wishlist-empty"><?php echo apply_filters( 'yith_wcwl_no_product_to_remove_message', __( 'No products added to the wishlist', 'yith-woocommerce-wishlist' ) ); ?></td>
		</tr>
		<tr class="border-0 py-0">
			<td colspan="<?php echo esc_attr( $column_count ); ?>" class="px-3 text-center">
				<a class="woocommerce-Button button btn-v-dark btn-go-shop" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>">
					<?php esc_html_e( 'GO SHOP', 'porto' ); ?>
				</a>
			</td>
		</tr>
		<?php
	endif;

	if ( ! empty( $page_links ) ) :
		?>
		<tr class="pagination-row">
			<td colspan="<?php echo esc_attr( $column_count ); ?>"><?php echo wp_kses_post( $page_links ); ?></td>
		</tr>
	<?php endif ?>
	</tbody>

</table>
