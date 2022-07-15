<?php

namespace WPMailSMTP\Pro\Alerts\Providers\Email;

use WPMailSMTP\Pro\Alerts\AbstractOptions;

/**
 * Class Options. Email API settings.
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
	const SLUG = 'email';

	/**
	 * Constructor.
	 *
	 * @since 3.5.0
	 */
	public function __construct() {

		$description = wp_kses(
			sprintf(
				/* translators: %s - Documentation link. */
				__( 'Enter the email addresses (3 max) you’d like to use to receive alerts when email sending fails. <a href="%s" target="_blank" rel="noopener noreferrer">Read our documentation on setting up email alerts</a>.', 'wp-mail-smtp-pro' ),
				esc_url(
					wp_mail_smtp()->get_utm_url(
						'https://wpmailsmtp.com/docs/setting-up-email-alerts/#email',
						[
							'medium'  => 'Alerts Settings',
							'content' => 'Email Documentation',
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

		if ( ! wp_mail_smtp()->pro->get_license()->is_valid() ) {
			ob_start();
			?>
			<div class="notice notice-error inline">
				<p>
					<?php
					echo wp_kses(
						sprintf(
							/* translators: %s - Plugin general settings page link. */
							__( 'This integration will not work without a valid WP Mail SMTP license key. <a href="%s">Please verify your license key</a>.', 'wp-mail-smtp-pro' ),
							esc_url( wp_mail_smtp()->get_admin()->get_admin_page_url() )
						),
						[
							'a' => [
								'href' => [],
							],
						]
					);
					?>
				</p>
			</div>
			<?php
			$description .= ob_get_clean();
		}

		parent::__construct(
			[
				'slug'                  => self::SLUG,
				'title'                 => esc_html__( 'Email', 'wp-mail-smtp-pro' ),
				'description'           => $description,
				'add_connection_text'   => esc_html__( 'Add Another Email Address', 'wp-mail-smtp-pro' ),
				'max_connections_count' => 3,
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
					'send_to' => '',
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
	public function get_connection_options( $connection, $i ) {

		$slug  = $this->get_slug();
		$group = $this->get_group();

		$send_to = isset( $connection['send_to'] ) ? $connection['send_to'] : '';

		ob_start();
		?>
		<div class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-alert-connection-options">
			<div class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-text">
				<div class="wp-mail-smtp-setting-label">
					<label for="wp-mail-smtp-setting-alert-<?php echo esc_attr( $slug ); ?>-send-to-<?php echo esc_attr( $i ); ?>">
						<?php esc_html_e( 'Send To', 'wp-mail-smtp-pro' ); ?>
					</label>
				</div>
				<div class="wp-mail-smtp-setting-field">
					<?php
					printf(
						'<input name="wp-mail-smtp[alert_%1$s][connections][%2$s][send_to]" type="text" value="%3$s" id="wp-mail-smtp-setting-alert-%1$s-send-to-%2$s" spellcheck="false" %4$s %5$s/>',
						esc_attr( $slug ),
						esc_attr( $i ),
						esc_attr( $send_to ),
						disabled( true, $this->options->is_const_defined( $group, 'connections' ), false ),
						$this->options->get( $group, 'enabled' ) ? 'required' : ''
					);
					?>

					<?php if ( $this->options->is_const_defined( $group, 'connections' ) ) : ?>
						<p class="desc">
							<?php
							//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo $this->options->get_const_set_message( 'WPMS_ALERT_EMAIL_SEND_TO' );
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
