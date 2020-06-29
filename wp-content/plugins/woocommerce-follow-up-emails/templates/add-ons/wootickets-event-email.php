<?php global $wpdb; ?>

<p><b><?php echo esc_html( $customer ); ?></b> just booked an event.</p>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<thead>
	<tr>
		<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e('Ticket', 'follow_up_emails'); ?></th>
		<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e('Event', 'follow_up_emails'); ?></th>
		<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e('Paid', 'follow_up_emails'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	$customer   = WC_FUE_Compatibility::get_order_prop( $order, 'billing_first_name' ) .' '. WC_FUE_Compatibility::get_order_prop( $order, 'billing_last_name' );
	$email      = WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' );

	foreach( $order->get_items() as $item ):
		$product_id = $item['product_id'];
		$event_id   = get_post_meta( $product_id, '_tribe_wooticket_for_event', true );

		if ( !$event_id ) {
			continue;
		}

		$ticket_name    = get_the_title( $product_id );
		$url            = admin_url('edit.php?post_type=post&page=tickets-attendees&event_id='. $event_id);
		$event          = '<a href="'. $url .'">'. get_the_title( $event_id ) .'</a>';
		$amount         = WC_FUE_Compatibility::get_order_prop( $order, 'order_total' );
		$product        = WC_FUE_Compatibility::wc_get_product( $product_id );
		$amount         = $product->get_price();
		?>
		<tr>
			<td style="text-align:left; border: 1px solid #eee;"><?php echo wp_kses_post( $ticket_name .' &times; '. $item['qty'] ); ?></td>
			<td style="text-align:left; border: 1px solid #eee;"><?php echo wp_kses_post( $event ); ?></td>
			<td style="text-align:left; border: 1px solid #eee;"><?php echo wp_kses_post( wc_price( $amount ) ); ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
