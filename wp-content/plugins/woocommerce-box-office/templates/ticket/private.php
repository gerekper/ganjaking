<div class="ticket-private-content">
	<?php
	if ( $notice && $notice_type ) {
		wc_print_notice( $notice, $notice_type );
	}
	?>
	<?php if ( $show_email_form ) : ?>
	<p>
		<?php _e( 'If you\'ve purchased the ticket use the form below to send link for unlocking content to your email.', 'woocommerce-box-office' ); ?>
	</p>
	<form method="POST" action="#ticket-private-content">
		<input name="ticket_send_link_for_private_content" type="hidden" value="1" />
		<input name="ticket_product_id" type="hidden" value="<?php echo esc_attr( $product_id ); ?>" />
		<input name="private_content_id" type="hidden" value="<?php echo esc_attr( get_the_ID() ); ?>" />

		<p class="form-row">
			<label for="ticket-private-content-email">
				<?php _e( 'E-mail', 'woocommerce-box-office' ); ?>
			</label>

			<input id="ticket-private-content-email" name="ticket_email" type="email" value="<?php echo esc_attr( $email ); ?>" />
			<span class="description">
				<?php _e( 'Your email associated with the ticket.', 'woocommerce-box-office' ); ?>
			</span>
		</p>
		<p class="buttons">
			<input type="submit" class="button" value="<?php _e( 'Send me private link', 'woocommerce-box-office' ); ?>" />
		</p>
	</form>
	<?php endif; ?>

	<?php
	if ( $show_content ) {
		echo do_shortcode( $content );
	}
	?>
</div>
