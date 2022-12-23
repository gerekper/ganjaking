
<h3><?php echo esc_html(WC_Wishlists_Settings::get_setting('wc_wishlist_label_saved_for_label_title', 'Saved for later')); ?></h3>
<table class="shop_table shop_table_responsive cart wl-table view" cellspacing="0">
    <thead>

    <tr>
        <th class="product-thumbnail">&nbsp;</th>
        <th class="product-name"><?php _e( 'Product', 'wc_wishlist' ); ?></th>
        <th class="product-price"><?php _e( 'Price', 'wc_wishlist' ); ?></th>
        <th class="product-quantity ctr"><?php _e( 'Qty', 'wc_wishlist' ); ?></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
	<?php
	foreach ( $wishlist_items as $wishlist_item_key => $item ) :
		$_product = wc_get_product( $item['data'] );

		if ( $_product->exists() && $item['quantity'] > 0 ) :
			?>
            <tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item cart_table_item', $item, $wishlist_item_key ) ); ?>">
                <td class="product-thumbnail" data-title="product-thumbnail">
					<?php
					printf( '<a href="%s">%s</a>', esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product_id', $item['product_id'] ) ) ), $_product->get_image() );
					?>

                </td>
                <td class="product-name" data-title="<?php _e( 'Product', 'wc_wishlist' ); ?>">
					<?php
					if ( WC_Wishlist_Compatibility::is_wc_version_gte_2_1() ) {
						if ( ! $_product->is_visible() ) {
							echo apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $item, $wishlist_item_key );
						} else {
							echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( is_array( $item['variation'] ) ? add_query_arg( $item['variation'], $_product->get_permalink() ) : $_product->get_permalink() ), $_product->get_name() ), $item, $wishlist_item_key );
						}
					} else {
						printf( '<a href="%s">%s</a>', esc_url( get_permalink( apply_filters( 'woocommerce_in_cart_product_id', $item['product_id'] ) ) ), apply_filters( 'woocommerce_in_wishlist_product_title', $_product->get_name(), $_product, $wishlist_item_key ) );
					}


					// Meta data
					if ( function_exists( 'wc_get_formatted_cart_item_data' ) ) {
						echo wc_get_formatted_cart_item_data( $item );
					} else {
						echo WC()->cart->get_item_data( $item );
					}

					// Availability
					$availability = $_product->get_availability();

					if ( $availability && $availability['availability'] ) :
						echo apply_filters( 'woocommerce_stock_html', '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</p>', $availability['availability'] );
					endif;
					?>

					<?php do_action( 'woocommerce_wishlist_after_list_item_name', $item, $item['wishlist'] ); ?>
                </td>
                <td class="product-price" data-title="<?php _e( 'Price', 'wc_wishlist' ); ?>">
					<?php
					$price = '';
					if ( WC_Wishlist_Compatibility::is_wc_version_gte_2_1() ) {
						$price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $item, $wishlist_item_key );
					} else {
						$product_price = ( get_option( 'woocommerce_display_cart_prices_excluding_tax' ) == 'yes' ) ? wc_get_price_excluding_tax( $_product ) : $_product->get_price();
						$price         = apply_filters( 'woocommerce_cart_item_price_html', wc_price( $product_price ), $item, $wishlist_item_key );
					}
					?>

					<?php echo apply_filters( 'woocommerce_wishlist_list_item_price', $price, $item, $item['wishlist'] ); ?>

                </td>
                <td class="product-quantity" data-title="<?php _e( 'Quantity', 'wc_wishlist' ); ?>">
					<?php echo apply_filters( 'woocommerce_wishlist_list_item_quantity_value', esc_attr( $item['quantity'] ), $item, $item['wishlist'] ); ?>
                </td>

				<?php if ( WC_Wishlists_Settings::get_setting( 'wc_wishlist_show_previously_ordered', 'no' ) == 'yes' ): ?>
                    <td class="product-quantity"
                        data-title="<?php echo apply_filters( 'wc_wishlist_show_previously_ordered_column_heading', WC_Wishlists_Settings::get_setting( 'wc_wishlist_show_previously_ordered_column_heading', __( 'Ordered', 'wc_wishlist' ) ) ); ?>">

						<?php echo( isset( $item['ordered_total'] ) ? intval( $item['ordered_total'] ) : 0 ); ?>
                    </td>
				<?php endif; ?>

                <td class="product-purchase"
                    data-title="<?php _e( 'Add to Cart', 'wc_wishlist' ); ?>">
					<?php if ( ! $_product->is_type( 'external' ) && $_product->is_in_stock() && apply_filters( 'woocommerce_wishlist_user_can_purcahse', true, $_product ) ) : ?>
                        <a rel="nofollow"
                           href="<?php echo woocommerce_wishlist_url_item_move_from_saved( $item['wishlist']->id, $wishlist_item_key, false, false, add_query_arg([]) ); ?>"
                           class="button"><?php _e( 'Add to Cart', 'wc_wishlist' ); ?></a>
					<?php elseif ( $_product->is_type( 'external' ) == 'external' ) : ?>
                        <a rel="nofollow"
                           href="<?php echo esc_url( get_permalink( $_product->get_id() ) ); ?>"
                           rel="nofollow"
                           class="single_add_to_cart_button button alt"><?php echo $_product->single_add_to_cart_text(); ?></a>
					<?php endif; ?>
                </td>

            </tr>
		<?php endif; ?>
	<?php endforeach;
	?>
    </tbody>
</table>