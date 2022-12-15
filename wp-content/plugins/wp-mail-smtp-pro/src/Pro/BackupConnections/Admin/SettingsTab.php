<?php

namespace WPMailSMTP\Pro\BackupConnections\Admin;

use WPMailSMTP\Options;
use WPMailSMTP\Pro\AdditionalConnections\AdditionalConnections;

/**
 * Class SettingsTab.
 *
 * Backup connection settings.
 *
 * @since 3.7.0
 */
class SettingsTab {

	/**
	 * Register hooks.
	 *
	 * @since 3.7.0
	 */
	public function hooks() {

		add_filter( 'wp_mail_smtp_admin_settings_tab_display', [ $this, 'display' ] );
	}

	/**
	 * Display backup connection settings.
	 *
	 * @since 3.7.0
	 *
	 * @param string $settings_html Settings page HTML.
	 *
	 * @return string
	 */
	public function display( $settings_html ) {

		$additional_connections = new AdditionalConnections();
		$connections            = $additional_connections->get_configured_connections();

		$connection_id = Options::init()->get( 'backup_connection', 'connection_id' );

		if ( ! $additional_connections->connection_exists( $connection_id ) ) {
			$connection_id = false;
		}

		ob_start();
		?>
		<!-- Backup Connection Section Title -->
		<div class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-content wp-mail-smtp-clear section-heading">
			<div class="wp-mail-smtp-setting-field">
				<h2><?php esc_html_e( 'Backup Connection', 'wp-mail-smtp-pro' ); ?></h2>
				<p class="desc">
					<?php
					echo wp_kses(
						sprintf( /* translators: %s - Backup Connections documentation page URL. */
							__( 'Don’t worry about losing emails. Add an additional connection, then set it as your Backup Connection. Emails that fail to send with the Primary Connection will be sent via the selected Backup Connection. <a href="%s" target="_blank" rel="noopener noreferrer">Learn More</a>.', 'wp-mail-smtp-pro' ),
							esc_url(
								wp_mail_smtp()->get_utm_url(
									'https://wpmailsmtp.com/docs/configuring-backup-connection/',
									[
										'content' => 'Backup Connection Setting',
									]
								)
							)
						),
						[
							'a' => [
								'href'   => [],
								'rel'    => [],
								'target' => [],
							],
						]
					);
					?>
				</p>
			</div>
		</div>

		<!-- Backup Connection Selector -->
		<div id="wp-mail-smtp-setting-row-backup_connection" class="wp-mail-smtp-setting-row wp-mail-smtp-clear">
			<div class="wp-mail-smtp-setting-label">
				<label for="wp-mail-smtp-setting-test_email_html">
					<?php esc_html_e( 'Backup Connection', 'wp-mail-smtp-pro' ); ?>
				</label>
			</div>
			<div class="wp-mail-smtp-setting-field">
				<div class="wp-mail-smtp-connection-selector">
					<label for="wp-mail-smtp-setting-row-backup_connection_none">
						<input type="radio" id="wp-mail-smtp-setting-row-backup_connection_none" name="wp-mail-smtp[backup_connection][connection_id]" value="" <?php checked( empty( $connection_id ) ); ?>/>
						<span><?php esc_attr_e( 'None', 'wp-mail-smtp-pro' ); ?></span>
					</label>
					<?php foreach ( $connections as $connection ) : ?>
						<label for="wp-mail-smtp-setting-row-backup_connection_<?php echo esc_attr( $connection->get_id() ); ?>">
							<input type="radio" id="wp-mail-smtp-setting-row-backup_connection_<?php echo esc_attr( $connection->get_id() ); ?>"
										 name="wp-mail-smtp[backup_connection][connection_id]" value="<?php echo esc_attr( $connection->get_id() ); ?>"
										 <?php checked( $connection_id, $connection->get_id() ); ?>
							/>
							<span><?php echo esc_html( $connection->get_title() ); ?></span>
						</label>
					<?php endforeach; ?>
				</div>
				<p class="desc">
					<?php
					if ( empty( $connections ) ) {
						echo wp_kses(
							sprintf( /* translators: %s - Additional connections settings page url. */
								__( 'Once you add an <a href="%s">additional connection</a>, you can select it here.', 'wp-mail-smtp-pro' ),
								add_query_arg(
									[
										'tab' => 'connections',
									],
									wp_mail_smtp()->get_admin()->get_admin_page_url()
								)
							),
							[
								'a' => [
									'href'   => [],
									'target' => [],
									'rel'    => [],
								],
							]
						);
					} else {
						esc_html_e( 'Choose which connection you’d like to use as your Backup Connection.', 'wp-mail-smtp-pro' );
					}
					?>
				</p>
			</div>
		</div>
		<?php

		return $settings_html . ob_get_clean();
	}
}
