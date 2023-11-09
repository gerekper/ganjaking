<?php

namespace WPMailSMTP\Pro\Emails\Logs\Admin;

use WPMailSMTP\Pro\Emails\Logs\Webhooks\Webhooks;
use WPMailSMTP\WP;
use WPMailSMTP\Options;
use WPMailSMTP\Admin\PageAbstract;
use WPMailSMTP\Helpers\UI;

/**
 * Class SettingsTab.
 *
 * @since 1.5.0
 */
class SettingsTab extends PageAbstract {

	/**
	 * Slug of a tab.
	 *
	 * @since 1.5.0
	 *
	 * @var string
	 */
	protected $slug = 'logs';

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

		parent::__construct();

		$this->options = Options::init();
	}

	/**
	 * Link label of a tab.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function get_label() {

		return esc_html__( 'Email Log', 'wp-mail-smtp-pro' );
	}

	/**
	 * Title of a tab.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	public function get_title() {

		return $this->get_label();
	}

	/**
	 * Tab content.
	 *
	 * @since 1.5.0
	 */
	public function display() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		?>
		<form method="POST" action="">
			<?php $this->wp_nonce_field(); ?>

			<!-- Section Title -->
			<div class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-content wp-mail-smtp-clear section-heading wp-mail-smtp-section-heading--has-divider no-desc wp-mail-smtp-tab-header">
				<div class="wp-mail-smtp-setting-field">
					<h2><?php echo $this->get_title(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></h2>
				</div>
			</div>

			<!-- Enable Log -->
			<div id="wp-mail-smtp-setting-row-logs_enabled" class="wp-mail-smtp-setting-row wp-mail-smtp-clear">
				<div class="wp-mail-smtp-setting-label">
					<label for="wp-mail-smtp-setting-logs_enabled">
						<?php esc_html_e( 'Enable Log', 'wp-mail-smtp-pro' ); ?>
					</label>
				</div>
				<div class="wp-mail-smtp-setting-field">
					<?php
					UI::toggle(
						[
							'name'     => 'wp-mail-smtp[logs][enabled]',
							'id'       => 'wp-mail-smtp-setting-logs_enabled',
							'value'    => 'true',
							'checked'  => (bool) $this->options->get( 'logs', 'enabled' ),
							'disabled' => $this->options->is_const_defined( 'logs', 'enabled' ),
						]
					);
					?>
					<p class="desc">
						<?php esc_html_e( 'Keep a record of basic details for all emails sent from your site.', 'wp-mail-smtp-pro' ); ?>
					</p>
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
			<div id="wp-mail-smtp-setting-row-logs_log_email_content" class="wp-mail-smtp-setting-row wp-mail-smtp-clear hidden">
				<div class="wp-mail-smtp-setting-label">
					<label for="wp-mail-smtp-setting-logs_log_email_content">
						<?php esc_html_e( 'Log Email Content', 'wp-mail-smtp-pro' ); ?>
					</label>
				</div>
				<div class="wp-mail-smtp-setting-field">
					<?php
					UI::toggle(
						[
							'name'     => 'wp-mail-smtp[logs][log_email_content]',
							'id'       => 'wp-mail-smtp-setting-logs_log_email_content',
							'value'    => 'true',
							'checked'  => (bool) $this->options->get( 'logs', 'log_email_content' ),
							'disabled' => $this->options->is_const_defined( 'logs', 'log_email_content' ),
						]
					);
					?>
					<p class="desc">
						<?php esc_html_e( 'Keep a record of all content for all emails sent from your site.', 'wp-mail-smtp-pro' ); ?>
					</p>
					<p class="desc">
						<?php
						esc_html_e( 'Email content may contain personal information, such as plain text passwords. Please carefully consider before enabling this option, as it will store all sent email content to your site’s database.', 'wp-mail-smtp-pro' );
						echo '<br>';
						echo wp_kses( __( 'This option has to be enabled if you want to <strong>resend emails</strong> from our Email Log.', 'wp-mail-smtp-pro' ), [ 'strong' => [] ] );

						if ( $this->options->is_const_defined( 'logs', 'log_email_content' ) ) {
							echo '<br>' . $this->options->get_const_set_message( 'WPMS_LOGS_LOG_EMAIL_CONTENT' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
					</p>
				</div>
			</div>

			<!-- Log Email Save Attachments -->
			<div id="wp-mail-smtp-setting-row-logs_save_attachments" class="wp-mail-smtp-setting-row wp-mail-smtp-clear hidden">
				<div class="wp-mail-smtp-setting-label">
					<label for="wp-mail-smtp-setting-logs_save_attachments">
						<?php esc_html_e( 'Save Attachments', 'wp-mail-smtp-pro' ); ?>
					</label>
				</div>
				<div class="wp-mail-smtp-setting-field">
					<?php
					UI::toggle(
						[
							'name'     => 'wp-mail-smtp[logs][save_attachments]',
							'id'       => 'wp-mail-smtp-setting-logs_save_attachments',
							'value'    => 'true',
							'checked'  => (bool) $this->options->get( 'logs', 'save_attachments' ),
							'disabled' => $this->options->is_const_defined( 'logs', 'save_attachments' ),
						]
					);
					?>
					<p class="desc">
						<?php esc_html_e( 'Save the sent attachments to the Email Log.', 'wp-mail-smtp-pro' ); ?>
					</p>
					<p class="desc">
						<?php
						esc_html_e( 'All sent attachments will be saved to your WordPress uploads folder. If your site sends a lot of big unique attachments, this could potentially cause some disk space issue.', 'wp-mail-smtp-pro' );

						if ( $this->options->is_const_defined( 'logs', 'save_attachments' ) ) {
							echo '<br>' . $this->options->get_const_set_message( 'WPMS_LOGS_SAVE_ATTACHMENTS' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
					</p>
				</div>
			</div>

			<!-- Open email tracking -->
			<div id="wp-mail-smtp-setting-row-logs_open_email_tracking" class="wp-mail-smtp-setting-row wp-mail-smtp-clear hidden">
				<div class="wp-mail-smtp-setting-label">
					<label for="wp-mail-smtp-setting-logs_open_email_tracking">
						<?php esc_html_e( 'Open Email Tracking', 'wp-mail-smtp-pro' ); ?>
					</label>
				</div>
				<div class="wp-mail-smtp-setting-field">
					<?php
					UI::toggle(
						[
							'name'     => 'wp-mail-smtp[logs][open_email_tracking]',
							'id'       => 'wp-mail-smtp-setting-logs_open_email_tracking',
							'value'    => 'true',
							'checked'  => (bool) $this->options->get( 'logs', 'open_email_tracking' ),
							'disabled' => $this->options->is_const_defined( 'logs', 'open_email_tracking' ),
						]
					);
					?>
					<p class="desc">
						<?php esc_html_e( 'Track when an email is opened.', 'wp-mail-smtp-pro' ); ?>
					</p>
					<p class="desc">
						<?php
						esc_html_e( 'This will allow you to see which emails were opened by the recipients.', 'wp-mail-smtp-pro' );

						if ( $this->options->is_const_defined( 'logs', 'open_email_tracking' ) ) {
							echo '<br>' . $this->options->get_const_set_message( 'WPMS_LOGS_OPEN_EMAIL_TRACKING' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
					</p>
				</div>
			</div>

			<!-- Click link tracking -->
			<div id="wp-mail-smtp-setting-row-logs_click_link_tracking" class="wp-mail-smtp-setting-row wp-mail-smtp-clear hidden">
				<div class="wp-mail-smtp-setting-label">
					<label for="wp-mail-smtp-setting-logs_click_link_tracking">
						<?php esc_html_e( 'Click Link Tracking', 'wp-mail-smtp-pro' ); ?>
					</label>
				</div>
				<div class="wp-mail-smtp-setting-field">
					<?php
					UI::toggle(
						[
							'name'     => 'wp-mail-smtp[logs][click_link_tracking]',
							'id'       => 'wp-mail-smtp-setting-logs_click_link_tracking',
							'value'    => 'true',
							'checked'  => (bool) $this->options->get( 'logs', 'click_link_tracking' ),
							'disabled' => $this->options->is_const_defined( 'logs', 'click_link_tracking' ),
						]
					);
					?>
					<p class="desc">
						<?php esc_html_e( 'Track clicked links in emails.', 'wp-mail-smtp-pro' ); ?>
					</p>
					<p class="desc">
						<?php
						esc_html_e( 'This will allow you to see which links were clicked in the sent emails.', 'wp-mail-smtp-pro' );

						if ( $this->options->is_const_defined( 'logs', 'click_link_tracking' ) ) {
							echo '<br>' . $this->options->get_const_set_message( 'WPMS_LOGS_CLICK_LINK_TRACKING' ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
					</p>
				</div>
			</div>

			<!-- Webhooks Status -->
			<?php $this->webhooks_status(); ?>

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

			<!-- Log content and save attachments should be displayed only when log is enabled. -->
			<script>
				var $logEnabled = jQuery('#wp-mail-smtp-setting-logs_enabled');
				if ( $logEnabled.is(':checked') ) {
					jQuery('#wp-mail-smtp-setting-row-logs_log_email_content').show();
					jQuery('#wp-mail-smtp-setting-row-logs_save_attachments').show();
					jQuery('#wp-mail-smtp-setting-row-logs_open_email_tracking').show();
					jQuery('#wp-mail-smtp-setting-row-logs_click_link_tracking').show();
				}
				$logEnabled.on('change', function() {
					if ( jQuery( this ).is(':checked') ) {
						jQuery('#wp-mail-smtp-setting-row-logs_log_email_content').show();
						jQuery('#wp-mail-smtp-setting-row-logs_save_attachments').show();
						jQuery('#wp-mail-smtp-setting-row-logs_open_email_tracking').show();
						jQuery('#wp-mail-smtp-setting-row-logs_click_link_tracking').show();
					} else {
						jQuery('#wp-mail-smtp-setting-row-logs_log_email_content').hide();
						jQuery('#wp-mail-smtp-setting-row-logs_save_attachments').hide();
						jQuery('#wp-mail-smtp-setting-row-logs_open_email_tracking').hide();
						jQuery('#wp-mail-smtp-setting-row-logs_click_link_tracking').hide();
					}
				} );
			</script>

			<?php $this->display_save_btn(); ?>

		</form>

		<?php
	}

	/**
	 * Webhooks status row.
	 *
	 * @since 3.3.0
	 */
	private function webhooks_status() {

		if ( ! wp_mail_smtp()->get_pro()->get_logs()->is_enabled() || ! Webhooks::is_allowed() ) {
			return;
		}

		$provider = wp_mail_smtp()->get_pro()->get_logs()->get_webhooks()->get_active_provider();

		if ( $provider === false ) {
			return;
		}

		// Verify subscription before display status.
		$provider->verify_subscription();

		?>
		<div id="wp-mail-smtp-setting-row-webhooks_status" class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-text wp-mail-smtp-clear">
			<div class="wp-mail-smtp-setting-label">
				<label>
					<?php esc_html_e( 'Webhooks Status', 'wp-mail-smtp-pro' ); ?>
				</label>
			</div>
			<div class="wp-mail-smtp-setting-field">
				<?php if ( $provider->get_setup_status() === Webhooks::SUCCESS_SETUP ) : ?>
					<?php esc_html_e( 'Subscribed.', 'wp-mail-smtp-pro' ); ?>

					<p class="desc">
						<?php esc_html_e( 'Webhooks subscription was created.', 'wp-mail-smtp-pro' ); ?>
						<?php if ( $this->options->is_const_enabled() ) : ?>
							<br><br>
							<span style="color: red;">
								<?php
								esc_html_e( 'If you need to change the values of your constants, click the Unsubscribe button below. After completing your changes, return to this page and click the Subscribe button to re-enable the delivery verification webhooks.', 'wp-mail-smtp-pro' );
								?>
							</span>
						<?php endif; ?>
					</p>
					<br>
					<button class="wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-blueish js-wp-mail-smtp-webhooks-unsubscribe">
						<?php esc_html_e( 'Unsubscribe', 'wp-mail-smtp-pro' ); ?>
					</button>
				<?php elseif ( $provider->get_setup_status() === Webhooks::BROKEN_SETUP ) : ?>
					<strong><?php esc_html_e( 'Subscription is broken.', 'wp-mail-smtp-pro' ); ?></strong>
					<br><br>
					<?php esc_html_e( 'Potential reasons:', 'wp-mail-smtp-pro' ); ?>
					<ol>
						<li><?php esc_html_e( 'Website domain was changed.', 'wp-mail-smtp-pro' ); ?></li>
						<li>
							<?php
							printf( /* translators: %s - mailer title. */
								esc_html__( 'Subscription was removed or modified manually in %s account.', 'wp-mail-smtp-pro' ),
								esc_html( wp_mail_smtp()->get_providers()->get_options( $provider->get_mailer_name() )->get_title() )
							);
							?>
						</li>
					</ol>
					<br>
					<button class="wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-blueish js-wp-mail-smtp-webhooks-subscribe">
						<?php esc_html_e( 'Resubscribe', 'wp-mail-smtp-pro' ); ?>
					</button>
				<?php elseif ( $provider->get_setup_status() === Webhooks::FAILED_SETUP ) : ?>
					<strong><?php esc_html_e( 'Failed.', 'wp-mail-smtp-pro' ); ?></strong>
					<p class="desc">
						<?php
						esc_html_e( 'Automatic creation of webhooks subscription for email deliverability status verification has failed.', 'wp-mail-smtp-pro' );
						?>
					</p>
					<br>
					<?php esc_html_e( 'Please fix the errors below and try to subscribe again.', 'wp-mail-smtp-pro' ); ?>
					<br><br>
					<?php echo esc_html( get_option( Webhooks::SUBSCRIPTION_ERROR_OPTION_NAME ) ); ?>
					<br><br>
					<button class="wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-blueish js-wp-mail-smtp-webhooks-subscribe">
						<?php esc_html_e( 'Subscribe', 'wp-mail-smtp-pro' ); ?>
					</button>
				<?php elseif ( $provider->get_setup_status() === Webhooks::MANUAL_SETUP ) : ?>
					<?php esc_html_e( 'Unsubscribed.', 'wp-mail-smtp-pro' ); ?>
					<p class="desc">
						<?php
						esc_html_e( 'The subscription was manually removed. If you want to use webhooks for email deliverability status verification, please perform the "Subscribe" action.', 'wp-mail-smtp-pro' );
						?>
					</p>
					<br>
					<button class="wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-blueish js-wp-mail-smtp-webhooks-subscribe">
						<?php esc_html_e( 'Subscribe', 'wp-mail-smtp-pro' ); ?>
					</button>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Process tab form submission ($_POST).
	 *
	 * @since 1.5.0
	 *
	 * @param array $data Data from $_POST array.
	 */
	public function process_post( $data ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		$this->check_admin_referer();

		// Unchecked checkboxes doesn't exist in $_POST, so we need to ensure we actually have them in data to save.
		if ( empty( $data['logs']['enabled'] ) ) {
			$data['logs']['enabled'] = false;
		}

		if ( empty( $data['logs']['log_email_content'] ) ) {
			$data['logs']['log_email_content'] = false;
		}

		if ( empty( $data['logs']['save_attachments'] ) ) {
			$data['logs']['save_attachments'] = false;
		}

		if ( empty( $data['logs']['open_email_tracking'] ) ) {
			$data['logs']['open_email_tracking'] = false;
		}

		if ( empty( $data['logs']['click_link_tracking'] ) ) {
			$data['logs']['click_link_tracking'] = false;
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
