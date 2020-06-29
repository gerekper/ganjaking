<hr>

<h3><?php esc_html_e('Notify me for Failed Subscription Payments', 'follow_up_emails'); ?></h3>

<?php wp_nonce_field( 'fue-update-settings-verify' ); ?>

<table class="form-table">
	<tr>
		<th><label for="subscription_failure_notification"><?php esc_html_e('Send Notification Email', 'follow_up_emails'); ?></label></th>
		<td>
			<input type="checkbox" name="subscription_failure_notification" id="subscription_failure_notification" value="1" <?php if (1 == get_option('fue_subscription_failure_notification', 0)) echo 'checked'; ?> />

		</td>
	</tr>
	<tr>
		<th>
			<label for="subscription_failure_notification_emails">
				<?php esc_html_e('Email Address', 'follow_up_emails'); ?>
			</label>
		</th>
		<td>
			<input type="text" name="subscription_failure_notification_emails" id="subscription_failure_notification_emails" value="<?php echo esc_attr(get_option('fue_subscription_failure_notification_emails', '')); ?>" />
			<span class="description"><?php esc_html_e('Comma-separated email addresses of recipients', 'follow_up_emails'); ?></span>
		</td>
	</tr>
</table>
