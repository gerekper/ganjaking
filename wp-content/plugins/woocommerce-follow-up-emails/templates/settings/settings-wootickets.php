<hr>

<h3><?php esc_html_e('Event Booking Notification', 'follow_up_emails'); ?></h3>

<?php wp_nonce_field( 'fue-update-settings-verify' ); ?>

<table class="form-table">
	<tr>
		<th><label for="event_booking_notification"><?php esc_html_e('Send Notification Email', 'follow_up_emails'); ?></label></th>
		<td>
			<input type="checkbox" name="event_booking_notification" id="event_booking_notification" value="1" <?php checked( 1, get_option('fue_event_booking_notification', 0) ); ?> />
		</td>
	</tr>
	<tr class="wootickets">
		<th>
			<label for="event_booking_notification_emails">
				<?php esc_html_e('Email Address', 'follow_up_emails'); ?>
			</label>
		</th>
		<td>
			<input type="text" name="event_booking_notification_emails" id="event_booking_notification_emails" value="<?php echo esc_attr(get_option('fue_event_booking_notification_emails', '')); ?>" />
			<span class="description"><?php esc_html_e('Comma-separated email addresses of recipients', 'follow_up_emails'); ?></span>
		</td>
	</tr>
	<tr class="wootickets">
		<th><label for="event_booking_notification_schedule"><?php esc_html_e('Notification Schedule', 'follow_up_emails'); ?></label></th>
		<td>
			<select name="event_booking_notification_schedule" id="event_booking_notification_schedule">
				<option value="instant" <?php selected( 'instant', get_option( 'fue_event_booking_notification_schedule', 'instant' ) ); ?>><?php esc_html_e('Send notification on every booking', 'follow_up_emails'); ?></option>
				<option value="digest" <?php selected( 'digest', get_option( 'fue_event_booking_notification_schedule', 'instant' ) ); ?>><?php esc_html_e('Send notification once daily', 'follow_up_emails'); ?></option>
			</select>
		</td>
	</tr>
	<tr valign="top" class="wootickets time">
		<th><label for="event_booking_notification_time_hour"><?php esc_html_e('Preferred Time', 'follow_up_emails'); ?></label></th>
		<td>
			<?php
			$args = array(
				'post_type'     => 'shop_order',
				'post_status'   => array('wc-processing', 'wc-completed'),
				'fields'        => 'ids',
				'nopaging'      => true,
				'meta_query'    => array(
					array(
						'key'   => '_tribe_has_tickets',
						'value' => 1
					)
				),
				'date_query'    => array(
					array(
						'after'     => date( 'M d, Y', current_time('timestamp') ) .' 00:00:00',
						'before'    => date( 'M d, Y', current_time('timestamp') ) .' 00:00:00'
					),
					'inclusive' => true
				)
			);


			$time   = get_option('fue_event_booking_notification_time', '07:00 AM');
			$parts  = explode(':', $time);
			$parts2 = explode(' ', $parts[1]);
			$hour   = $parts[0];
			$minute = $parts2[0];
			$ampm   = $parts2[1];
			?>
			<select name="event_booking_notification_time_hour" id="event_booking_notification_time_hour">
				<?php
				for ($x = 1; $x <= 12; $x++):
					$val = ($x >= 10) ? $x : '0'.$x;
					?>
					<option value="<?php echo esc_attr( $val ); ?>" <?php selected($hour, $val); ?>><?php echo esc_html( $val ); ?></option>
				<?php endfor; ?>
			</select>

			<select name="event_booking_notification_time_minute" id="event_booking_notification_time_minute">
				<?php
				for ($x = 0; $x <= 55; $x+=15):
					$val = ($x >= 10) ? $x : '0'. $x;
					?>
					<option value="<?php echo esc_attr( $val ); ?>" <?php selected($minute, $val); ?>><?php echo esc_html( $val ); ?></option>
				<?php endfor; ?>
			</select>

			<select name="event_booking_notification_time_ampm" id="event_booking_notification_time_ampm">
				<option value="AM" <?php selected($ampm, 'AM'); ?>>AM</option>
				<option value="PM" <?php selected($ampm, 'PM'); ?>>PM</option>
			</select>
		</td>
	</tr>
</table>
<script>
jQuery(document).ready(function($) {
	$( '#event_booking_notification' ).on( 'change', function() {
		if ( $(this).is(":checked") ) {
			$("tr.wootickets").show();
		} else {
			$("tr.wootickets").hide();
		}
	} ).trigger( 'change' );

	$( '#event_booking_notification_schedule' ).on( 'change', function() {
		$("tr.wootickets.time").hide();

		if ( $(this).val() == "digest" ) {
			$("tr.wootickets.time").show();
		}
	} ).trigger( 'change' );
});
</script>
