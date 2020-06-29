<form action="admin-post.php" method="post" enctype="multipart/form-data">
	<?php wp_nonce_field( 'fue-update-settings-verify' ); ?>

	<input type="hidden" name="action" value="fue_followup_save_settings" />
	<input type="hidden" name="section" value="<?php echo esc_attr( $tab ); ?>" />

	<h3><?php esc_html_e('Upload CSV of Emails', 'follow_up_emails'); ?></h3>

	<p><?php echo wp_kses_post( sprintf( __( 'Import your existing mailing lists and email addresses. Then go to <a href="%s">Subscribers</a> to assign to lists and manage your addresses.', 'follow_up_emails'), 'admin.php?page=followup-emails-subscribers' ) ); ?></p>    

	<p class="form-field">
		<input type="file" name="csv" />
	</p>
	<p class="submit">
		<input type="submit" class="button-primary" name="upload" value="<?php esc_attr_e('Upload', 'follow_up_emails'); ?>" />
	</p>

	<hr>

	<h3><?php esc_html_e('Page Endpoints', 'follow_up_emails'); ?></h3>

	<table class="form-table">
		<tr>
			<th scope="row"><?php esc_html_e('Unsubscribe Page', 'follow_up_emails'); ?></th>
			<td><input type="text" name="unsubscribe_endpoint" id="unsubscribe_endpoint" value="<?php echo esc_attr( get_option( 'fue_unsubscribe_endpoint', 'unsubscribe' ) ); ?>" /></td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('Email Subscriptions Page', 'follow_up_emails'); ?></th>
			<td><input type="text" name="email_subscriptions_endpoint" id="email_subscriptions_endpoint" value="<?php echo esc_attr( get_option( 'fue_email_subscriptions_endpoint', 'email-subscriptions' ) ); ?>" /></td>
		</tr>
		<tr>
			<th scope="row"><?php esc_html_e('Email Preferences Page', 'follow_up_emails'); ?></th>
			<td><input type="text" name="email_preferences_endpoint" id="email_preferences_endpoint" value="<?php echo esc_attr( get_option( 'fue_email_preferences_endpoint', 'email-preferences' ) ); ?>" /></td>
		</tr>
	</table>

	<?php do_action('fue_settings_subscribers'); ?>

	<p class="submit">
		<input type="submit" name="save" value="<?php esc_attr_e('Save Settings', 'follow_up_emails'); ?>" class="button-primary" />
	</p>

</form>
