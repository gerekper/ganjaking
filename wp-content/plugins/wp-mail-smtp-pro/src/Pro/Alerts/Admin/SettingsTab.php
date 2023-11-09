<?php

namespace WPMailSMTP\Pro\Alerts\Admin;

use WPMailSMTP\Admin\Area;
use WPMailSMTP\Admin\Pages\AlertsTab;
use WPMailSMTP\Helpers\Helpers;
use WPMailSMTP\Helpers\UI;
use WPMailSMTP\Options;
use WPMailSMTP\Pro\Alerts\AbstractOptions;
use WPMailSMTP\Pro\Alerts\Loader;
use WPMailSMTP\Pro\Alerts\Providers\Email\Handler as EmailAlertsHandler;
use WPMailSMTP\Pro\Alerts\Providers\Email\Options as EmailAlertsOptions;
use WPMailSMTP\Pro\Emails\Logs\Admin\PageAbstract;
use WPMailSMTP\WP;

/**
 * Class SettingsTab.
 *
 * @since 3.5.0
 */
class SettingsTab extends AlertsTab {

	/**
	 * Providers loader.
	 *
	 * @since 3.5.0
	 *
	 * @var Loader
	 */
	private $loader;

	/**
	 * User meta for test alerts action notices.
	 *
	 * @since 3.9.0
	 *
	 * @var string
	 */
	const NOTICE_USER_META = 'wp_mail_smtp_test_alerts_notice';

	/**
	 * Plugin options.
	 *
	 * @since 3.10.0
	 *
	 * @var Options
	 */
	private $options;

	/**
	 * Constructor.
	 *
	 * @since 3.5.0
	 *
	 * @param PageAbstract $parent_page Parent page object.
	 */
	public function __construct( $parent_page = null ) {

		parent::__construct( $parent_page );

		if ( wp_mail_smtp()->get_admin()->get_current_tab() === $this->slug ) {
			$this->hooks();
		}

		$this->loader  = new Loader();
		$this->options = Options::init();
	}

	/**
	 * Register hooks.
	 *
	 * @since 3.5.0
	 */
	public function hooks() {

		add_action( 'wp_mail_smtp_admin_area_enqueue_assets', [ $this, 'enqueue_assets' ] );
		add_action( 'admin_init', [ $this, 'display_notices' ] );

		add_filter( 'wp_mail_smtp_options_postprocess_key_defaults', [ $this, 'options_defaults' ], 10, 3 );
	}

	/**
	 * Enqueue required JS and CSS.
	 *
	 * @since 3.5.0
	 */
	public function enqueue_assets() {

		$min = WP::asset_min();

		wp_enqueue_script(
			'wp-mail-smtp-alerts',
			wp_mail_smtp()->plugin_url . "/assets/pro/js/smtp-pro-alerts{$min}.js",
			[ 'jquery', 'wp-mail-smtp-admin' ],
			WPMS_PLUGIN_VER,
			true
		);

		wp_localize_script(
			'wp-mail-smtp-alerts',
			'wp_mail_smtp_alerts',
			$this->get_localized_data()
		);
	}

	/**
	 * Get localized data.
	 *
	 * @since 3.5.0
	 *
	 * @return array
	 */
	private function get_localized_data() {

		return [
			'providers' => array_map(
				function ( AbstractOptions $option ) {
					return [
						'connection_options_tmpl' => $option->get_connection_options( [], '%%index%%' ),
						'max_connections_count'   => $option->get_max_connections_count(),
					];
				},
				$this->loader->get_options_all()
			),
			'plugin_url' => wp_mail_smtp()->plugin_url,
			'texts'      => [
				'ok'            => esc_html__( 'OK', 'wp-mail-smtp-pro' ),
				'alert_title'   => esc_html__( 'Heads up!', 'wp-mail-smtp-pro' ),
				'alert_content' => esc_html__( 'Alerts settings have changed. Please save the settings before you can perform a test.', 'wp-mail-smtp-pro' ),
			],
		];
	}

