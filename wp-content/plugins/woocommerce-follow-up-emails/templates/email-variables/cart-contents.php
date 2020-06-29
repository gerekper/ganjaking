<table class="td" cellspacing="0" cellpadding="6" style="width: 100%; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; color: #737373; border: 1px solid #e4e4e4;" border="1">
	<thead><tr>
		<th class="td" scope="col" style="text-align: center; color: #737373; border: 1px solid #e4e4e4; padding: 12px;">&nbsp;</th>
		<th class="td" scope="col" style="text-align: left; color: #737373; border: 1px solid #e4e4e4; padding: 12px;"><?php esc_html_e('Product', 'follow_up_emails'); ?></th>
		<th class="td" scope="col" style="text-align: left; color: #737373; border: 1px solid #e4e4e4; padding: 12px;"><?php esc_html_e('Quantity', 'follow_up_emails'); ?></th>
		<th class="td" scope="col" style="text-align: left; color: #737373; border: 1px solid #e4e4e4; padding: 12px;"><?php esc_html_e('Price', 'follow_up_emails'); ?></th>
	</tr></thead>
	<tbody>
	<?php
	foreach ( $cart as $cart_item_key => $cart_item ):
		$product_id   = ($cart_item['variation_id']) ? $cart_item['variation_id'] : $cart_item['product_id'];
		$_product     = WC_FUE_Compatibility::wc_get_product( $product_id );

		if ( ! $_product ) {
			continue;
		}

		?>
		<tr class="order_item">
			<td class="td" style="text-align: center; vertical-align: middle; border: 1px solid #eee; font-family: 'Helvetica Neue', Helvetica,Roboto, Arial, sans-serif; word-wrap: break-word; color: #737373; padding: 12px;">
				<?php
				$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
				// Protocol agcnostic approach doesn't work for email clients
				$protocol  = is_ssl() ? 'https://' : 'http://';
				$thumbnail = str_replace( array( '//', 'https:https://', 'http:http://', 'http:https://' ), $protocol, $thumbnail );

				if ( ! $_product->is_visible() ) {
					printf( '<a href="%s">%s</a>', '#', wp_kses_post( $thumbnail ) );
				} else {
					printf( '<a href="%s">%s</a>', esc_url( $_product->get_permalink( $cart_item ) ), wp_kses_post( $thumbnail ) );
				}
				?>
			</td>
			<td class="td" style="text-align: left; vertical-align: middle; border: 1px solid #eee; font-family: 'Helvetica Neue', Helvetica,Roboto, Arial, sans-serif; word-wrap: break-word; color: #737373; padding: 12px;">
				<?php
				if ( ! $_product->is_visible() ) {
					echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key ) ) . '&nbsp;';
				} else {
					echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s </a>', esc_url( $_product->get_permalink( $cart_item ) ), $_product->get_title() ), $cart_item, $cart_item_key ) );
				}

				// Meta data
				echo wp_kses_post( fue_get_cart_item_data( $cart_item ) );
				?>
			</td>
			<td class="td" style="text-align: left; vertical-align: middle; border: 1px solid #eee; font-family: 'Helvetica Neue', Helvetica,Roboto, Arial, sans-serif; color: #737373; padding: 12px;">
				<?php echo esc_html( $cart_item['quantity'] ); ?>
			</td>
			<td class="td" style="text-align: left; vertical-align: middle; border: 1px solid #eee; font-family: 'Helvetica Neue', Helvetica,Roboto, Arial, sans-serif; color: #737373; padding: 12px;">
				<?php
				echo wp_kses_post( apply_filters( 'woocommerce_cart_item_price', wc_price( wc_get_price_to_display( $_product, array( 'qty' => $cart_item['quantity'] ) ) ), $cart_item, $cart_item_key ) );
				?>
			</td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
