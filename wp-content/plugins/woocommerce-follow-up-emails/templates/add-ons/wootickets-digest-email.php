<?php global $wpdb; ?>
<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<thead>
	<tr>
		<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e('Customer', 'follow_up_emails'); ?></th>
		<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e('Email Address', 'follow_up_emails'); ?></th>
		<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e('Event', 'follow_up_emails'); ?></th>
		<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e('Ticket', 'follow_up_emails'); ?></th>
		<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e('Paid', 'follow_up_emails'); ?></th>
		<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e('Date', 'follow_up_emails'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php
	foreach( $tickets as $_id ):
		$post = get_post( $_id );

		if ( $post->post_type == 'shop_order' ) {
			$order      = WC_FUE_Compatibility::wc_get_order( $_id );
			$customer   = WC_FUE_Compatibility::get_order_prop( $order, 'billing_first_name' ) .' '. WC_FUE_Compatibility::get_order_prop( $order, 'billing_last_name' );
			$email      = WC_FUE_Compatibility::get_order_prop( $order, 'billing_email' );
			$amount     = WC_FUE_Compatibility::get_order_prop( $order, 'order_total' );
			$event      = '-';
			$ticket_name= '-';

			$ticket_id  = $this->get_ticket_id_by_order_id( $_id );

			if ( $ticket_id ) {
				$event_id       = get_post_meta( $ticket_id, '_tribe_wooticket_event', true );
				$ticket_name    = get_the_title( get_post_meta( $ticket_id, '_tribe_wooticket_product', true ) );
				$url            = admin_url('edit.php?post_type=post&page=tickets-attendees&event_id='. $event_id);
				$event          = '<a href="'. $url .'">'. get_the_title( $event_id ) .'</a>';
			}

		} elseif ( $post->post_type == 'tribe_rsvp_attendees' ) {
			$customer       = get_post_meta( $_id, '_tribe_rsvp_full_name', true );
			$email          = get_post_meta( $_id, '_tribe_rsvp_email', true );
			$amount         = 0;
			$event_id       = get_post_meta( $_id, '_tribe_rsvp_event', true );
			$ticket_name    = get_the_title( get_post_meta( $_id, '_tribe_rsvp_product', true ) );
			$url            = admin_url('edit.php?post_type=post&page=tickets-attendees&event_id='. $event_id);
			$event          = '<a href="'. $url .'">'. get_the_title( $event_id ) .'</a>';
		} else {
			continue;
		}
	?>
	<tr>
		<td style="text-align:left; border: 1px solid #eee;"><?php echo wp_kses_post( $customer ); ?></td>
		<td style="text-align:left; border: 1px solid #eee;"><?php echo wp_kses_post( $email ); ?></td>
		<td style="text-align:left; border: 1px solid #eee;"><?php echo wp_kses_post( $event ); ?></td>
		<td style="text-align:left; border: 1px solid #eee;"><?php echo wp_kses_post( $ticket_name ); ?></td>
		<td style="text-align:left; border: 1px solid #eee;"><?php echo wp_kses_post( wc_price( $amount ) ); ?></td>
		<td style="text-align:left; border: 1px solid #eee;"><?php echo esc_html( $post->post_date ); ?></td>
	</tr>
	<?php endforeach; ?>
	</tbody>
</table>
