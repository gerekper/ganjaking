<?php

$request = new YITH_Refund_Request( $post->ID );
if ( ! ( $request instanceof YITH_Refund_Request && $request->exists() ) ) {
	return;
}
$finished_request = apply_filters( 'ywcars_finished_request', ( 'ywcars-approved' == $request->status || 'ywcars-rejected' == $request->status || 'trash' == $request->status ), $request );
$order = wc_get_order( $request->order_id );
$order_id = $order->get_id();
$order_number = $order->get_order_number();
$order_total = $order->get_total() - $order->get_total_refunded();
$product = $request->product_id ? wc_get_product( $request->product_id ) : '';
$tax_enabled = wc_tax_enabled() && 'yes' == get_option( 'yith_wcars_enable_taxes' );
?>
<div class="ywcars_items_info">
	<?php
	$href       = admin_url( 'post.php?post=' . $order_id . '&action=edit' );
	$title      = $order_id == $order_number ? sprintf( esc_html__( 'Go to order #%d', 'yith-advanced-refund-system-for-woocommerce' ), $order_id ) : sprintf( esc_html__( 'Go to order %s', 'yith-advanced-refund-system-for-woocommerce' ), $order_number );
	$order_link = '<a href="' . $href . '" title="' . $title . '">';
    $order_link .= $order_id == $order_number ? '#' . $order_id . '</a>' : $order_number . '</a>';

	$status_title = ywcars_get_request_status_by_key( $request->status );
	$src          = YITH_WCARS_ASSETS_URL . 'images/' . $request->status . '.png';
	?>
    <input type="hidden" id="ywcars_request_id"     value="<?php echo $request->ID; ?>">
    <input type="hidden" id="ywcars_request_status" value="<?php echo $request->status; ?>">
    <input type="hidden" id="ywcars_request_is_closed" value="<?php echo $request->is_closed; ?>">
    <input type="hidden" id="ywcars_order_id"       value="<?php echo $request->order_id; ?>">


	<?php if ( 'trash' == $request->status ) : ?>
        <span class="ywcars_trash_status_icon"></span>
	<?php else : ?>
        <img title="<?php echo $status_title; ?>" src="<?php echo $src; ?>">
	<?php endif; ?>

    <h2 style="display: inline-block; vertical-align: super;"><?php printf( esc_html_x( 'Request %s for order %s', 'Request [request ID] for order [order ID]', 'yith-advanced-refund-system-for-woocommerce' ), '#' . $request->ID, $order_link );
		?></h2>
    <div class="ywcars_before_items_table">
		<?php do_action( 'ywcars_before_items_table_start', $request ); ?>
		<?php
		$customer = version_compare( WC()->version, '3.0.0', '<' ) ? $request->get_customer_link_legacy() : $request->get_customer_link();
		if ( $request->whole_order ) {
			$info = sprintf( esc_html__( '%s made a request for the whole order', 'yith-advanced-refund-system-for-woocommerce' ), $customer );
		} else {
			$span_product = $product ? '<span style="text-decoration: underline;">' . $product->get_title() . '</span>': '';
			$span_qty     = '<span title="' . esc_html__( 'Quantity requested', 'yith-advanced-refund-system-for-woocommerce' ) . '">' . $request->qty . '</span>';
			$info         = sprintf( esc_html__( '%s made a request for %s (%s)', 'yith-advanced-refund-system-for-woocommerce' ), $customer, $span_product, $span_qty );
		}
		?>
        <div><?php echo $info; ?></div>
		<?php do_action( 'ywcars_before_items_table_end', $request ); ?>
    </div>

    <div style="overflow: auto;">
        <table class="ywcars_items_table">
            <thead>
            <tr>
                <th><?php esc_html_e( 'Item', 'yith-advanced-refund-system-for-woocommerce' ); ?></th>
                <th><?php esc_html_e( 'Item value', 'yith-advanced-refund-system-for-woocommerce' ); ?></th>
				<?php if ( $tax_enabled ) : ?>
                    <th><?php esc_html_e( 'Tax per item', 'yith-advanced-refund-system-for-woocommerce' ); ?></th>
				<?php endif; ?>
                <th><?php esc_html_e( 'Qty', 'yith-advanced-refund-system-for-woocommerce' ); ?></th>
				<?php if ( $tax_enabled ) : ?>
                    <th class="ywcars_items_table_totals"><?php esc_html_e( 'Item total', 'yith-advanced-refund-system-for-woocommerce' ); ?></th>
                    <th class="ywcars_items_table_totals"><?php esc_html_e( 'Tax total', 'yith-advanced-refund-system-for-woocommerce' ); ?></th>
				<?php endif; ?>
                <th class="ywcars_items_table_totals"><?php esc_html_e( 'Order Total', 'yith-advanced-refund-system-for-woocommerce' ); ?></th>
                <th class="ywcars_items_table_totals ywcars_items_table_refund"><?php esc_html_e( 'Qty to refund', 'yith-advanced-refund-system-for-woocommerce' ); ?></th>
                <th class="ywcars_items_table_totals ywcars_items_table_refund"><?php esc_html_e( 'Total to be refunded',
						'yith-advanced-refund-system-for-woocommerce' ); ?></th>
            </tr>
            </thead>
            <tbody>
			<?php
			do_action( 'ywcars_items_table_tbody_start', $request, $tax_enabled );

			$items = $order->get_items( array( 'line_item', 'shipping', 'fee' ) );
			foreach ( $items as $item_id => $item ) {
				$item_type = version_compare( WC()->version, '3.0.0', '<' ) ? $item['type'] : $item->get_type();

				if ( 'line_item' == $item_type ) :
					$product = empty( $item['variation_id'] ) ? wc_get_product( $item['product_id'] ) : wc_get_product( $item['variation_id'] );

					if ( ! $product || ! $product->exists() ) {
						?>
                        <tr>
                            <td><?php esc_html_e( 'The requested product does not exist.', 'yith-advanced-refund-system-for-woocommerce' ); ?></td>
                        </tr>
						<?php
						continue;
					}

					$qty_requested = '0';
					if ( $request->whole_order ) {
						$qty_requested = $item['qty'];
					}
					if ( $item_id == $request->item_id ) {
						$qty_requested = $request->qty;
					}

					$item_value   = $item['line_total'] / $item['qty'];
					$total_refund = $item_value * $qty_requested;
					if ( $tax_enabled ) {
						$item_and_tax_value = ( $item['line_total'] + $item['line_tax'] ) / $item['qty'];
						$tax_value          = $item['line_tax'] / $item['qty'];
						$total_refund       = $item_and_tax_value * $qty_requested;
					}

					?>
                    <tr>
                        <td><?php echo $product->get_title(); ?></td>
                        <td><?php echo wc_price( $item_value, array( 'currency' => $order->get_currency() ) ); ?></td>
						<?php if ( $tax_enabled ) : ?>
                            <td><?php echo wc_price( $tax_value, array( 'currency' => $order->get_currency() ) ); ?></td>
						<?php endif; ?>
                        <td><?php echo $item['qty']; ?></td>
						<?php if ( $tax_enabled ) : ?>
                            <td class="ywcars_items_table_totals"><?php echo wc_price( $item['line_total'], array( 'currency' => $order->get_currency() ) ); ?></td>
                            <td class="ywcars_items_table_totals"><?php echo wc_price( $item['line_tax'], array( 'currency' => $order->get_currency() ) ); ?></td>
						<?php endif; ?>
                        <td class="ywcars_items_table_totals">
							<?php $total = $tax_enabled ? $item['line_total'] + $item['line_tax'] : $item['line_total'] ?>
                            <span><?php echo wc_price( $total, array( 'currency' => $order->get_currency() ) ); ?></span>
                        </td>
                        <td class="ywcars_items_table_totals ywcars_items_table_refund ywcars_item_data">
							<?php if ( ! YITH_Advanced_Refund_System_Request_Manager::order_has_wc_refunds( $order ) && ! $request->is_closed ) : ?>
                                <input type="number" class="ywcars_item_qty" value="<?php echo $finished_request ? '0' : $qty_requested; ?>"
                                       min="0"
                                       max="<?php echo $qty_requested; ?>"
                                       step="1" >
							<?php else : ?>
                                <input type="text" size="1" readonly class="ywcars_item_qty" value="<?php echo $qty_requested;?>">
							<?php endif; ?>
                            <input type="hidden" class="ywcars_item_id" value="<?php echo $item_id; ?>">
                            <input type="hidden" class="ywcars_item_value" value="<?php echo $item_value; ?>">
							<?php
							if ( $tax_enabled ) {
								$line_tax_data = maybe_unserialize( $item['line_tax_data'] );
								foreach ( $line_tax_data['total'] as $tax_id => $value ) {
									?>
                                    <input type="hidden" class="ywcars_item_tax" data-tax_id="<?php echo $tax_id; ?>"
                                           value="<?php echo (float) $value / $item['qty']; ?>">
									<?php
								}
								?>
                                <input type="hidden" class="ywcars_item_tax_value" value="<?php echo $tax_value; ?>">
								<?php
							}
							?>
                            <input type="hidden" class="ywcars_item_total" value="<?php echo $total_refund ?>">
                        </td>
                        <td class="ywcars_items_table_totals ywcars_items_table_refund ywcars_refund_subtotal_data"><?php echo wc_price( $total_refund, array( 'currency' => $order->get_currency() ) ); ?></td>
                    </tr>
				<?php else : ?>
                    <tr class="ywcars_non_line_item">
						<?php $item_name  = version_compare( WC()->version, '3.0.0', '<' ) ? $item['name'] : $item->get_name(); ?>
						<?php if ( 'shipping' == $item_type ) : ?>
							<?php $shipping_total = version_compare( WC()->version, '3.0.0', '<' ) ? (float) $item['cost'] : (float) $item->get_total(); ?>
							<?php
							$shipping_tax = 0;
							if ( $tax_enabled ) {
								if ( version_compare( WC()->version, '3.0.0', '<' ) ) {
									foreach ( maybe_unserialize( $item['taxes'] ) as $tax ) {
										$shipping_tax += (float) $tax;
									}
								} else {
									$shipping_tax = (float) $item->get_total_tax();
								}
							}
							?>
							<?php $shipping_sum = $tax_enabled ? $shipping_total + $shipping_tax : $shipping_total; ?>
                            <td><?php echo $item_name ? $item_name : esc_html__( 'Shipping', 'yith-advanced-refund-system-for-woocommerce' ); ?></td>
                            <td></td><td></td>
							<?php if ( $tax_enabled ) : ?>
                                <td></td>
                                <td class="ywcars_items_table_totals"><?php echo wc_price( $shipping_total, array( 'currency' => $order->get_currency() ) ); ?></td>
                                <td class="ywcars_items_table_totals"><?php echo wc_price( $shipping_tax, array( 'currency' => $order->get_currency() ) ); ?></td>
							<?php endif; ?>
                            <td class="ywcars_items_table_totals"><?php echo wc_price( $shipping_sum, array( 'currency' => $order->get_currency() ) ); ?></td>
                            <td class="ywcars_items_table_totals ywcars_items_table_refund ywcars_non_line_item_data">
								<?php if ( ! YITH_Advanced_Refund_System_Request_Manager::order_has_wc_refunds( $order ) && ! $request->is_closed ) : ?>
                                    <input class="ywcars_non_line_item_cb" type="checkbox">
								<?php else : ?>
                                    <input class="ywcars_non_line_item_cb" disabled type="checkbox">
								<?php endif; ?>
                                <input type="hidden" class="ywcars_item_id" value="<?php echo $item_id; ?>">
                                <input type="hidden" class="ywcars_item_value" value="<?php echo $shipping_total; ?>">
								<?php if ( $tax_enabled ) : ?>
                                    <?php
									$taxes_data = maybe_unserialize( $item['taxes'] );
									if ( empty( $taxes_data['total'] ) ) {
										$taxes = $order->get_items( 'tax' );
										foreach ( $taxes as $tax_item ) {
											?>
                                            <input type="hidden" class="ywcars_item_tax" data-tax_id="<?php echo $tax_item['rate_id']; ?>" value="0">
                                            <?php
										}
                                    } else {
										foreach ( $taxes_data['total'] as $tax_id => $value ) {
											?>
                                            <input type="hidden" class="ywcars_item_tax" data-tax_id="<?php echo $tax_id; ?>" value="<?php echo (float) $value; ?>">
											<?php
										}
                                    }
                                    ?>
                                    <input type="hidden" class="ywcars_item_tax_value" value="<?php echo $shipping_tax; ?>">
								<?php endif; ?>
                                <input type="hidden" class="ywcars_item_total" value="<?php echo $shipping_sum ?>">
                            </td>
                            <td class="ywcars_items_table_totals ywcars_items_table_refund ywcars_refund_subtotal_data"></td>
						<?php endif; ?>
						<?php if ( 'fee' == $item_type ) : ?>

							<?php $fee_total = version_compare( WC()->version, '3.0.0', '<' ) ? (float) $item['line_total'] : (float) $item->get_total(); ?>
							<?php $fee_tax   = version_compare( WC()->version, '3.0.0', '<' ) ? (float) $item['line_tax'] : (float) $item->get_total_tax(); ?>
							<?php $fee_sum = $tax_enabled ? $fee_total + $fee_tax : $fee_total; ?>
                            <td><?php echo $item_name ? $item_name : esc_html__( 'Fee', 'yith-advanced-refund-system-for-woocommerce' ); ?></td>
                            <td></td><td></td>
							<?php if ( $tax_enabled ) : ?>
                                <td></td>
                                <td class="ywcars_items_table_totals"><?php echo wc_price( $fee_total, array( 'currency' => $order->get_currency() ) ); ?></td>
                                <td class="ywcars_items_table_totals"><?php echo wc_price( $fee_tax, array( 'currency' => $order->get_currency() ) ); ?></td>
							<?php endif; ?>
                            <td class="ywcars_items_table_totals"><?php echo wc_price( $fee_sum, array( 'currency' => $order->get_currency() ) ); ?></td>
                            <td class="ywcars_items_table_totals ywcars_items_table_refund ywcars_non_line_item_data">
								<?php if ( ! YITH_Advanced_Refund_System_Request_Manager::order_has_wc_refunds( $order ) && ! $request->is_closed ) : ?>
                                    <input class="ywcars_non_line_item_cb" type="checkbox">
								<?php else : ?>
                                    <input class="ywcars_non_line_item_cb" disabled type="checkbox">
								<?php endif; ?>
                                <input type="hidden" class="ywcars_item_id" value="<?php echo $item_id; ?>">
                                <input type="hidden" class="ywcars_item_value" value="<?php echo $fee_total; ?>">
								<?php if ( $tax_enabled ) : ?>
                                    <input type="hidden" class="ywcars_item_tax_value" value="<?php echo $fee_tax; ?>">
								<?php endif; ?>
                                <input type="hidden" class="ywcars_item_total" value="<?php echo $fee_sum ?>">
                            </td>
                            <td class="ywcars_items_table_totals ywcars_items_table_refund ywcars_refund_subtotal_data"></td>
						<?php endif; ?>
                    </tr>
				<?php endif;
			}
			do_action( 'ywcars_items_table_tbody_end', $request, $tax_enabled );
			?>
            </tbody>
            <tfoot>
            <tr>
                <td></td><td></td>
				<?php if ( $tax_enabled ) : ?>
                    <td></td>
				<?php endif; ?>
                <td class="ywcars_items_table_totals"><?php esc_html_e( 'Totals', 'yith-advanced-refund-system-for-woocommerce' ); ?></td>
				<?php $order_tax = $order->get_total_tax(); ?>
				<?php if ( $tax_enabled ) : ?>
                    <td class="ywcars_items_table_totals"><?php echo wc_price( $order->get_total() -  $order_tax, array( 'currency' => $order->get_currency() ) ); ?></td>
                    <td class="ywcars_items_table_totals"><?php echo wc_price( $order_tax, array( 'currency' => $order->get_currency() ) ); ?></td>
				<?php endif; ?>
                <td class="ywcars_items_table_totals"><?php
					$order_total = $order->get_total() - $order->get_total_refunded();
					// If the option 'Enable taxes' is disabled but the order has old taxes set
					if ( $order_tax && 'yes' != get_option( 'yith_wcars_enable_taxes' ) ) {
						$order_total = $order_total - $order_tax;
						echo wc_price( $order_total, array( 'currency' => $order->get_currency() ) );
					} else {
						echo $order->get_formatted_order_total();
					}
					?></td>
                <td class="ywcars_items_table_totals ywcars_items_table_refund"></td>
                <td class="ywcars_items_table_totals ywcars_items_table_refund ywcars_refund_total_data"></td>
            </tr>
            </tfoot>
        </table>
    </div>

	<?php if ( YITH_Advanced_Refund_System_Request_Manager::order_has_wc_refunds( $order ) ) : ?>
        <div>
            <h4><?php esc_html_e( 'Refunds for this order have already been managed through WooCommerce.', 'yith-advanced-refund-system-for-woocommerce' ); ?></h4>
        </div>
	<?php endif; ?>

	<?php if ( $request->is_closed ) : ?>
        <div>
            <h4><?php esc_html_e( 'This request is currently closed.', 'yith-advanced-refund-system-for-woocommerce' ); ?></h4>
        </div>
	<?php endif; ?>

	<?php if ( ! YITH_Advanced_Refund_System_Request_Manager::order_has_wc_refunds( $order ) && ! $request->is_closed ) : ?>

        <div class="ywcars_after_items_table">
            <div>
                <p class="ywcars_after_items_table_block">
                <span>
                    <input id="ywcars_restock_items" type="checkbox">
                    <label for="ywcars_restock_items"><?php esc_html_e( 'Restock selected items?', 'yith-advanced-refund-system-for-woocommerce' ) ?></label>
                </span>
                </p>
                <p class="ywcars_after_items_table_block">
                <span>
                    <span><?php esc_html_e( 'Use a custom refund amount', 'yith-advanced-refund-system-for-woocommerce' ) ?></span>
                    <input type="text" id="refund_amount" class="ywcars_custom_refund_amount wc_input_price">
                </span>
                </p>
            </div>

            <div>
                <input type="hidden" id="ywcars_whole_order"      value="<?php echo $request->whole_order ? 'true' : 'false'; ?>">
                <input type="hidden" id="ywcars_refunded_item_id" value="<?php echo $request->item_id; ?>">
                <input type="hidden" id="refunded_amount"         value="<?php echo esc_attr( $order->get_total_refunded() ); ?>">
                <input type="hidden" id="ywcars_order_total"      value="<?php echo $order_total; ?>">
                <input type="hidden" id="ywcars_taxes_enabled"    value="<?php echo $tax_enabled; ?>">
                <input type="hidden" id="ywcars_refund_amount"    value="<?php echo $request->refund_total; ?>">
				<?php
				$payment_gateway = wc_get_payment_gateway_by_order( $order );

				$refund_amount            = wc_price( 0, array( 'currency' => $order->get_currency() ) );
				$gateway_supports_refunds = false !== $payment_gateway && $payment_gateway->supports( 'refunds' );
				$gateway_name             = false !== $payment_gateway ? ( ! empty( $payment_gateway->method_title ) ? $payment_gateway->method_title : $payment_gateway->get_title() ) : esc_html__( 'Payment Gateway', 'yith-advanced-refund-system-for-woocommerce' );

				$automatic_refund_button_classes = $gateway_supports_refunds ? 'button-primary do-api-refund' : 'button-primary tips disabled';
				$automatic_tip = $gateway_supports_refunds ? '' : 'data-tip="' . esc_attr__( 'The payment gateway used to place this order does not support automatic refunds.', 'yith-advanced-refund-system-for-woocommerce' ) . '"';
				$manual_tip = 'data-tip="' . esc_attr__( 'You will need to manually issue a refund through your payment gateway after using this.', 'yith-advanced-refund-system-for-woocommerce' ) . '"';
				?>
                <button id="ywcars_api_refund_button"
                        class="ywcars_request_action_button ywcars_accept_button button <?php echo $automatic_refund_button_classes; ?>" <?php echo $automatic_tip; ?>><?php
					printf( esc_html_x( 'Refund %s via %s', 'Refund [amount] via [gateway name]', 'yith-advanced-refund-system-for-woocommerce' ), $refund_amount, $gateway_name );
					?></button>
                <button id="ywcars_manual_refund_button"
                        class="ywcars_request_action_button ywcars_accept_button button button-primary do-manual-refund tips" <?php echo $manual_tip; ?>><?php
					printf( esc_html_x( 'Refund %s manually', 'Refund $amount manually', 'yith-advanced-refund-system-for-woocommerce' ), $refund_amount );
					?></button>
				<?php do_action( 'ywcars_after_refund_buttons', $request, $refund_amount, $tax_enabled ); ?>
                <button class="button button-secondary" id="ywcars_reject_request_button">
                    <span><?php esc_html_e( 'Reject request', 'yith-advanced-refund-system-for-woocommerce' ); ?></span>
                </button>
                <button class="button button-secondary" id="ywcars_processing_request_button">
                    <span><?php esc_html_e( 'Set to processing', 'yith-advanced-refund-system-for-woocommerce' ); ?></span>
                </button>
                <button class="button button-secondary" id="ywcars_on_hold_request_button">
                    <span><?php esc_html_e( 'Set to on hold', 'yith-advanced-refund-system-for-woocommerce' ); ?></span>
                </button>
				<?php do_action( 'ywcars_after_action_buttons', $request, $refund_amount, $tax_enabled ); ?>
            </div>
        </div>
	<?php endif; ?>
	<?php if ( $finished_request && ! $request->is_closed || 'yes' == get_option( 'yith_wcars_allow_closing_requests', 'no' ) && ! $request->is_closed ) : ?>
        <div>
            <button class="button button-secondary tips" id="ywcars_close_request_button"
                    data-tip="<?php esc_html_e( 'Close the request by stopping the messages system. This action prevents you from changing the request status.', 'yith-advanced-refund-system-for-woocommerce' ); ?>">
                <span><?php esc_html_e( 'Close this request', 'yith-advanced-refund-system-for-woocommerce' ); ?></span>
            </button>
        </div>
	<?php endif; ?>

</div>