<?php
/**
 * SaveForLater List page template
 *
 * @author Your Inspiration Themes
 * @package YITH Save for Later
 * @version 1.0.0
 */
$elements           = count( $savelist_items );
$show_wishlist_link = ( defined( 'YITH_WCWL' ) && get_option( 'ywsfl_show_wishlist_link' ) == 'yes' );
$text               = sprintf( _n( '1 Product', '%s Products', count( $savelist_items ), 'yith-woocommerce-save-for-later' ), count( $savelist_items ) );
?>
<div id="ywsfl_general_content" data-num-elements="<?php echo $elements; ?>">
    <div id="ywsfl_title_save_list"><h3><?php echo $title_list . '(' . $text . ' )'; ?></h3></div>
	<?php

	if ( $elements > 0 ):
		$obj_id = get_queried_object_id();
		$url = empty( $current_page ) ? get_permalink( $obj_id ) : $current_page;
		?>
        <div id="ywsfl_container_list">
			<?php
			do_action( 'before_save_for_later_list_table' );
			?>
            <table class="shop_table shop_table_responsive cart woocommerce-cart-form__contents ywsfl_table">
                <thead>
                <tr>
                    <th class="product-remove">&nbsp;</th>
                    <th class="product-thumbnail">&nbsp;</th>
                    <th class="product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
                    <th class="product-price"><?php esc_html_e( 'Price', 'woocommerce' ); ?></th>
                    <th></th>
					<?php
					if ( $show_wishlist_link ) {
						?>
                        <th></th>
						<?php
					}
					?>

                </tr>
                </thead>
                <tbody>
				<?php do_action( 'before_save_for_later_list_content' ); ?>
				<?php
				foreach ( $savelist_items as $key => $item ) {


					$product_id = ( isset( $item['variation_id'] ) && $item['variation_id'] > 0 ) ? $item['variation_id'] : $item['product_id'];

					global $product;
					$product = wc_get_product( $product_id );
					$item_id = isset( $item['ID'] ) ? $item['ID'] : $key;

					if ( $product && $product->exists()  ) {

						$product_permalink = $product->is_visible() ? $product->get_permalink() : '';
						$product_in_stock  = $product->is_in_stock();
						?>

                        <tr class="woocommerce-cart-form__cart-item cart_item ywsfl-row"
                            id="row-<?php echo $product_id; ?>">
                            <td class="product-remove">
								<?php
								$remove_args = array(
									'remove_from_savelist' => $item_id,
								);
								?>
                                <a href="<?php echo esc_url( add_query_arg( $remove_args, $url ) ) ?>"
                                   class="remove_from_savelist"
                                   data-item_ID="<?php echo $item_id; ?>"
                                   title="Remove this product">&times;</a>
                            </td>
                            <td class="product-thumbnail">
								<?php
								$thumbnail = $product->get_image();

								if ( ! $product_permalink ) {
									echo $thumbnail; // PHPCS: XSS ok.
								} else {
									printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
								}
								?>
                            </td>
                            <td class="product-name"
                                data-title="<?php esc_attr_e( 'Product', 'yith-woocommerce-save-for-later' ); ?>">
								<?php
								if ( ! $product_permalink ) {
									echo wp_kses_post( $product->get_name() . '&nbsp;' );
								} else {
									echo wp_kses_post( sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $product->get_name() ) );
								}

								if ( 'variation' === $product->get_type() ) {

									/**
									 * @var WC_Product_Variation $product
									 */

									$item_variation = ! empty( $item['variations'] ) ? maybe_unserialize( $item['variations'] ) : array();
									$variation      = count( $item_variation ) == 0 ? $product->get_variation_attributes() : $item_variation;


									$cart_item = array(
										'data'      => $product,
										'variation' => $variation
									);

									echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

									$parent_id        = $product->get_parent_id();
									$variable         = wc_get_product( $parent_id );
									$product_in_stock = $variable->is_in_stock();
									echo wc_get_stock_html( $variable );

								} else {
									$product_in_stock = $product->is_in_stock();
									echo wc_get_stock_html( $product );
								}


								?>
                            </td>
                            <td class="product-price" data-title="<?php esc_attr_e( 'Price', 'woocommerce' ); ?>">
								<?php

								if ( $product->get_price() > 0 ) {
									echo WC()->cart->get_product_price( $product );
								} else {
									echo apply_filters( 'yith_free_text', __( 'Free!', 'yith-woocommerce-save-for-later' ) );
								}
								?>
                            </td>
                            <td>
								<?php

								if ( $product_in_stock ) {

								    ?>
                                    <form class="save_for_later_cart" method="POST" >
                                        <?php
                                            if( 'variation' == $product->get_type() && count( $variation )>0 ){

                                                $product_id = $product->get_parent_id();
                                                 foreach( $variation as $attribute_key => $attribute_value ):?>
                                                 <input type="hidden" name="<?php echo $attribute_key ;?>" value="<?php echo $attribute_value;?>">
                                                 <?php
                                                 endforeach;
                                                ?>
                                                <input type="hidden" name="variation_id" value="<?php echo $product->get_id();?>">
                                                <?php
                                            }else{
	                                            $product_id = $product->get_id();
                                                ?>
                                                <?php

                                            }
                                        ?>
                                        <button name="add-to-cart" class="save_add_to_cart" value="<?php echo $product_id;?>"><?php echo $product->single_add_to_cart_text();?></button>
                                    </form>
                                    <?php
								}
								?>
                            </td>
							<?php
							if ( $show_wishlist_link ) {
								?>
                                <td>
									<?php
									if ( ! YITH_WCWL()->is_product_in_wishlist( $product_id ) ) {
										echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );

									}
									?>
                                </td>
								<?php
							}
							?>
                        </tr>
						<?php
					} else {
						global $YIT_Save_For_Later;
						$YIT_Save_For_Later->remove_no_available_product_form_save_list( $item['product_id'], $item['variation_id'] );
					}
				}
				?>

                </tbody>
            </table>
        </div>
	<?php else: ?>
        <span class="ywsfl_no_products_message"><?php _e( 'No Products in save list', 'yith-woocommerce-save-for-later' ); ?></span>
	<?php endif; ?>
</div>
