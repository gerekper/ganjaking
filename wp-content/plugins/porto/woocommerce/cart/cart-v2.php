<?php
/**
 * Cart Version 2
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$porto_woo_version = porto_get_woo_version_number();
?>
<div class="cart-v2">
	<div class="row mb-5 pb-2">
		<div class="col-lg-8">
			<div class="align-left">
				<div class="box-content">
					<form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">
						<?php do_action( 'woocommerce_before_cart_table' ); ?>
						<table class="shop_table responsive cart cart-table woocommerce-cart-form__contents" cellspacing="0">
							<thead>
								<tr>
									<th class="product-thumbnail">&nbsp;</th>
									<th class="product-name"><span><?php esc_html_e( 'Product', 'woocommerce' ); ?></span></th>
									<th class="product-price"><span><?php esc_html_e( 'Price', 'woocommerce' ); ?></span></th>
									<th class="product-quantity"><span><?php esc_html_e( 'Quantity', 'woocommerce' ); ?></span></th>
									<th class="product-subtotal text-center text-md-right"><span><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></span></th>
								</tr>
							</thead>
							<tbody>
								<?php do_action( 'woocommerce_before_cart_contents' ); ?>
								<?php
								foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
									$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
									$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
									if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
										$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
										?>
										<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
											<td class="product-thumbnail">
												<div class="position-relative">
													<?php
													$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
													if ( ! $product_permalink ) {
														echo porto_filter_output( $thumbnail ); // PHPCS: XSS ok.
													} else {
														printf( '<a href="%s">%s</a>', esc_url( $_product->get_permalink( $cart_item ) ), $thumbnail ); // PHPCS: XSS ok.
													}
													echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
														'woocommerce_cart_item_remove_link',
														sprintf(
															'<a href="%s" class="remove remove-product position-absolute" aria-label="%s" data-product_id="%s" data-product_sku="%s" data-cart_id="%s"></a>',
															esc_url( function_exists( 'wc_get_cart_remove_url' ) ? wc_get_cart_remove_url( $cart_item_key ) : WC()->cart->get_remove_url( $cart_item_key ) ),
															esc_attr__( 'Remove this item', 'woocommerce' ),
															esc_attr( $product_id ),
															esc_attr( $_product->get_sku() ),
															esc_attr( $cart_item_key )
														),
														$cart_item_key
													);
													?>
												</div>
											</td>
											<td class="product-name">
												<?php
												if ( ! $product_permalink ) {
													echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', esc_html( $_product->get_name() ), $cart_item, $cart_item_key ) . '&nbsp;' );
												} else {
													echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', $_product->get_permalink( $cart_item ), esc_html( $_product->get_name() ) ), $cart_item, $cart_item_key ) );
												}

												do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

												// Meta data
												echo function_exists( 'wc_get_formatted_cart_item_data' ) ? wc_get_formatted_cart_item_data( $cart_item ) : WC()->cart->get_item_data( $cart_item ); // PHPCS: XSS ok.
												// Backorder notification
												if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
													echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>' ) );
												}
												?>
											</td>
											<td class="product-price">
												<?php
													echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
												?>
											</td>
											<td class="product-quantity">
												<?php
												if ( $_product->is_sold_individually() ) {
													$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
												} else {
													$product_quantity = woocommerce_quantity_input(
														array(
															'input_name'  => "cart[{$cart_item_key}][qty]",
															'input_value' => $cart_item['quantity'],
															'max_value'   => $_product->get_max_purchase_quantity(),
															'min_value'   => '0',
															'product_name'  => $_product->get_name(),
														),
														$_product,
														false
													);
												}
												echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item ); // PHPCS: XSS ok.
												?>
											</td>
											<td class="product-subtotal text-center text-md-right" data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>">
												<?php
													echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key ); // PHPCS: XSS ok.
												?>
											</td>
										</tr>
										<?php
									}
								}
								do_action( 'woocommerce_cart_contents' );
								?>
								<tr>
									<td colspan="6" class="actions px-md-2">
										<?php if ( wc_coupons_enabled() ) { ?>
											<div class="cart_totals_toggle">
												<div id="panel-cart-discount" class="d-sm-flex justify-content-between">
													<div class="coupon">
														<input type="text" name="coupon_code" class="input-text" id="coupon_code" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" value="" />
														<button type="submit" class="button wc-action-btn wc-action-sm" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_html_e( 'Apply coupon', 'woocommerce' ); ?></button>
														<?php do_action( 'woocommerce_cart_coupon' ); ?>
													</div>
													<button type="submit" class="button wc-action-btn wc-action-sm" name="update_cart" value="<?php esc_attr_e( 'Update cart', 'woocommerce' ); ?>"><?php esc_html_e( 'Update cart', 'woocommerce' ); ?></button>
												</div>
											</div>

										<?php } ?>
										<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
									</td>
								</tr>

								<?php do_action( 'woocommerce_after_cart_contents' ); ?>
							</tbody>
						</table>

						<div class="cart-actions">
							<?php do_action( 'woocommerce_cart_actions' ); ?>
						</div>
						<div class="clear"></div>
						<?php do_action( 'woocommerce_after_cart_table' ); ?>
					</form>
				</div>
			</div>
		</div>

		<div class="col-lg-4">

			<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>

			<div class="cart-collaterals">
				<?php
					/**
					 * Cart collaterals hook.
					 *
					 * @hooked woocommerce_cross_sell_display
					 * @hooked woocommerce_cart_totals - 10
					 */
					do_action( 'woocommerce_cart_collaterals' );
				?>
			</div>
		</div>
	</div>

</div>