	/**
	 * Set options defaults.
	 *
	 * @since 3.5.0
	 *
	 * @param mixed  $value Option value.
	 * @param string $group Group key.
	 * @param string $key   Option key.
	 *
	 * @return mixed
	 */
	public function options_defaults( $value, $group, $key ) {

		// Set admin email as default for Email channel.
		if ( $group === 'alert_email' && $key === 'connections' ) {
			$value = [
				[
					'send_to' => get_option( 'admin_email' ),
				],
			];
		} elseif (
			$key === 'connections' &&
			in_array( $group, [ 'alert_slack_webhook', 'alert_twilio_sms', 'alert_custom_webhook' ], true )
		) {
			$value = [];
		}

		return $value;
	}

	/**
	 * Output HTML of the email controls settings.
	 *
	 * @since 3.5.0
	 */
	public function display() {

		$options = Options::init();
		?>

		<form method="POST" action="">
			<?php $this->wp_nonce_field(); ?>

			<div class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-content section-heading wp-mail-smtp-section-heading--has-divider">
				<div class="wp-mail-smtp-setting-field">
					<h2><?php esc_html_e( 'Alerts', 'wp-mail-smtp-pro' ); ?></h2>
					<p class="desc">
						<?php esc_html_e( 'Configure at least one of these integrations to receive notifications when email fails to send from your site. Alert notifications will contain the following important data: email subject, email Send To address, the error message, and helpful links to help you fix the issue.', 'wp-mail-smtp-pro' ); ?>
					</p>
				</div>
			</div>

			<!-- Alert Events -->
			<div id="wp-mail-smtp-setting-row-alert_event_types" class="wp-mail-smtp-setting-row wp-mail-smtp-clear">
				<div class="wp-mail-smtp-setting-label">
					<label for="wp-mail-smtp-setting-debug_event_types">
						<?php esc_html_e( 'Notify when', 'wp-mail-smtp-pro' ); ?>
					</label>
				</div>
				<div class="wp-mail-smtp-setting-field">
					<?php
					UI::toggle(
						[
							'name'     => '',
							'id'       => 'wp-mail-smtp-setting-alert_events_email_failed',
							'value'    => '',
							'label'    => esc_html__( 'The initial email sending request fails', 'wp-mail-smtp-pro' ),
							'checked'  => true,
							'disabled' => true,
						]
					);
					?>

					<p class="desc">
						<?php esc_html_e( 'This option is always enabled and will notify you about instant email sending failures.', 'wp-mail-smtp-pro' ); ?>
					</p>
					<hr class="wp-mail-smtp-setting-mid-row-sep">

					<?php
					// Whether logs are enabled.
					$logs_enabled = wp_mail_smtp()->get_pro()->get_logs()->is_enabled();
					// Whether current mailer supports delivery verification.
					$mailer_supports = ! Helpers::mailer_without_send_confirmation();

					$this->display_unavailable_hard_bounce_alerts_notice( $logs_enabled, $mailer_supports );

					UI::toggle(
						[
							'name'     => 'wp-mail-smtp[alert_events][email_hard_bounced]',
							'id'       => 'wp-mail-smtp-setting-alert_events_email_bounced',
							'value'    => 'true',
							'label'    => __( 'The deliverability verification process detects a hard bounce', 'wp-mail-smtp-pro' ),
							'checked'  => (bool) $this->options->get( 'alert_events', 'email_hard_bounced' ) && $logs_enabled && $mailer_supports,
							'disabled' => ! $logs_enabled || ! $mailer_supports,
						]
					);
					?>
					<p class="desc">
						<?php esc_html_e( 'Get notified about emails that were successfully sent, but have hard bounced on delivery attempt. A hard bounce is an email that has failed to deliver for permanent reasons, such as the recipient\'s email address being invalid.', 'wp-mail-smtp-pro' ); ?>
					</p>
				</div>
			</div>

			<?php foreach ( $this->loader->get_options_all() as $option ) : ?>
				<?php
				$is_enabled  = (bool) $options->get( $option->get_group(), 'enabled' );
				$connections = (array) $options->get( $option->get_group(), 'connections' );
				?>

				<div id="wp-mail-smtp-setting-row-alerts-<?php echo esc_attr( $option->get_slug() ); ?>" class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-alert">
					<div class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-content section-heading">
						<div class="wp-mail-smtp-setting-field">
							<h3><?php echo esc_html( $option->get_title() ); ?></h3>
							<p class="desc"><?php echo wp_kses_post( $option->get_description() ); ?></p>

							<?php
							if ( $option->get_slug() === EmailAlertsOptions::SLUG ) {
								$this->display_email_alert_rate_limit_notice();
							}
							?>
						</div>
					</div>

                    <div class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-checkbox-toggle">
						<div class="wp-mail-smtp-setting-label">
							<label for="wp-mail-smtp-setting-alert-<?php echo esc_attr( $option->get_slug() ); ?>-enabled">
								<?php
								/* translators: %s - Alert title. */
								echo sprintf( esc_html__( '%s Alerts', 'wp-mail-smtp-pro' ), esc_html( $option->get_title() ) );
								?>
							</label>
						</div>
						<div class="wp-mail-smtp-setting-field">
							<?php
							UI::toggle(
								[
									'class'   => 'js-wp-mail-smtp-setting-alert-enabled',
									'name'    => 'wp-mail-smtp[alert_' . $option->get_slug() . '][enabled]',
									'id'      => 'wp-mail-smtp-setting-alert-' . $option->get_slug() . '-enabled',
									'checked' => $is_enabled,
								]
							);
							?>
						</div>
					</div>

					<div class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-alert-options"<?php echo ! $is_enabled ? ' style="display:none;"' : ''; ?>>

						<?php $option->display_options(); ?>

						<div class="wp-mail-smtp-setting-row">
							<div class="wp-mail-smtp-setting-field">
								<button class="wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-blueish js-wp-mail-smtp-setting-alert-add-connection" data-provider="<?php echo esc_attr( $option->get_slug() ); ?>" <?php disabled( $option->get_max_connections_count() > 0 && count( $connections ) >= $option->get_max_connections_count() ); ?>>
									<?php echo esc_html( $option->get_add_connection_text() ); ?>
								</button>
							</div>
						</div>
					</div>
				</div>
			<?php endforeach; ?>

			<?php $this->display_save_btn(); ?>
		</form>
		<?php
	}

