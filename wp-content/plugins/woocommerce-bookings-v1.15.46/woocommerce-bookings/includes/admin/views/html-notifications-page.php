<div class="wrap woocommerce">
	<h2><?php esc_html_e( 'Send Notification', 'woocommerce-bookings' ); ?></h2>

	<p>
	<?php
	/* translators: 1: starting strong tag 2: closing strong tag 3: starting a href tag 4: closing a href tag */
	echo sprintf( esc_html__( 'You may send an email notification to all customers who have a %1$sfuture%2$s booking for a particular product. This will use the default template specified under %3$sWooCommerce > Settings > Emails%4$s.', 'woocommerce-bookings' ), '<strong>', '</strong>', '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=email' ) ) . '">', '</a>' );
	?>
	</p>

	<form method="POST">
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">
						<label for="notification_product_id"><?php esc_html_e( 'Booking Product', 'woocommerce-bookings' ); ?></label>
					</th>
					<td>
						<select id="notification_product_id" name="notification_product_id">
							<option value=""><?php esc_html_e( 'Select a booking product...', 'woocommerce-bookings' ); ?></option>
							<?php foreach ( $booking_products as $product ) : ?>
								<option value="<?php echo esc_attr( $product->get_id() ); ?>"><?php echo esc_html( $product->get_title() ); ?></option>
							<?php endforeach; ?>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="notification_subject"><?php esc_html_e( 'Subject', 'woocommerce-bookings' ); ?></label>
					</th>
					<td>
						<input type="text" placeholder="<?php esc_html_e( 'Email subject', 'woocommerce-bookings' ); ?>" name="notification_subject" id="notification_subject" />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="notification_message"><?php esc_html_e( 'Message', 'woocommerce-bookings' ); ?></label>
					</th>
					<td>
						<textarea id="notification_message" name="notification_message" class="large-text code" placeholder="<?php esc_attr_e( 'The message you wish to send', 'woocommerce-bookings' ); ?>"></textarea>
						<span class="description"><?php esc_html_e( 'The following tags can be inserted in your message/subject and will be replaced dynamically', 'woocommerce-bookings' ); ?>: <code>{booking_id} {product_title} {order_date} {order_number} {customer_name} {customer_first_name} {customer_last_name}</code></span>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<?php esc_html_e( 'Attachment', 'woocommerce-bookings' ); ?>
					</th>
					<td>
						<label><input type="checkbox" name="notification_ics" id="notification_ics" /> <?php
							/* translators: %s: file extension */
							printf( esc_html__( 'Attach %s file', 'woocommerce-bookings' ), '<code>.ics</code>' ); 
						?></label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">&nbsp;</th>
					<td>
						<input type="submit" name="send" class="button-primary" value="<?php esc_attr_e( 'Send Notification', 'woocommerce-bookings' ); ?>" />
						<?php wp_nonce_field( 'send_booking_notification' ); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</form>
</div>
