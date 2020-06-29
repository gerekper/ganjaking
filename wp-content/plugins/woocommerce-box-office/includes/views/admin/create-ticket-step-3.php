<?php
/**
 * Template when tickets were created.
 *
 * Render all created tickets.
 */
$tickets = $this->_created_tickets;
?>
<div class="wrap woocommerce">
	<h2><?php _e( 'Created Tickets', 'woocommerce-box-office' ); ?></h2>

	<div class="notice notice-success">
		<p>
			<?php echo esc_html( sprintf( _n( '%s ticket created.', '%s tickets created', sizeof( $tickets ), 'woocommerce-box-office' ), sizeof( $tickets ) ) ); ?>
		</p>
		<?php if ( ! empty( $order_url ) ) : ?>
		<p>
			<?php printf( __( '<a href="%s">View order</a>.', 'woocommerce-box-office' ), $order_url ); ?>
		</p>
		<?php endif; ?>
	</div>

	<table class="widefat wp-list-table">
		<thead>
			<tr>
				<th><?php _e( 'No', 'woocommerce-box-office' ); ?></th>
				<th><?php _e( 'Ticket', 'woocommerce-box-office' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ( $tickets as $index => $ticket ) : ?>
			<tr>
				<th scope="row"><?php echo esc_html( $index + 1 ); ?></th>
				<td class="ticket column-ticket has-row-actions column-primary">
					<strong><a href="<?php echo esc_url( admin_url( 'post.php?post=' . $ticket->id . '&action=edit' ) ); ?>"><?php echo esc_html( $ticket->title ); ?></a></strong>
					<div class="ticket-fields">
						<?php echo esc_html( wc_box_office_get_ticket_description( $ticket->id ) ); ?>
					</div>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
