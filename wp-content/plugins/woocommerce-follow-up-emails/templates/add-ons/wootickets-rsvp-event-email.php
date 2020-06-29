<?php
global $wpdb;

$customer       = get_post_meta( $attendee_id, '_tribe_rsvp_full_name', true );
$email          = get_post_meta( $attendee_id, '_tribe_rsvp_email', true );
$items          = $this->get_rsvp_tickets( $order_id );
?>

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

	foreach( $items as $item ):
		$product_id = $item['product_id'];
		$event_id   = get_post_meta( $product_id, '_tribe_rsvp_for_event', true );

		if ( !$event_id ) {
			continue;
		}

		$ticket_name    = get_the_title( $product_id );
		$url            = admin_url('edit.php?post_type=post&page=tickets-attendees&event_id='. $event_id);
		$event          = '<a href="'. $url .'">'. get_the_title( $event_id ) .'</a>';
		$amount         = 0;
		?>
		<tr>
			<td style="text-align:left; border: 1px solid #eee;"><?php echo wp_kses_post( $ticket_name .' &times; '. $item['qty'] ); ?></td>
			<td style="text-align:left; border: 1px solid #eee;"><?php echo wp_kses_post( $event ); ?></td>
			<td style="text-align:left; border: 1px solid #eee;"><?php echo wp_kses_post( wc_price( $amount ) ); ?></td>
		</tr>
	<?php endforeach; ?>
	</tbody>
</table>
