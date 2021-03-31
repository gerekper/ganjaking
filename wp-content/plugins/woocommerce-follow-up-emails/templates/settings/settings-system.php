<style type="text/css">
	.red-pill {
		font-size: 10px;
		font-family: Verdana, Tahoma, Arial;
		font-weight: bold;
		display: inline-block;
		margin-left: 5px;
		background: #f00;
		color: #fff;
		padding: 0px 8px;
		border-radius: 20px;
		vertical-align: super;
	}
</style>
<form action="admin-post.php" method="post" enctype="multipart/form-data">

	<?php wp_nonce_field( 'fue-update-settings-verify' ); ?>

	<h3><?php esc_html_e('Permissions', 'follow_up_emails'); ?></h3>

	<p><?php esc_html_e('Select the User Roles that will be given permission to manage Follow-Up Emails.', 'follow_up_emails'); ?></p>

	<table class="form-table">
		<tbody>
		<tr valign="top">
			<th><label for="roles"><?php esc_html_e('Roles', 'follow_up_emails'); ?></label></th>
			<td>
				<select name="roles[]" id="roles" multiple style="width: 400px;">
					<?php
					$roles = get_editable_roles();
					foreach ( $roles as $key => $role ) {
						$selected = false;
						$readonly = '';
						if (array_key_exists('manage_follow_up_emails', $role['capabilities'])) {
							$selected = true;

							if ( $key == 'administrator' ) {
								$readonly = 'readonly';
							}
						}
						echo '<option value="'. esc_attr( $key ) .'" '. esc_attr( selected($selected, true, false) ) .'>'. esc_html( $role['name'] ) .'</option>';

					}
					?>
				</select>
				<script>jQuery("#roles").select2();</script>
			</td>
		</tr>
		</tbody>
	</table>

	<hr>

	<h3><?php esc_html_e('Daily Emails Summary', 'follow_up_emails'); ?></h3>

	<p><?php esc_html_e('Turn on a daily summary of all emails sent to users, and sent the email addresses that you want to be notified with this summary.', 'follow_up_emails'); ?></p>

	<table class="form-table">
		<tbody>
		<tr valign="top">
			<th><label for="enable_daily_summary"><?php esc_html_e('Enable', 'follow_up_emails'); ?></label></th>
			<td>
				<input type="checkbox" name="enable_daily_summary" id="enable_daily_summary" value="yes" <?php checked( 'yes', $enable_daily_summary ); ?> />
				<span class="description"><?php esc_html_e('Enable the Daily Email Summary', 'follow_up_emails'); ?></span>
			</td>
		</tr>
		<tr valign="top" class="summary_row">
			<th><label for="daily_emails"><?php esc_html_e('Email Address(es)', 'follow_up_emails'); ?></label></th>
			<td>
				<input type="text" name="daily_emails" id="daily_emails" value="<?php echo esc_attr( get_option('fue_daily_emails', '') ); ?>" />
				<span class="description"><?php esc_html_e('comma separated', 'follow_up_emails'); ?></span>
			</td>
		</tr>
		<tr valign="top" class="summary_row">
			<th><label for="daily_emails_time_hour"><?php esc_html_e('Preferred Time', 'follow_up_emails'); ?></label></th>
			<td>
				<?php
				$time   = get_option('fue_daily_emails_time', '12:00 AM');
				$parts  = explode(':', $time);
				$parts2 = explode(' ', $parts[1]);
				$hour   = $parts[0];
				$minute = $parts2[0];
				$ampm   = $parts2[1];
				?>
				<select name="daily_emails_time_hour" id="daily_emails_time_hour">
					<?php
					for ($x = 1; $x <= 12; $x++):
						$val = ($x >= 10) ? $x : '0'.$x;
						?>
						<option value="<?php echo esc_attr( $val ); ?>" <?php selected($hour, $val); ?>><?php echo esc_html( $val ); ?></option>
					<?php endfor; ?>
				</select>

				<select name="daily_emails_time_minute" id="daily_emails_time_minute">
					<?php
					for ($x = 0; $x <= 55; $x+=15):
						$val = ($x >= 10) ? $x : '0'. $x;
						?>
						<option value="<?php echo esc_attr( $val ); ?>" <?php selected($minute, $val); ?>><?php echo esc_html( $val ); ?></option>
					<?php endfor; ?>
				</select>

				<select name="daily_emails_time_ampm" id="daily_emails_time_ampm">
					<option value="AM" <?php selected($ampm, 'AM'); ?>>AM</option>
					<option value="PM" <?php selected($ampm, 'PM'); ?>>PM</option>
				</select>
			</td>
		</tr>
		</tbody>
	</table>

	<hr>

	<h3><?php esc_html_e('Email Settings', 'follow_up_emails'); ?></h3>

	<p><?php esc_html_e('You can change the default from and reply-to name and email for all your emails. You can also customize these on every individual email.', 'follow_up_emails'); ?></p>

	<table class="form-table">
		<tbody>
		<tr valign="top">
			<th class="titledesc">
				<label for="staging"><?php esc_html_e( 'Staging mode', 'follow_up_emails' ); ?></label>
			</th>
			<td>
				<input type="checkbox" name="staging" id="staging" value="yes" <?php checked( 'yes', $staging ); ?> />
				<p class="description"><?php esc_html_e( 'All emails will be prevented from being sent out.', 'follow_up_emails' ); ?></p>
			</td>
		</tr>
		<tr valign="top">
			<th>
				<label for="bcc"><?php esc_html_e('BCC', 'follow_up_emails'); ?></label>
			</th>
			<td>
				<input type="text" name="bcc" id="bcc" value="<?php echo esc_attr( $bcc ); ?>" />
				<p class="description"><?php esc_html_e('All emails will be blind carbon copied to this address.', 'follow_up_emails'); ?></p>
			</td>
		</tr>
		<tr valign="top">
			<th>
				<label for="from_name"><?php esc_html_e('From/Reply-To Name', 'follow_up_emails'); ?></label>
			</th>
			<td>
				<input type="text" name="from_name" id="from_name" value="<?php echo esc_attr( $from_name ); ?>" />
				<p class="description"><?php esc_html_e('The name that your emails will come from and replied to.', 'follow_up_emails'); ?></p>
			</td>
		</tr>
		<tr valign="top">
			<th>
				<label for="from_email"><?php esc_html_e('From/Reply-To Email', 'follow_up_emails'); ?></label>
			</th>
			<td>
				<input type="text" name="from_email" id="from_email" value="<?php echo esc_attr( $from ); ?>" />
				<p class="description"><?php esc_html_e('The email address that your emails will come from and replied to.', 'follow_up_emails'); ?></p>
			</td>
		</tr>
		</tbody>
	</table>

	<hr>

	<h3><?php esc_html_e('Bounce Settings', 'follow_up_emails'); ?></h3>

	<p><?php esc_html_e('Which email address should all of your bounced emails be sent to? No premium version needed.', 'follow_up_emails'); ?></p>

	<table id="emails_form" class="form-table">
		<tbody>
		<tr valign="top">
			<th class="titledesc">
				<label for="bounce_email"><?php esc_html_e('Bounce Address', 'follow_up_emails'); ?></label>
			</th>
			<td>
				<input type="text" name="bounce[email]" id="bounce_email" value="<?php echo esc_attr( $bounce['email'] ); ?>" />
				<p class="description"><?php esc_html_e('Undelivered emails will be sent to this address.', 'follow_up_emails'); ?></p>
			</td>
		</tr>
		<tr valign="top">
			<th class="titledesc">
				<label for="bounce_handling"><?php esc_html_e('Automatic Bounce Handling', 'follow_up_emails'); ?></label>
			</th>
			<td>
				<input type="checkbox" name="bounce[handle_bounces]" id="bounce_handling" value="1" <?php checked( 1, $bounce['handle_bounces'] ); ?> />
			</td>
		</tr>
		<tr valign="top" class="bounce_enabled">
			<td colspan="2">
				<?php esc_html_e('To enable the automatic handling of bounced emails, enter the POP3 account of the bounce address above.', 'follow_up_emails'); ?>
			</td>
		</tr>
		<tr valign="top" class="bounce_enabled">
			<th class="titledesc">
				<label for="bounce_server"><?php esc_html_e('Server Address', 'follow_up_emails'); ?></label>
			</th>
			<td>
				<input type="text" name="bounce[server]" id="bounce_server" value="<?php echo esc_attr( $bounce['server'] ); ?>" />
			</td>
		</tr>
		<tr valign="top" class="bounce_enabled">
			<th class="titledesc">
				<label for="bounce_port"><?php esc_html_e('Port', 'follow_up_emails'); ?></label>
			</th>
			<td>
				<input type="text" name="bounce[port]" id="bounce_port" size="3" value="<?php echo esc_attr( $bounce['port'] ); ?>" />
			</td>
		</tr>
		<tr valign="top" class="bounce_enabled">
			<th class="titledesc">
				<label for="bounce_ssl"><?php esc_html_e('Use SSL', 'follow_up_emails'); ?></label>
			</th>
			<td>
				<input type="checkbox" name="bounce[ssl]" id="bounce_ssl" value="1" <?php checked( 1, $bounce['ssl'] ); ?> />
			</td>
		</tr>
		<tr valign="top" class="bounce_enabled">
			<th class="titledesc">
				<label for="bounce_username"><?php esc_html_e('Username', 'follow_up_emails'); ?></label>
			</th>
			<td>
				<input type="text" name="bounce[username]" id="bounce_username" value="<?php echo esc_attr( $bounce['username'] ); ?>" />
			</td>
		</tr>
		<tr valign="top" class="bounce_enabled">
			<th class="titledesc">
				<label for="bounce_password"><?php esc_html_e('Password', 'follow_up_emails'); ?></label>
			</th>
			<td>
				<input type="password" name="bounce[password]" id="bounce_password" value="<?php echo esc_attr( $bounce['password'] ); ?>" />
			</td>
		</tr>
		<tr valign="top" class="bounce_enabled">
			<th class="titledesc">
				<label for="bounce_delete_messages"><?php esc_html_e('Delete Messages', 'follow_up_emails'); ?></label>
			</th>
			<td>
				<input type="checkbox" name="bounce[delete_messages]" id="bounce_delete_messages" value="1" <?php checked( 1, $bounce['delete_messages'] ); ?> />
				<span class="description"><?php esc_html_e('Delete emails to keep the mailbox clean', 'follow_up_emails'); ?></span>
			</td>
		</tr>
		<tr valign="top" class="bounce_enabled">
			<th class="titledesc">
				<label for="bounce_soft_bounce_resend_interval"><?php esc_html_e('Soft Bounces', 'follow_up_emails'); ?></label>
			</th>
			<td>
				<?php
				printf(
					esc_html__('Attempt to resend up to %s times with an interval of %s minutes between each send before marking as a Hard Bounce.', 'follow_up_emails'),
					'<input type="number" name="bounce[soft_bounce_resend_limit]" id="bounce_soft_bounce_resend_limit" style="width: 50px;" value="'. esc_attr( $bounce['soft_bounce_resend_limit'] ) .'" />',
					'<input type="number" name="bounce[soft_bounce_resend_interval]" id="bounce_soft_bounce_resend_interval" style="width: 50px;" value="'. esc_attr( $bounce['soft_bounce_resend_interval'] ) .'" />'
				);
				?>
			</td>
		</tr>
		</tbody>
	</table>

	<div class="submit" style="width: auto;">
		<input class="button button-secondary test-bounce" type="button" value="<?php esc_attr_e('Test Bounce Settings', 'follow_up_emails'); ?>" data-nonce="<?php echo esc_attr( wp_create_nonce( 'test_bounce' ) ); ?>" />
		<div class="spinner test-bounce-spinner" style="float: none;"></div>
		<div class="test-bounce-status" style="display: none;"><?php esc_html_e('Sending test email...', 'follow_up_emails'); ?></div>
	</div>

	<hr>

	<h3><?php esc_html_e('Single Emails Sending Schedule', 'follow_up_emails'); ?></h3>
	<p><strong><?php esc_html_e('Sending manual emails at to large numbers of recipients could cause mail server issues with your host. For example, Gmail limits you to 500 sends per day to limit spam.', 'follow_up_emails'); ?></strong></p>

	<p>
		<input type="checkbox" name="email_batch_enabled" value="1" <?php checked( 1, $email_batches ); ?> />
		<?php
		printf(
			esc_html__( 'Send manual emails in batches of %s emails every %s minutes', 'follow_up_emails' ),
			'<input type="text" name="emails_per_batch" value="'. esc_attr( $emails_per_batch ) .'" size="3" />',
			'<input type="text" name="email_batch_interval" value="'. esc_attr( $email_batch_interval ) .'" size="2" />'
		);
		?>
	</p>

	<hr/>

	<!-- Future location of reporting data improvement settings -->

	<?php do_action( 'fue_settings_system' ); ?>
	<?php do_action( 'fue_settings_crm' ); ?>
	<?php do_action( 'fue_settings_email' ); ?>

	<p class="submit">
		<input type="hidden" name="action" value="fue_followup_save_settings" />
		<input type="hidden" name="section" value="<?php echo esc_attr( $tab ); ?>" />
		<input type="submit" name="save" value="<?php esc_attr_e('Save Settings', 'follow_up_emails'); ?>" class="button-primary" />
	</p>

</form>
<script>
	jQuery(document).ready(function($) {
		$( '#enable_daily_summary' ).on( 'change', function() {
			if ( $(this).is(":checked") ) {
				$(".summary_row").show();
			} else {
				$(".summary_row").hide();
			}
		} ).trigger( 'change' );
	});
</script>