	/**
	 * Display the test alerts success notice, if present.
	 *
	 * @since 3.9.0
	 */
	public function display_notices() {

		$current_user_id = get_current_user_id();

		// Bail early if there is no notice.
		if ( ! metadata_exists( 'user', $current_user_id, self::NOTICE_USER_META ) ) {
			return;
		}

		$notice_message       = esc_html__( 'Alert Tests sent successfully.', 'wp-mail-smtp-pro' );
		$options              = Options::init();
		$email_alerts_enabled = (bool) $options->get( 'alert_email', 'enabled' );

		// If email alerts are enabled, add remaining rate limit time to the notice.
		if ( $email_alerts_enabled ) {
			$remaining_seconds = EmailAlertsHandler::get_remaining_rate_limit_seconds();
			// If rate limit just expired, or the alert handler
			// hasn't run yet, default to the handler rate limit.
			$remaining_seconds = $remaining_seconds === 0 ? EmailAlertsHandler::RATE_LIMIT : $remaining_seconds;
			$remaining_minutes = round( $remaining_seconds / MINUTE_IN_SECONDS );
			$notice_message    = sprintf(
				/* translators: %1$s - Default success notice; %2$d. number of minutes until new email alerts can be sent. */
				esc_html__( '%1$s Any additional email alerts from this site are paused for %2$d minutes.', 'wp-mail-smtp-pro' ),
				$notice_message,
				$remaining_minutes
			);
		}

		WP::add_admin_notice( $notice_message, WP::ADMIN_NOTICE_SUCCESS, false );

		delete_user_meta( $current_user_id, self::NOTICE_USER_META );
	}

