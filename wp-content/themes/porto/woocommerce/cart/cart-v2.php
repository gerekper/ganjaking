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
	<h2 class="heading-primary m-b-md font-weight-normal">
		<span><?php esc_html_e( 'Shopping Cart', 'porto' ); ?></span>
		<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn btn-primary proceed-to-checkout"><?php esc_html_e( 'Proceed to Checkout', 'porto' ); ?></a>
	</h2>
	<div class="row">
		<div class="col-lg-8 col-xl-9">

			<div class="featured-box featured-box-primary align-left">
				<div class="box-content">
					<form class="woocommerce-cart-form" action="<?php echo esc_url( version_compare( $porto_woo_version, '2.5', '<' ) ? WC()->cart->get_cart_url() : wc_get_cart_url() ); ?>" method="post">
						<?php do_action( 'woocommerce_before_cart_table' ); ?>
						<table class="shop_table responsive cart woocommerce-cart-form__contents" cellspacing="0">
							<thead>
								<tr>
									<th class="product-remove">&nbsp;</th>
									<th class="product-thumbnail">&nbsp;</th>
									<th class="product-name"><?php esc_html_e( 'Product Name', 'porto' ); ?></th>
									<th class="product-price"><?php esc_html_e( 'Unit Price', 'porto' ); ?></th>
									<th class="product-quantity"><?php esc_html_e( 'Qty', 'porto' ); ?></th>
									<th class="product-subtotal"><?php esc_html_e( 'Subtotal', 'porto' ); ?></th>
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
											<td class="product-remove">
												<?php
													echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
														'woocommerce_cart_item_remove_link',
														sprintf(
															'<a href="%s" class="remove remove-product" aria-label="%s" data-product_id="%s" data-product_sku="%s" data-cart_id="%s">&times;</a>',
															esc_url( function_exists( 'wc_get_cart_remove_url' ) ? wc_get_cart_remove_url( $cart_item_key ) : WC()->cart->get_remove_url( $cart_item_key ) ),
															esc_attr__( 'Remove this item', 'porto' ),
															esc_attr( $product_id ),
															esc_attr( $_product->get_sku() ),
															esc_attr( $cart_item_key )
														),
														$cart_item_key
													);
												?>
											</td>
											<td class="product-thumbnail">
												<?php
												$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
												if ( ! $product_permalink ) {
													echo porto_filter_output( $thumbnail ); // PHPCS: XSS ok.
												} else {
													printf( '<a href="%s">%s</a>', esc_url( $_product->get_permalink( $cart_item ) ), $thumbnail ); // PHPCS: XSS ok.
												}
												?>
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
											<td class="product-subtotal" data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>">
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
									<td colspan="6" class="actions">
										<?php if ( version_compare( $porto_woo_version, '2.5', '<' ) ? WC()->cart->coupons_enabled() : wc_coupons_enabled() ) { ?>
											<div class="cart_totals_toggle">
												<div class="card card-default">
													<div class="card-header arrow">
														<h2 class="card-title"><a class="accordion-toggle collapsed" data-toggle="collapse" href="#panel-cart-discount"><?php esc_html_e( 'DISCOUNT CODE', 'porto' ); ?></a></h2>
													</div>
													<div id="panel-cart-discount" class="accordion-body collapse">
														<div class="card-body">
															<div class="coupon">
																<label for="coupon_code"><?php esc_html_e( 'Enter your coupon code if you have one:', 'porto' ); ?></label>
																<input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" />
																<button type="submit" class="btn btn-primary" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_html_e( 'Apply coupon', 'woocommerce' ); ?></button>
																<?php do_action( 'woocommerce_cart_coupon' ); ?>
															</div>
														</div>
													</div>
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
							<a class="btn btn-default" href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>"><?php esc_html_e( 'Continue Shopping', 'porto' ); ?></a>
							<button type="submit" class="btn btn-default pt-right" name="update_cart" value="<?php esc_attr_e( 'Update Cart', 'porto' ); ?>"><?php esc_html_e( 'Update Cart', 'porto' ); ?></button>
							<?php do_action( 'woocommerce_cart_actions' ); ?>
						</div>
						<div class="clear"></div>
						<?php do_action( 'woocommerce_after_cart_table' ); ?>
					</form>
				</div>
			</div>
		</div>

		<div class="col-lg-4 col-xl-3">

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
