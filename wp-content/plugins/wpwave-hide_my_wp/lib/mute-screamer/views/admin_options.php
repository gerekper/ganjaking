<?php if( ! defined( 'ABSPATH' ) ) exit; ?>

<div class="wrap">
	<div class="icon32" id="icon-options-general"><br /></div>
	<h2><?php _e( 'Mute Screamer Settings', 'mute-screamer' ); ?></h2>

	<form action="options.php" method="post">
		<?php settings_fields( 'hmwp_ms_options' ); ?>

		<h3><?php _e( 'General Settings', 'mute-screamer' ); ?></h3>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><?php _e( 'WordPress Admin', 'mute-screamer' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span><?php _e( 'WordPress Admin', 'mute-screamer' ); ?></span></legend>
							<label for="hmwp_ms_enable_admin">
								<input type="checkbox" value="1" id="hmwp_ms_enable_admin" name="hmwp_ms_options[enable_admin]" <?php checked( '1', $enable_admin ); ?> />
								<?php _e( 'Enable Mute Screamer for the WordPress admin', 'mute-screamer' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e( 'Intrusion Logs', 'mute-screamer' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span><?php _e( 'Intrusion Logs', 'mute-screamer' ); ?></span></legend>
							<label for="hmwp_ms_enable_intrusion_logs">
								<input type="checkbox" value="1" id="hmwp_ms_enable_intrusion_logs" name="hmwp_ms_options[enable_intrusion_logs]" <?php checked( '1', $enable_intrusion_logs ); ?> />
								<?php _e( 'Enable logging for intrusion attempts', 'mute-screamer' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e( 'Automatic Updates', 'mute-screamer' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span><?php _e( 'Automatic Updates', 'mute-screamer' ); ?></span></legend>
							<label for="hmwp_ms_enable_automatic_updates">
								<input type="checkbox" value="1" id="hmwp_ms_enable_automatic_updates" name="hmwp_ms_options[enable_automatic_updates]" <?php checked( '1', $enable_automatic_updates ); ?> />
								<?php _e( 'Enable automatic updates for default_filter.xml and Converter.php', 'mute-screamer' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>
			</tbody>
		</table>

		<h3><?php _e( 'Email', 'mute-screamer' ); ?></h3>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><label for="hmwp_ms_email"><?php _e( 'E-mail address', 'mute-screamer' ); ?></label></th>
					<td>
						<input type="text" class="regular-text" value="<?php echo esc_attr( $email ); ?>" id="hmwp_ms_email" name="hmwp_ms_options[email]" />
						<span class="description"><?php _e( 'This address is used to send intrusion alerts.', 'mute-screamer' ); ?></span>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e( 'E-mail Notifications', 'mute-screamer' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span><?php _e( 'E-mail Notifications', 'mute-screamer' ); ?></span></legend>
							<label for="hmwp_ms_email_notifications">
								<input type="checkbox" value="1" id="hmwp_ms_email_notifications" name="hmwp_ms_options[email_notifications]" <?php checked( '1', $email_notifications ); ?> />
								<?php _e( 'Send alert emails', 'mute-screamer' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><label for="hmwp_ms_email_threshold"><?php _e( 'E-mail threshold', 'mute-screamer' ); ?></label></th>
					<td>
						<input type="text" class="small-text" value="<?php echo esc_attr( $email_threshold ); ?>" id="hmwp_ms_email_threshold" name="hmwp_ms_options[email_threshold]" />
						<span class="description"><?php _e( 'Minimum impact to send an alert email.', 'mute-screamer' ); ?></span>
					</td>
				</tr>
			</tbody>
		</table>

		<h3><?php _e( 'Warning Page', 'mute-screamer' ); ?></h3>
		<p><?php _e( "When a warning page is displayed Mute Screamer will look for a template file named 500.php in the current theme's directory. If a 500.php template is not available the 404.php template will be used. If, for some reason the 404.php template is not available the user will be redirected to the homepage.", 'mute-screamer' ); ?></p>
		<p><?php printf( __( 'Sample 500.php templates can be found in %s/mute-screamer/templates', 'mute-screamer' ), str_replace( ABSPATH, '', WP_PLUGIN_DIR ) ); ?></p>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><?php _e( 'WordPress admin warning', 'mute-screamer' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span><?php _e( 'WordPress admin warning', 'mute-screamer' ); ?></span></legend>
							<label for="hmwp_ms_warning_wp_admin">
								<input type="checkbox" value="1" id="hmwp_ms_warning_wp_admin" name="hmwp_ms_options[warning_wp_admin]" <?php checked( '1', $warning_wp_admin ); ?> />
								<?php _e( 'Log user out of the WordPress admin', 'mute-screamer' ); ?>
							</label>
						</fieldset>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><label for="hmwp_ms_warning_threshold"><?php _e( 'Warning threshold', 'mute-screamer' ); ?></label></th>
					<td>
						<input type="text" class="small-text" value="<?php echo esc_attr( $warning_threshold ); ?>" id="hmwp_ms_warning_threshold" name="hmwp_ms_options[warning_threshold]" />
						<span class="description"><?php _e( 'Minimum impact to show warning page.', 'mute-screamer' ); ?></span>
					</td>
				</tr>
			</tbody>
		</table>

		<h3><?php _e( 'IP Banning', 'mute-screamer' ); ?></h3>
		<p><?php _e( 'Clients can be banned for attacks over a certain threshold or for a number of repeated attacks.', 'mute-screamer' ); ?></p>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><?php _e( 'Enable banning', 'mute-screamer' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span><?php _e( 'Enable banning', 'mute-screamer' ); ?></span></legend>
							<label for="hmwp_ms_ban_enabled">
								<input type="checkbox" value="1" id="hmwp_ms_ban_enabled" name="hmwp_ms_options[ban_enabled]" <?php checked( '1', $ban_enabled ); ?> />
							</label>
						</fieldset>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><label for="hmwp_ms_ban_time"><?php _e( 'Ban time', 'mute-screamer' ); ?></label></th>
					<td>
						<input type="text" class="small-text" value="<?php echo esc_attr( $ban_time ); ?>" id="hmwp_ms_ban_time" name="hmwp_ms_options[ban_time]" />
						<span class="description"><?php _e( 'Number of seconds a client will be banned.', 'mute-screamer' ); ?></span>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><label for="hmwp_ms_ban_threshold"><?php _e( 'Ban threshold', 'mute-screamer' ); ?></label></th>
					<td>
						<input type="text" class="small-text" value="<?php echo esc_attr( $ban_threshold ); ?>" id="hmwp_ms_ban_threshold" name="hmwp_ms_options[ban_threshold]" />
						<span class="description"><?php _e( 'Minimum impact to ban a client.', 'mute-screamer' ); ?></span>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><label for="hmwp_ms_attack_repeat_limit"><?php _e( 'Attack repeat limit', 'mute-screamer' ); ?></label></th>
					<td>
						<input type="text" class="small-text" value="<?php echo esc_attr( $attack_repeat_limit ); ?>" id="hmwp_ms_attack_repeat_limit" name="hmwp_ms_options[attack_repeat_limit]" />
						<span class="description"><?php _e( 'Number of repeated attacks before a client is banned (repeat attacks can be under the ban threshold).', 'mute-screamer' ); ?></span>
					</td>
				</tr>
			</tbody>
		</table>

		<h3><?php _e( 'Exceptions', 'mute-screamer' ); ?></h3>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><?php _e( 'Exception fields', 'mute-screamer' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span><?php _e( 'Exception fields', 'mute-screamer' ); ?></span></legend>
							<p><label for="hmwp_ms_exception_fields">
								<?php _e( "Define fields that will be excluded from PHPIDS. One field per line. We've already added some defaults.", 'mute-screamer' ); ?><br />
								<?php _e( 'Example - exlude the POST field my_field: POST.my_field', 'mute-screamer' ); ?><br />
								<?php _e( 'Example - regular expression exclude: /.*foo/i', 'mute-screamer' ); ?>
							</label></p>
							<p><textarea class="large-text code" id="hmwp_ms_exception_fields" cols="50" rows="5" name="hmwp_ms_options[exception_fields]"><?php echo $exception_fields; ?></textarea></p>
						</fieldset>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e( 'HTML fields', 'mute-screamer' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span><?php _e( 'HTML fields', 'mute-screamer' ); ?></span></legend>
							<p><label for="hmwp_ms_html_fields">
								<?php _e( 'Define fields that contain HTML and need preparation before hitting the PHPIDS rules.', 'mute-screamer' ); ?><br />
								<?php _e( 'Note: Fields must contain valid HTML', 'mute-screamer' ); ?>
							</label></p>
							<p><textarea class="large-text code" id="hmwp_ms_html_fields" cols="50" rows="5" name="hmwp_ms_options[html_fields]"><?php echo $html_fields; ?></textarea></p>
						</fieldset>
					</td>
				</tr>

				<tr valign="top">
					<th scope="row"><?php _e( 'JSON fields', 'mute-screamer' ); ?></th>
					<td>
						<fieldset>
							<legend class="screen-reader-text"><span><?php _e( 'JSON fields', 'mute-screamer' ); ?></span></legend>
							<p><label for="hmwp_ms_json_fields">
								<?php _e( 'Define fields that contain JSON data and should be treated as such.', 'mute-screamer' ); ?>
							</label></p>
							<p><textarea class="large-text code" id="hmwp_ms_json_fields" cols="50" rows="5" name="hmwp_ms_options[json_fields]"><?php echo $json_fields; ?></textarea></p>
						</fieldset>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit">
			<input type="submit" value="<?php esc_attr_e( 'Save Changes', 'mute-screamer' ); ?>" class="button-primary" name="Submit">
		</p>
	</form>
</div>