<form action="" method="post">

	<?php wp_nonce_field( 'woocommerce_box_office_tools_send_emails', 'tools_send_emails_nonce' ); ?>
	<input type="hidden" name="post_type" value="event_ticket">
	<input type="hidden" name="page" value="ticket_tools">
	<input type="hidden" name="tab" value="email">
	<input type="hidden" name="action" value="email_tickets">

	<p><?php _e( 'Send an email to all individuals who have booked the following ticket:', 'woocommerce-box-office' ); ?></p>

	<select name="product_id" id="product-id" class="chosen_select ticket-product-select" style="width:300px">
		<option value=""><?php _e( 'Select ticket product', 'woocommerce-box-office' ); ?></option>
		<?php foreach ( wc_box_office_get_all_ticket_products() as $product ) : ?>
			<option value="<?php echo esc_attr( $product->ID ); ?>"><?php echo esc_html( $product->post_title ); ?></option>
		<?php endforeach; ?>
	</select>

	<hr class="ticket-tools-hr"/>

	<p class="description">
		<?php printf( __( 'Add ticket fields to your email subject or content by inserting the field label like this: %1$s - e.g. %2$s. Use %3$s for the ticket edit link.', 'woocommerce-box-office' ), '<code>{Label}</code>', '<code>{First Name}</code>', '<code>{ticket_link}</code>' ); ?>
	</p>

	<p>
		<input
			type="text"
			class="large-text"
			id="email-subject"
			name="email_subject"
			placeholder="<?php _e( 'Email subject', 'woocommerce-box-office' ); ?>"
			value="<?php ! empty( $_POST['email_subject'] ) ? esc_attr( $_POST['email_subject'] ) : ''; ?>">
	</p>

	<p>
	<?php
	wp_editor( '', 'ticket_email_editor', array(
		'wpautop'       => true,
		'media_buttons' => true,
		'textarea_name' => 'email_body',
		'textarea_rows' => 15,
		'editor_class'  => 'attendee_email_editor',
		'teeny'         => false,
		'dfw'           => false,
		'tinymce'       => true,
		'quicktags'     => false,
	) );
	?>
	</p>

	<p class="buttons">
		<input type="button" id="preview-email" value="<?php _e( 'Preview email', 'woocommerce-box-office' ); ?>" class="button-secondary">
		<input type="submit" value="<?php _e( 'Send to all ticket holders', 'woocommerce-box-office' ); ?>" class="button-primary">
	</p>

	<div id="email-preview-container"></div>
</form>

<hr class="ticket-tools-hr"/>

<?php require_once( WCBO()->dir . 'includes/views/admin/tools-email-history.php' ); ?>
