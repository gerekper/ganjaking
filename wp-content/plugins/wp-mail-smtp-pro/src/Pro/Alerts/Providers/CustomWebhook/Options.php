<?php

namespace WPMailSMTP\Pro\Alerts\Providers\CustomWebhook;

use WPMailSMTP\Pro\Alerts\AbstractOptions;

/**
 * Class Options. Custom webhook settings.
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
	const SLUG = 'custom_webhook';

	/**
	 * Constructor.
	 *
	 * @since 3.5.0
	 */
	public function __construct() {

		$description = wp_kses(
			sprintf(
				/* translators: %s - Documentation link. */
				__( 'Paste in the webhook URL you’d like to use to receive alerts when email sending fails. <a href="%s" target="_blank" rel="noopener noreferrer">Read our documentation on setting up webhook alerts</a>.', 'wp-mail-smtp-pro' ),
				esc_url(
					wp_mail_smtp()->get_utm_url(
						'https://wpmailsmtp.com/docs/setting-up-email-alerts/#webhooks',
						[
							'medium'  => 'Alerts Settings',
							'content' => 'Webhook Documentation',
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
				'title'               => esc_html__( 'Webhook', 'wp-mail-smtp-pro' ),
				'description'         => $description,
				'add_connection_text' => esc_html__( 'Add Another Webhook URL', 'wp-mail-smtp-pro' ),
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
					'webhook_url' => '',
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

		$webhook_url = isset( $connection['webhook_url'] ) ? $connection['webhook_url'] : '';

		ob_start();
		?>
		<div class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-alert-connection-options">
			<div class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-text">
				<div class="wp-mail-smtp-setting-label">
					<label for="wp-mail-smtp-setting-alert-<?php echo esc_attr( $slug ); ?>-webhook-url-<?php echo esc_attr( $i ); ?>">
						<?php esc_html_e( 'Webhook URL', 'wp-mail-smtp-pro' ); ?>
					</label>
				</div>
				<div class="wp-mail-smtp-setting-field">
					<?php
					printf(
						'<input name="wp-mail-smtp[alert_%1$s][connections][%2$s][webhook_url]" type="text" value="%3$s" id="wp-mail-smtp-setting-alert-%1$s-webhook-url-%2$s" spellcheck="false" %4$s %5$s/>',
						esc_attr( $slug ),
						esc_attr( $i ),
						esc_attr( $webhook_url ),
						disabled( true, $this->options->is_const_defined( $group, 'connections' ), false ),
						$this->options->get( $group, 'enabled' ) ? 'required' : ''
					);
					?>

					<?php if ( $this->options->is_const_defined( $group, 'connections' ) ) : ?>
						<p class="desc">
							<?php
							//phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							echo $this->options->get_const_set_message( 'WPMS_ALERT_CUSTOM_WEBHOOK_URL' );
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
