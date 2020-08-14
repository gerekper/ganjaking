<?php

/*
 * Print tables based on multi shipping data array. One table for each different item in WC cart.
 */

$cart = WC()->cart->cart_contents;
$product_title = esc_html__( 'Product', 'yith-multiple-shipping-addresses-for-woocommerce' );
$qty_title     = esc_html__( 'Quantity', 'yith-multiple-shipping-addresses-for-woocommerce' );
$ship_title    = esc_html__( 'Ship to', 'yith-multiple-shipping-addresses-for-woocommerce' );

$first_table = true;
?>

<?php if ( $multi_shipping_data ) : ?>
	<?php foreach ( $multi_shipping_data as $item_id => $item ) : ?>
        <?php if ( ! isset( $cart[$item_id] ) ) continue; ?>
		<?php $product = wc_get_product( ! empty( $cart[$item_id]['variation_id'] ) ? $cart[$item_id]['variation_id'] : $cart[$item_id]['product_id'] ); ?>
        <?php if ( ! $product ) continue; ?>
		<?php $first = true; ?>
        <table class="ywcmas_addresses_manager_table shop_table_responsive">
            <thead>
            <th class="ywcmas_addresses_manager_table_product_th"><?php echo $first_table ? $product_title : ''; ?></th>
            <th class="ywcmas_addresses_manager_table_qty_th"><?php echo $first_table ? $qty_title : ''; ?></th>
            </thead>
            <tbody>
            <?php // Now iterate over the shipping selectors of the current cart item. ?>
			<?php foreach ( $item as $shipping_selector_id => $shipping_selector ) : ?>
                <?php
                if ( ! isset( $shipping_selector['qty'] ) || ! isset( $shipping_selector['shipping'] ) ) {
                    continue;
                }
                ?>
                <tr class="ywcmas_addresses_manager_table_shipping_selection_row">
					<?php if ( $first ) : ?>
                        <td class="ywcmas_addresses_manager_table_product_name_td" data-title="<?php echo $product_title; ?>">
                            <input class="ywcmas_addresses_manager_table_product_id" type="hidden" value="<?php echo $product->get_id(); ?>">
                            <input class="ywcmas_addresses_manager_table_item_id" type="hidden" value="<?php echo $item_id; ?>">
                            <span class="ywcmas_addresses_manager_table_img"><?php echo $product->get_image( 'shop_thumbnail' ); ?></span>
                            <span><strong><?php echo $product->get_name(); ?></strong></span>
                        </td>
					<?php else : ?>
                        <td class="ywcmas_addresses_manager_table_product_name_td_empty"></td>
					<?php endif; ?>
                    <td class="ywcmas_addresses_manager_table_qty_td" data-title="<?php echo $qty_title; ?>">
                        <div class="ywcmas_addresses_manager_table_qty_container">
	                        <?php if ( count( $item ) > 1 ) : ?>
                                <div class="ywcmas_addresses_manager_table_remove">
                                    <div class="ywcmas_addresses_manager_table_remove_button">×</div>
                                </div>
	                        <?php endif; ?>
                            <div class="ywcmas_qty">
                                <input class="ywcmas_addresses_manager_table_qty" type="number" value="<?php echo $shipping_selector['qty']; ?>" min="1">
                                <input class="ywcmas_addresses_manager_table_current_qty" type="hidden" value="<?php echo $shipping_selector['qty']; ?>">
                                <input class="ywcmas_addresses_manager_table_item_cart_id" type="hidden" value="<?php echo $item_id; ?>">
                                <input class="ywcmas_addresses_manager_table_shipping_selector_id" type="hidden" value="<?php echo $shipping_selector_id; ?>">
                                <a class="ywcmas_addresses_manager_table_update_qty_button" href="#"><?php esc_html_e( 'Update', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?></a>
                            </div>
                            <div class="ywcmas_select">
                                <select class="ywcmas_addresses_manager_table_shipping_address_select"><?php yith_wcmas_print_addresses_select_options( $shipping_selector['shipping'], get_current_user_id(), true ); ?></select>
                            </div>
                        </div>
                    </td>
                </tr>
				<?php $first = false; ?>
			<?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <td class="ywcmas_addresses_manager_table_foot"></td>
                <td class="ywcmas_addresses_manager_table_foot">
                    <?php $different_addresses_limit = get_option( 'ywcmas_different_addresses_limit', '10' ); ?>
                    <div class="ywcmas_more_addresses">
                        <span class="ywcmas_excluded_item"><?php esc_html_e( 'This item can be shipped to one address only', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?></span>
                        <span class="ywcmas_no_more_shipping_selectors_alert"><?php printf( esc_html__( 'You cannot ship to more than %d different addresses', 'yith-multiple-shipping-addresses-for-woocommerce' ), $different_addresses_limit ); ?></span>
                        <span class="ywcmas_increase_qty_alert"><?php esc_html_e( 'Increase the quantity to ship this item to other addresses', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?></span>
                        <input class="ywcmas_different_addresses_limit" type="hidden" value="<?php echo $different_addresses_limit; ?>">
                        <a class="ywcmas_new_shipping_selector_button"><?php esc_html_e( 'Ship this item to other addresses', 'yith-multiple-shipping-addresses-for-woocommerce' ); ?></a>
                    </div>
                </td>
            </tr>
            </tfoot>
        </table>
        <?php $first_table = false; ?>
	<?php endforeach; ?>
<?php endif; ?>
