<?php
$last_status = ( !empty( $email->meta['bookings_last_status'] ) ) ? $email->meta['bookings_last_status'] : '';
?>
<p class="form-field show-if-booking-status">
	<label for="meta_bookings_last_status"><?php esc_html_e('Last Status', 'follow_up_emails'); ?></label>
	<select name="meta[bookings_last_status]" id="meta_bookings_last_status">
		<option value="" <?php selected($last_status, ''); ?>><?php esc_html_e('Any status', 'follow_up_emails'); ?></option>
		<?php foreach ( self::$statuses as $status ): ?>
		<option value="<?php echo esc_attr( $status ); ?>" <?php selected($last_status, $status); ?>><?php echo esc_html( ucfirst( $status ) ); ?></option>
		<?php endforeach; ?>
	</select>
	<br/>
	<span class="description"><?php esc_html_e('Only send this email if the booking\'s last status matches the selected value', 'follow_up_emails'); ?></span>
</p>