	/**
	 * Display a notice with the remaining minutes before
	 * email alerts rate limit expires.
	 *
	 * @since 3.9.0
	 */
	private function display_email_alert_rate_limit_notice() {

		$remaining_seconds = EmailAlertsHandler::get_remaining_rate_limit_seconds();

		// Bail early if rate limit expired.
		if ( $remaining_seconds === 0 ) {
			return;
		}

		$remaining_minutes = round( $remaining_seconds / MINUTE_IN_SECONDS );
		?>
		<div class="notice inline notice-inline wp-mail-smtp-notice notice-warning">
			<p>
				<?php
				printf(
					/* translators: %d - number of minutes until new email alerts can be sent. */
					esc_html__( 'Any additional email alerts from this site are paused for %d minutes. You can still test other types of alerts.', 'wp-mail-smtp-pro' ),
					absint( $remaining_minutes )
				);
				?>
			</p>
		</div>
		<?php
	}

	/**
	 * Display a notice with the reasons hard-bounce alerts
	 * aren't available.
	 *
	 * @since 3.10.0
	 *
	 * @param bool $logs_enabled    Whether email logs are enabled.
	 * @param bool $mailer_supports Whether current mailer supports delivery verification.
	 */
	private function display_unavailable_hard_bounce_alerts_notice( $logs_enabled, $mailer_supports ) {

		if ( $logs_enabled && $mailer_supports ) {
			return;
		}

		$message = '';

		if ( ! $logs_enabled ) {
			$message = sprintf(
				'%1$s <a href="%2$s">%3$s</a>.',
				esc_html__( 'Email Log has to be enabled for hard bounce detection to work.', 'wp-mail-smtp-pro' ),
				esc_url(
					add_query_arg(
						'tab',
						'logs',
						wp_mail_smtp()->get_admin()->get_admin_page_url( Area::SLUG )
					)
				),
				esc_html__( 'Please enable Email Log', 'wp-mail-smtp-pro' )
			);
		} else {
			$message = esc_html__( 'Your Primary Connection mailer doesn\'t support delivery verification. If you want to enable this option, please switch to a transactional mailer like SendLayer.', 'wp-mail-smtp-pro' );
		}
		?>
		<div class="notice inline notice-inline wp-mail-smtp-notice notice-warning">
			<p>
				<?php echo wp_kses( $message, [ 'a' => [ 'href' => [] ] ] ); ?>
			</p>
		</div>
		<?php
	}

	/**
	 * Display save and test buttons.
	 *
	 * @since 3.9.0
	 */
	public function display_save_btn() {

		?>
		<p class="wp-mail-smtp-submit">
			<button type="submit" class="wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-orange">
				<?php esc_html_e( 'Save Settings', 'wp-mail-smtp-pro' ); ?>
			</button>
			<button type="button" class="wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-light-grey" id="js-wp-mail-smtp-btn-test-alerts" disabled>
				<?php esc_html_e( 'Test Alerts', 'wp-mail-smtp-pro' ); ?>
			</button>
		</p>
		<?php
		$this->post_form_hidden_field();
	}

	/**
	 * Process tab form submission ($_POST).
	 *
	 * @since 3.5.0
	 *
	 * @param array $data Post data specific for the plugin.
	 */
	public function process_post( $data ) {

		$this->check_admin_referer();

		$options = Options::init();

		foreach ( $this->loader->get_options_all() as $option ) {
			$group                     = $option->get_group();
			$data[ $group ]['enabled'] = ! empty( $data[ $group ]['enabled'] );

			if ( ! empty( $data[ $group ]['connections'] ) ) {
				$data[ $group ]['connections'] = array_values( array_unique( $data[ $group ]['connections'], SORT_REGULAR ) );
			}
		}

		$data['alert_events']['email_hard_bounced'] = ! empty( $data['alert_events']['email_hard_bounced'] );

		$all = $options->get_all();

		// Prevent connections array recursive merge.
		$all = array_merge( $all, $data );

		// All the sanitization is done there.
		$options->set( $all, false, true );

		WP::add_admin_notice(
			esc_html__( 'Settings were successfully saved.', 'wp-mail-smtp-pro' ),
			WP::ADMIN_NOTICE_SUCCESS
		);
	}
}
