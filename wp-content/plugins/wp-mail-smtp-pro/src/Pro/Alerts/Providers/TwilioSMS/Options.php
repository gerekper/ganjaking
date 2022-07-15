<?php

namespace WPMailSMTP\Pro\Alerts\Providers\TwilioSMS;

use WPMailSMTP\Pro\Alerts\AbstractOptions;

/**
 * Class Options. Twilio SMS settings.
 *
 * @since 3.5.0
 */
class Options extends AbstractOptions {

	/**
	 * Provider slug.
	 *
	 * @since 3.5.0
	 *
	 * @var string
	 */
	const SLUG = 'twilio_sms';

	/**
	 * Constructor.
	 *
	 * @since 3.5.0
	 */
	public function __construct() {

		$description = wp_kses(
			sprintf(
				/* translators: %s - Documentation link. */
				__( 'To receive SMS alerts, you’ll need a Twilio account. <a href="%s" target="_blank" rel="noopener noreferrer">Read our documentation</a> to learn how to set up Twilio SMS, then enter your connection details below.', 'wp-mail-smtp-pro' ),
				esc_url(
					wp_mail_smtp()->get_utm_url(
						'https://wpmailsmtp.com/docs/setting-up-email-alerts/#sms',
						[
							'medium'  => 'Alerts Settings',
							'content' => 'Twilio Documentation',
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

		parent::__construct(
			[
				'slug'                => self::SLUG,
				'title'               => esc_html__( 'SMS via Twilio', 'wp-mail-smtp-pro' ),
				'description'         => $description,
				'add_connection_text' => esc_html__( 'Add Another Account', 'wp-mail-smtp-pro' ),
			]
		);
	}

	/**
	 * Output the provider options.
	 *
	 * @since 3.5.0
	 */
	public function display_options() {

		$connections = $this->options->get( $this->get_group(), 'connections' );

		if ( empty( $connections ) ) {
			$connections = [
				[
					'account_sid'       => '',
					'auth_token'        => '',
					'from_phone_number' => '',
					'to_phone_number'   => '',
				],
			];
		}

		foreach ( $connections as $i => $connection ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $this->get_connection_options( $connection, $i );
		}
	}

	/**
	 * Get single connection options.
	 *
	 * @since 3.5.0
	 *
	 * @param array  $connection Connection settings.
	 * @param string $i          Connection index.
	 *
	 * @return string
	 */
	public function get_connection_options( $connection, $i ) { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		$slug  = $this->get_slug();
		$group = $this->get_group();

		$account_sid       = isset( $connection['account_sid'] ) ? $connection['account_sid'] : '';
		$auth_token        = isset( $connection['auth_token'] ) ? $connection['auth_token'] : '';
		$from_phone_number = isset( $connection['from_phone_number'] ) ? $connection['from_phone_number'] : '';
		$to_phone_number   = isset( $connection['to_phone_number'] ) ? $connection['to_phone_number'] : '';

		$is_enabled                   = $this->options->get( $group, 'enabled' );
		$is_connections_const_defined = $this->options->is_const_defined( $group, 'connections' );

		ob_start();
		?>
		<div class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-alert-connection-options">
			<div class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-text">
				<div class="wp-mail-smtp-setting-label">
					<label for="wp-mail-smtp-setting-alert-<?php echo esc_attr( $slug ); ?>-account-sid-<?php echo esc_attr( $i ); ?>">
						<?php esc_html_e( 'Twilio Account ID', 'wp-mail-smtp-pro' ); ?>
					</label>
				</div>
				<div class="wp-mail-smtp-setting-field">
					<?php
					printf(
						'<input name="wp-mail-smtp[alert_%1$s][connections][%2$s][account_sid]" type="text" value="%3$s" id="wp-mail-smtp-setting-alert-%1$s-account-sid-%2$s" spellcheck="false" %4$s %5$s/>',
						esc_attr( $slug ),
						esc_attr( $i ),
						esc_attr( $account_sid ),
						disabled( true, $is_connections_const_defined, false ),
						$is_enabled ? 'required' : ''
					);
					?>

					<?php if ( $is_connections_const_defined ) : ?>
						<p class="desc">
							<?php
							//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo $this->options->get_const_set_message( 'WPMS_ALERT_TWILIO_SMS_ACCOUNT_SID' );
							?>
						</p>
					<?php endif; ?>
				</div>
			</div>

			<div class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-text">
				<div class="wp-mail-smtp-setting-label">
					<label for="wp-mail-smtp-setting-alert-<?php echo esc_attr( $slug ); ?>-auth-token-<?php echo esc_attr( $i ); ?>">
						<?php esc_html_e( 'Twilio Auth Token', 'wp-mail-smtp-pro' ); ?>
					</label>
				</div>
				<div class="wp-mail-smtp-setting-field">
					<?php
					printf(
						'<input name="wp-mail-smtp[alert_%1$s][connections][%2$s][auth_token]" type="text" value="%3$s" id="wp-mail-smtp-setting-alert-%1$s-auth-token-%2$s" spellcheck="false" %4$s %5$s/>',
						esc_attr( $slug ),
						esc_attr( $i ),
						esc_attr( $auth_token ),
						disabled( true, $is_connections_const_defined, false ),
						$is_enabled ? 'required' : ''
					);
					?>

					<?php if ( $is_connections_const_defined ) : ?>
						<p class="desc">
							<?php
							//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo $this->options->get_const_set_message( 'WPMS_ALERT_TWILIO_SMS_AUTH_TOKEN' );
							?>
						</p>
					<?php endif; ?>
				</div>
			</div>

			<div class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-text">
				<div class="wp-mail-smtp-setting-label">
					<label for="wp-mail-smtp-setting-alert-<?php echo esc_attr( $slug ); ?>-from-phone-number-<?php echo esc_attr( $i ); ?>">
						<?php esc_html_e( 'From Phone Number', 'wp-mail-smtp-pro' ); ?>
					</label>
				</div>
				<div class="wp-mail-smtp-setting-field">
					<?php
					printf(
						'<input name="wp-mail-smtp[alert_%1$s][connections][%2$s][from_phone_number]" type="text" value="%3$s" id="wp-mail-smtp-setting-alert-%1$s-from-phone-number-%2$s" spellcheck="false" %4$s %5$s/>',
						esc_attr( $slug ),
						esc_attr( $i ),
						esc_attr( $from_phone_number ),
						disabled( true, $is_connections_const_defined, false ),
						$is_enabled ? 'required' : ''
					);
					?>

					<?php if ( $is_connections_const_defined ) : ?>
						<p class="desc">
							<?php
							//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo $this->options->get_const_set_message( 'WPMS_ALERT_TWILIO_SMS_FROM_PHONE_NUMBER' );
							?>
						</p>
					<?php endif; ?>
				</div>
			</div>

			<div class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-text">
				<div class="wp-mail-smtp-setting-label">
					<label for="wp-mail-smtp-setting-alert-<?php echo esc_attr( $slug ); ?>-to-phone-number-<?php echo esc_attr( $i ); ?>">
						<?php esc_html_e( 'To Phone Number', 'wp-mail-smtp-pro' ); ?>
					</label>
				</div>
				<div class="wp-mail-smtp-setting-field">
					<?php
					printf(
						'<input name="wp-mail-smtp[alert_%1$s][connections][%2$s][to_phone_number]" type="text" value="%3$s" id="wp-mail-smtp-setting-alert-%1$s-to-phone-number-%2$s" spellcheck="false" %4$s %5$s/>',
						esc_attr( $slug ),
						esc_attr( $i ),
						esc_attr( $to_phone_number ),
						disabled( true, $is_connections_const_defined, false ),
						$is_enabled ? 'required' : ''
					);
					?>

					<?php if ( $is_connections_const_defined ) : ?>
						<p class="desc">
							<?php
							//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo $this->options->get_const_set_message( 'WPMS_ALERT_TWILIO_SMS_TO_PHONE_NUMBER' );
							?>
						</p>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<?php

		return ob_get_clean();
	}
}
