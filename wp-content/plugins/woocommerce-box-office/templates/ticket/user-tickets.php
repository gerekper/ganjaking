<?php if ( ! empty( $title ) ) : ?>
	<h2><?php echo esc_html( apply_filters( 'woocommerce_box_office_my_account_tickets_title', $title ) ); ?></h2>
<?php endif; ?>

<?php $description = apply_filters( 'woocommerce_box_office_user_tickets_description', __( 'Edit each of your purchased tickets using the links below.', 'woocommerce-box-office' ) ); ?>

<?php if ( $description ) : ?>
	<p class="ticket-list-description"><?php echo esc_html( $description ); ?></p>
<?php endif; ?>

<dl class="purchased-tickets">
<?php foreach ( $tickets as $ticket ) : ?>
	<dt>
		<a href="<?php echo esc_url( wcbo_get_my_ticket_url( $ticket->ID ) ); ?>"><?php echo esc_html( $ticket->post_title ); ?></a>
	</dt>
	<dd class="description"><?php echo wc_box_office_get_ticket_description( $ticket->ID, $fields_format ); ?></dd>
<?php endforeach; ?>
</dl>
