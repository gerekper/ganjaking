<?php

/**
 * Metabox for Subscription Items details
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$product      = wc_get_product( $subscription->product_id );
$product_link = $product ? admin_url( 'post.php?post=' . $subscription->product_id . '&action=edit' ) : '';
$thumbnail    = $product ? apply_filters( 'woocommerce_admin_order_item_thumbnail', $product->get_image( 'thumbnail', array( 'title' => '' ), false ), $subscription->product_id, $product ) : '';

$order = wc_get_order( $subscription->order_id );
if ( ! $order ) {
	return;
}

?>
<div id="woocommerce-order-items">
	<div class="woocommerce_order_items_wrapper wc-order-items-editable">
		<table cellpadding="0" cellspacing="0" class="woocommerce_order_items ywsbs_subscription_items_list">
			<thead>
			<tr>
				<th class="ywsbs_subscription_items_list_item"
					colspan="2"><?php esc_html_e( 'Item', 'yith-woocommerce-subscription' ); ?></th>
				<th class="ywsbs_subscription_items_list_quantity"><?php esc_html_e( 'Cost', 'yith-woocommerce-subscription' ); ?></th>
				<th class="ywsbs_subscription_items_list_quantity"><?php esc_html_e( 'Qty', 'yith-woocommerce-subscription' ); ?></th>
				<th class="ywsbs_subscription_items_list_total"><?php esc_html_e( 'Total', 'yith-woocommerce-subscription' ); ?></th>
				<th class="ywsbs_subscription_items_list_tax"><?php esc_html_e( 'Tax', 'yith-woocommerce-subscription' ); ?></th>
				<th class="wc-order-edit-line-item" width="1%"></th>
			</tr>
			</thead>

			<tbody id="order_line_items">

			<tr class="item">
				<td class="thumb">
					<?php echo '<div class="wc-order-item-thumbnail">' . wp_kses_post( $thumbnail ) . '</div>'; ?>
				</td>
				<td class="name ywsbs_subscription_items_list_item">
					<?php
					echo $product_link ? '<a href="' . esc_url( $product_link ) . '" class="wc-order-item-name">' . wp_kses_post( $subscription->product_name ) . '</a>' : '<div class="wc-order-item-name">' . esc_html( $subscription->product_name ) . '</div>';

					$text_align  = is_rtl() ? 'right' : 'left';
					$margin_side = is_rtl() ? 'left' : 'right';
					$item        = $order->get_item( $subscription->order_item_id );

					wc_display_item_meta(
						$item,
						array(
							'label_before' => '<strong class="wc-item-meta-label" style="float: ' . esc_attr( $text_align ) . '; margin-' . esc_attr( $margin_side ) . ': .25em; clear: both">',
						)
					);

					if ( $product && $product->get_sku() ) {
						echo '<div class="wc-order-item-sku"><strong>' . esc_html__( 'SKU:', 'yith-woocommerce-subscription' ) . '</strong> ' . esc_html( $product->get_sku() ) . '</div>';
					}

					if ( $subscription->variation_id ) {
						echo '<div class="wc-order-item-variation"><strong>' . esc_html__( 'Variation ID:', 'yith-woocommerce-subscription' ) . '</strong> ';
						if ( 'product_variation' === get_post_type( $subscription->variation_id ) ) {
							echo esc_html( $subscription->variation_id ) . '<br>';
							yith_ywsbs_get_product_meta( $subscription, $subscription->variation );
						} else {
							/* translators: %s: variation id */
							printf( esc_html__( '%s (No longer exists)', 'yith-woocommerce-subscription' ), esc_html( $subscription->variation_id ) );
						}
						echo '</div>';
					}
					?>
				</td>

				<td class="ywsbs_subscription_items_list_cost item_cost" width="1%">
					<div class="view">
						<?php
						$cost = $subscription->quantity ? floatval( $subscription->line_total ) / floatval( $subscription->quantity ) : 0;
						echo wp_kses_post( wc_price( $cost, array( 'currency' => $subscription->order_currency ) ) );
						?>
					</div>
				</td>
				<td class="quantity" width="1%">
					<div class="view">
						<?php
						echo wp_kses_post( '<small class="times">&times;</small> ' ) . esc_html( $subscription->quantity );
						?>
					</div>
					<div class="edit" style="display: none;">
						<input type="number"
							   step="<?php echo esc_attr( apply_filters( 'woocommerce_quantity_input_step', '1', $product ) ); ?>"
							   min="0" autocomplete="off" name="ywsbs_quantity" placeholder="0"
							   value="<?php echo esc_attr( $subscription->quantity ); ?>"
							   data-qty="<?php echo esc_attr( $subscription->quantity ); ?>" size="4" class="quantity" />
					</div>
				</td>
				<td class="line_cost ywsbs_subscription_items_list_total" width="1%">
					<div class="view">
						<?php echo wp_kses_post( wc_price( $subscription->line_total, array( 'currency' => $subscription->order_currency ) ) ); ?>
					</div>
					<div class="edit" style="display: none;">
						<div class="split-input">
							<div class="input">
								<label><?php esc_attr_e( 'Total:', 'yith-woocommerce-subscription' ); ?></label>
								<input type="text" name="ywsbs_line_total"
									   placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>"
									   value="<?php echo esc_attr( wc_format_localized_price( $subscription->line_total ) ); ?>"
									   class="line_total wc_input_price"
									   data-total="<?php echo esc_attr( wc_format_localized_price( $subscription->line_total ) ); ?>" />
							</div>
						</div>
					</div>
				</td>
				<td class="line_cost ywsbs_subscription_items_list_total" width="1%">
					<div class="view">
						<?php echo wp_kses_post( wc_price( $subscription->line_tax, array( 'currency' => $subscription->order_currency ) ) ); ?>
					</div>
					<div class="edit" style="display: none;">
						<div class="split-input">
							<div class="input">
								<label><?php esc_attr_e( 'Total Tax:', 'yith-woocommerce-subscription' ); ?></label>
								<input type="text" name="ywsbs_line_tax"
									   placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>"
									   value="<?php echo esc_attr( wc_format_localized_price( $subscription->line_tax ) ); ?>"
									   class="line_tax wc_input_price"
									   data-total="<?php echo esc_attr( wc_format_localized_price( $subscription->line_tax ) ); ?>" />
							</div>
						</div>
					</div>
				</td>
				<td class="wc-order-edit-line-item" width="1%">
					<div class="wc-order-edit-line-item-actions">
						<?php if ( $subscription->can_be_editable( 'recurring_amount' ) ) : ?>
							<a class="edit-order-item tips" href="#"
							   data-tip="<?php esc_attr_e( 'Edit item', 'yith-woocommerce-subscription' ); ?>"></a>
						<?php endif; ?>
					</div>
				</td>
			</tr>
			</tbody>
			<tbody class="order_shipping_line_items">
			<?php if ( ! empty( $subscription->subscriptions_shippings ) ) : ?>
				<tr class="shipping">
					<td class="thumb">
						<div></div>
					</td>
					<?php if ( isset( $subscription->subscriptions_shippings['name'] ) ) : ?>
						<td class="name">
							<div class="view">
								<?php echo esc_html( $subscription->subscriptions_shippings['name'] ); ?>
							</div>
							<div class="edit" style="display: none;">
								<input type="text" class="shipping_method_name"
									   placeholder="<?php esc_attr_e( 'Shipping name', 'yith-woocommerce-subscription' ); ?>"
									   name="ywsbs_shipping_method_name"
									   value="<?php echo esc_attr( $subscription->subscriptions_shippings['name'] ); ?>" />
							</div>
						</td>
					<?php endif; ?>
					<td class="item_cost" width="1%">&nbsp;</td>
					<td class="quantity" width="1%">&nbsp;</td>
					<td class="line_cost" width="1%">
						<div class="view">
							<?php echo wp_kses_post( wc_price( $subscription->order_shipping, array( 'currency' => $subscription->order_currency ) ) ); ?>
						</div>
						<div class="edit" style="display: none;">
							<input type="text" name="ywsbs_shipping_cost_line_cost"
								   placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>"
								   value="<?php echo esc_attr( wc_format_localized_price( $subscription->order_shipping ) ); ?>"
								   class="line_total wc_input_price" />
						</div>
					</td>
					<td class="line_tax" width="1%">
						<div class="view">
							<?php echo wp_kses_post( wc_price( $subscription->order_shipping_tax, array( 'currency' => $subscription->order_currency ) ) ); ?>
						</div>
						<div class="edit" style="display: none;">
							<input type="text" name="ywsbs_shipping_cost_line_tax"
								   placeholder="<?php echo esc_attr( wc_format_localized_price( 0 ) ); ?>"
								   value="<?php echo esc_attr( wc_format_localized_price( $subscription->order_shipping_tax ) ); ?>"
								   class="line_tax wc_input_price" />
						</div>
					</td>

					<td class="wc-order-edit-line-item">
						<?php if ( $subscription->can_be_editable( 'recurring_amount' ) ) : ?>
							<div class="wc-order-edit-line-item-actions">
								<a class="edit-order-item" href="#"></a>
							</div>
						<?php endif; ?>
					</td>
				</tr>
			<?php endif; ?>

			</tbody>
		</table>
	</div>

	<div class="wc-order-data-row wc-order-totals-items wc-order-items-editable">
		<table class="wc-order-totals">
			<?php if ( $subscription->subscriptions_shippings ) : ?>
				<tr>
					<td class="label"><?php esc_html_e( 'Shipping', 'yith-woocommerce-subscription' ); ?>:</td>
					<td width="1%"></td>
					<td class="total">
						<?php
						echo wp_kses_post( wc_price( $subscription->order_shipping, array( 'currency' => $subscription->order_currency ) ) );
						?>
					</td>
				</tr>
			<?php endif; ?>


			<?php if ( wc_tax_enabled() ) : ?>

				<tr>
					<td class="label"><?php esc_html_e( 'Tax', 'yith-woocommerce-subscription' ); ?>:</td>
					<td width="1%"></td>
					<td class="total">
						<?php
						echo wp_kses_post( wc_price( ( floatval( $subscription->order_shipping_tax ) + floatval( $subscription->order_tax ) ), array( 'currency' => $subscription->order_currency ) ) );
						?>
					</td>
				</tr>

			<?php endif; ?>

			<tr>
				<td class="label"><?php esc_html_e( 'Total', 'yith-woocommerce-subscription' ); ?>:</td>
				<td width="1%"></td>
				<td class="total">
					<?php
					echo wp_kses_post( wc_price( $subscription->subscription_total, array( 'currency' => $subscription->order_currency ) ) );
					?>
				</td>
			</tr>


		</table>
		<div class="clear"></div>
	</div>


	<?php if ( $subscription->can_be_editable( 'recurring_amount' ) ) : ?>
		<div class="wc-order-data-row wc-order-recalculate s wc-order-data-row-toggle">
			<button type="button" class="button button-primary recalculate-action"><?php esc_html_e( 'Recalculate', 'woocommerce' ); ?></button>
		</div>
		<div class="wc-order-data-row wc-order-add-item wc-order-data-row-toggle" style="display:none;">
			<button type="button" class="button cancel-action"><?php esc_html_e( 'Cancel', 'yith-woocommerce-subscription' ); ?></button>
			<button type="button"
					class="button button-primary save-action"><?php esc_html_e( 'Save', 'yith-woocommerce-subscription' ); ?></button>
		</div>

	<?php else : ?>
		<div class="wc-order-data-row wc-order-add-item wc-order-data-row-toggle">
			<span class="description"><?php echo  wc_help_tip( __( 'This payment method does not allow editing the subscription amount.', 'yith-woocommerce-subscription' ) ); ?><?php esc_html_e( 'This subscription is no editable.', 'yith-woocommerce-subscription' ); ?></span>
		</div>
	<?php endif ?>

</div>
