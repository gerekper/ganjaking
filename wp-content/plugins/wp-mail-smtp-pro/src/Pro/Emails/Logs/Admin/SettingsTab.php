<?php

namespace WPMailSMTP\Pro\Emails\Logs\Admin;

use WPMailSMTP\Options;
use WPMailSMTP\WP;

/**
 * Class SettingsTab.
 */
class SettingsTab extends \WPMailSMTP\Admin\PageAbstract {

	/**
	 * Plugin options.
	 *
	 * @since 2.8.0
	 *
	 * @var Options
	 */
	protected $options;

	/**
	 * Constructor.
	 *
	 * @since 2.8.0
	 */
	public function __construct() {

		$this->options = new Options();
	}

	/**
	 * @var string Slug of a tab.
	 */
	protected $slug = 'logs';

	/**
	 * @inheritdoc
	 */
	public function get_label() {

		return esc_html__( 'Email Log', 'wp-mail-smtp-pro' );
	}

	/**
	 * @inheritdoc
	 */
	public function get_title() {

		return $this->get_label();
	}

	/**
	 * @inheritdoc
	 */
	public function display() {
		?>

		<form method="POST" action="">
			<?php $this->wp_nonce_field(); ?>

			<!-- Section Title -->
			<div class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-content wp-mail-smtp-clear section-heading no-desc" id="wp-mail-smtp-setting-row-email-heading">
				<div class="wp-mail-smtp-setting-field">
					<h2><?php echo $this->get_title(); ?></h2>
				</div>
			</div>

			<!-- Enable Log -->
			<div id="wp-mail-smtp-setting-row-logs_enabled" class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-checkbox wp-mail-smtp-clear">
				<div class="wp-mail-smtp-setting-label">
					<label for="wp-mail-smtp-setting-logs_enabled">
						<?php esc_html_e( 'Enable Log', 'wp-mail-smtp-pro' ); ?>
					</label>
				</div>
				<div class="wp-mail-smtp-setting-field">
					<input name="wp-mail-smtp[logs][enabled]" type="checkbox" id="wp-mail-smtp-setting-logs_enabled"
						value="true" <?php checked( true, $this->options->get( 'logs', 'enabled' ) ); ?>
						<?php disabled( $this->options->is_const_defined( 'logs', 'enabled' ) ); ?>>
					<label for="wp-mail-smtp-setting-logs_enabled">
						<?php esc_html_e( 'Keep a record of basic details for all emails sent from your site.', 'wp-mail-smtp-pro' ); ?>
					</label>
					<p class="desc">
						<?php
						esc_html_e( 'This will allow you to view both general information (date sent, subject, email status) and technical information (all the headers, including TO, CC, BCC) for all sent emails.', 'wp-mail-smtp-pro' );

						if ( $this->options->is_const_defined( 'logs', 'enabled' ) ) {
							echo '<br>' . $this->options->get_const_set_message( 'WPMS_LOGS_ENABLED' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
					</p>
				</div>
			</div>

			<!-- Log Email Content -->
			<div id="wp-mail-smtp-setting-row-logs_log_email_content" class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-checkbox wp-mail-smtp-clear hidden">
				<div class="wp-mail-smtp-setting-label">
					<label for="wp-mail-smtp-setting-logs_log_email_content">
						<?php esc_html_e( 'Log Email Content', 'wp-mail-smtp-pro' ); ?>
					</label>
				</div>
				<div class="wp-mail-smtp-setting-field">
					<input name="wp-mail-smtp[logs][log_email_content]" type="checkbox" id="wp-mail-smtp-setting-logs_log_email_content"
						value="true" <?php checked( true, $this->options->get( 'logs', 'log_email_content' ) ); ?>
						<?php disabled( $this->options->is_const_defined( 'logs', 'log_email_content' ) ); ?>>
					<label for="wp-mail-smtp-setting-logs_log_email_content">
						<?php esc_html_e( 'Keep a record of all content for all emails sent from your site.', 'wp-mail-smtp-pro' ); ?>
					</label>
					<p class="desc">
						<?php
						esc_html_e( 'Email content may contain personal information, such as plain text passwords. Please carefully consider before enabling this option, as it will store all sent email content to your site’s database.', 'wp-mail-smtp-pro' );

						if ( $this->options->is_const_defined( 'logs', 'log_email_content' ) ) {
							echo '<br>' . $this->options->get_const_set_message( 'WPMS_LOGS_LOG_EMAIL_CONTENT' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
					</p>
				</div>
			</div>

			<!-- Log Retention Period -->
			<div id="wp-mail-smtp-setting-row-log_retention_period" class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-text wp-mail-smtp-clear">
				<div class="wp-mail-smtp-setting-label">
					<label for="wp-mail-smtp-setting-log_retention_period">
						<?php esc_html_e( 'Log Retention Period', 'wp-mail-smtp-pro' ); ?>
					</label>
				</div>
				<div class="wp-mail-smtp-setting-field">
					<select name="wp-mail-smtp[logs][log_retention_period]"
						id="wp-mail-smtp-setting-log_retention_period"
						<?php disabled( $this->options->is_const_defined( 'logs', 'log_retention_period' ) ); ?>>
						<option value=""><?php esc_html_e( 'Forever', 'wp-mail-smtp-pro' ); ?></option>
						<?php foreach ( $this->get_log_retention_period_options() as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $this->options->get( 'logs', 'log_retention_period' ), $value ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
					<p class="desc">
						<?php
						esc_html_e( 'Email logs older than the selected period will be permanently deleted from the database.', 'wp-mail-smtp-pro' );

						if ( $this->options->is_const_defined( 'logs', 'log_retention_period' ) ) {
							echo '<br>' . $this->options->get_const_set_message( 'WPMS_LOGS_LOG_RETENTION_PERIOD' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
					</p>
				</div>
			</div>

			<!-- Log content should be displayed only when log is enabled. -->
			<script>
				var $logEnabled = jQuery('#wp-mail-smtp-setting-logs_enabled');
				if ( $logEnabled.is(':checked') ) {
					jQuery('#wp-mail-smtp-setting-row-logs_log_email_content').show();
				}
				$logEnabled.on('change', function() {
					if ( jQuery( this ).is(':checked') ) {
						jQuery('#wp-mail-smtp-setting-row-logs_log_email_content').show();
					} else {
						jQuery('#wp-mail-smtp-setting-row-logs_log_email_content').hide();
					}
				} );
			</script>

			<?php $this->display_save_btn(); ?>

		</form>

		<?php
	}

	/**
	 * @inheritdoc
	 */
	public function process_post( $data ) {

		$this->check_admin_referer();

		// Unchecked checkboxes doesn't exist in $_POST, so we need to ensure we actually have them in data to save.
		if ( empty( $data['logs']['enabled'] ) ) {
			$data['logs']['enabled'] = false;
		}
		if ( empty( $data['logs']['log_email_content'] ) ) {
			$data['logs']['log_email_content'] = false;
		}

		// All the sanitization is done there.
		$this->options->set( $data, false, false );

		WP::add_admin_notice(
			esc_html__( 'Settings were successfully saved.', 'wp-mail-smtp-pro' ),
			WP::ADMIN_NOTICE_SUCCESS
		);
	}

	/**
	 * Get log retention period options.
	 *
	 * @since 2.8.0
	 *
	 * @return array
	 */
	public function get_log_retention_period_options() {

		$options = [
			86400    => esc_html__( '1 Day', 'wp-mail-smtp-pro' ),
			604800   => esc_html__( '1 Week', 'wp-mail-smtp-pro' ),
			2628000  => esc_html__( '1 Month', 'wp-mail-smtp-pro' ),
			15770000 => esc_html__( '6 Months', 'wp-mail-smtp-pro' ),
			31540000 => esc_html__( '1 Year', 'wp-mail-smtp-pro' ),
		];

		$log_retention_period = $this->options->get( 'logs', 'log_retention_period' );

		// Check if defined value already in list and add it if not.
		if (
			! empty( $log_retention_period ) &&
			! isset( $options[ $log_retention_period ] )
		) {
			$log_retention_period_days = floor( $log_retention_period / DAY_IN_SECONDS );

			$options[ $log_retention_period ] = sprintf(
			/* translators: %d - days count. */
				_n( '%d Day', '%d Days', $log_retention_period_days, 'wp-mail-smtp-pro' ),
				$log_retention_period_days
			);

			ksort( $options );
		}

		/**
		 * Filter log retention period options.
		 *
		 * @since 2.8.0
		 *
		 * @param array $options Log retention period options.
		 *                       Option key in seconds and value in human readable time period.
		 */
		return apply_filters(
			'wp_mail_smtp_pro_emails_logs_admin_settings_tab_get_log_retention_period_options',
			$options
		);
	}
}
