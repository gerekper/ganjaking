<h2><?php echo apply_filters( 'woocommerce_box_office_order_tickets_title', __( 'Order Tickets', 'woocommerce-box-office' ) ); ?></h2>

<p class="ticket-list-description">
	<?php echo apply_filters( 'woocommerce_box_office_order_tickets_description', __( 'Edit each of your purchased tickets using the links below.', 'woocommerce-box-office' ) ); ?>
</p>

<dl class="purchased-tickets">
	<?php foreach ( $tickets as $ticket ) : ?>
		<dt>
			<a href="<?php echo esc_url( wcbo_get_my_ticket_url( $ticket->ID ) ); ?>">
				<?php echo esc_html( $ticket->post_title ); ?>
			</a>
			<?php if ( 'pending' === $ticket->post_status ) : ?>
				&mdash;
				<span class="pending"><?php _e( 'Pending', 'woocommerce-box-office' ); ?></span>
			<?php endif; ?>
		</dt>
		<dd class="description">
			<?php echo wc_box_office_get_ticket_description( $ticket->ID, $fields_format ); ?>
		</dd>
	<?php endforeach; ?>
</dl>
