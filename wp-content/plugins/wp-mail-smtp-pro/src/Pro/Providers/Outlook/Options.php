<?php

namespace WPMailSMTP\Pro\Providers\Outlook;

use WPMailSMTP\Admin\ConnectionSettings;
use WPMailSMTP\ConnectionInterface;
use WPMailSMTP\Providers\OptionsAbstract;

/**
 * Class Options
 */
class Options extends OptionsAbstract {

	/**
	 * Mailer slug.
	 *
	 * @since 1.5.0
	 */
	const SLUG = 'outlook';

	/**
	 * Outlook Options constructor.
	 *
	 * @since 1.5.0
	 * @since 2.3.0 Added supports parameter.
	 *
	 * @param ConnectionInterface $connection The Connection object.
	 */
	public function __construct( $connection = null ) {

		parent::__construct(
			[
				'logo_url'    => wp_mail_smtp()->assets_url . '/images/providers/microsoft.svg',
				'slug'        => self::SLUG,
				'title'       => esc_html__( '365 / Outlook', 'wp-mail-smtp-pro' ),
				'description' => sprintf(
					wp_kses( /* translators: %s - URL to Outlook doc. */
						__( 'Our Microsoft 365 / Outlook.com mailer allows you to send emails from a Microsoft email account on Outlook.com or a Microsoft 365 email address. It\'s free, but you\'ll need to provide a credit card to get started. The setup steps are more technical than other options, so we created a detailed guide to walk you through the process.<br><br>To get started, read our <a href="%s" target="_blank" rel="noopener noreferrer">Microsoft 365 / Outlook documentation</a>.', 'wp-mail-smtp-pro' ),
						[
							'br' => [],
							'a'  => [
								'href'   => [],
								'rel'    => [],
								'target' => [],
							],
						]
					),
					esc_url( wp_mail_smtp()->get_utm_url( 'https://wpmailsmtp.com/docs/how-to-set-up-the-outlook-mailer-in-wp-mail-smtp/', 'Microsoft 365 / Outlook documentation' ) )
				),
				'notices'     => [
					'educational' => wp_kses(
						__( 'The Microsoft 365 / Outlook mailer is a great choice if you\'re already using paid email services with Microsoft, as you\'ll have the benefit of high email sending limits without signing up for a separate service. Due to the fairly complex setup, however, this option is recommended for more technical users.<br><br>If you\'d prefer a more straightforward setup, then we\'d recommend considering one of the other mailer options.', 'wp-mail-smtp-pro' ),
						[
							'br' => [],
						]
					),
				],
				'php'         => '5.6',
				'supports'    => [
					'from_email'       => true,
					'from_name'        => true,
					'return_path'      => false,
					'from_email_force' => true,
					'from_name_force'  => true,
				],
			],
			$connection
		);
	}

	/**
	 * Output the mailer provider options.
	 *
	 * @since 1.5.0
	 */
	public function display_options() {

		// Do not display options if PHP version is not correct.
		if ( ! $this->is_php_correct() ) {
			$this->display_php_warning();

			return;
		}

		// Do not display options if there is no SSL certificate on a site.
		if ( ! is_ssl() ) {
			$this->display_ssl_warning();

			return;
		}
		?>

		<!-- Application ID -->
		<div id="wp-mail-smtp-setting-row-<?php echo esc_attr( $this->get_slug() ); ?>-client_id"
			class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-text wp-mail-smtp-clear">
			<div class="wp-mail-smtp-setting-label">
				<label for="wp-mail-smtp-setting-<?php echo esc_attr( $this->get_slug() ); ?>-client_id"><?php esc_html_e( 'Application ID', 'wp-mail-smtp-pro' ); ?></label>
			</div>
			<div class="wp-mail-smtp-setting-field">
				<input name="wp-mail-smtp[<?php echo esc_attr( $this->get_slug() ); ?>][client_id]" type="text"
					value="<?php echo esc_attr( $this->connection_options->get( $this->get_slug(), 'client_id' ) ); ?>"
					<?php echo $this->connection_options->is_const_defined( $this->get_slug(), 'client_id' ) ? 'disabled' : ''; ?>
					id="wp-mail-smtp-setting-<?php echo esc_attr( $this->get_slug() ); ?>-client_id" spellcheck="false"
				/>
			</div>
		</div>

		<!-- Application Password -->
		<div id="wp-mail-smtp-setting-row-<?php echo esc_attr( $this->get_slug() ); ?>-client_secret"
			class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-text wp-mail-smtp-clear">
			<div class="wp-mail-smtp-setting-label">
				<label for="wp-mail-smtp-setting-<?php echo esc_attr( $this->get_slug() ); ?>-client_secret"><?php esc_html_e( 'Application Password', 'wp-mail-smtp-pro' ); ?></label>
			</div>
			<div class="wp-mail-smtp-setting-field">
				<?php if ( $this->connection_options->is_const_defined( $this->get_slug(), 'client_secret' ) ) : ?>
					<input type="text" disabled value="****************************************"
						id="wp-mail-smtp-setting-<?php echo esc_attr( $this->get_slug() ); ?>-client_secret"
					/>
					<?php $this->display_const_set_message( 'WPMS_OUTLOOK_CLIENT_SECRET' ); ?>
				<?php else : ?>
					<input type="password" spellcheck="false"
						name="wp-mail-smtp[<?php echo esc_attr( $this->get_slug() ); ?>][client_secret]"
						value="<?php echo esc_attr( $this->connection_options->get( $this->get_slug(), 'client_secret' ) ); ?>"
						id="wp-mail-smtp-setting-<?php echo esc_attr( $this->get_slug() ); ?>-client_secret"
					/>
				<?php endif; ?>
			</div>
		</div>

		<!-- Redirect URIs -->
		<div id="wp-mail-smtp-setting-row-<?php echo esc_attr( $this->get_slug() ); ?>-client_redirect"
			class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-text wp-mail-smtp-clear">
			<div class="wp-mail-smtp-setting-label">
				<label for="wp-mail-smtp-setting-<?php echo esc_attr( $this->get_slug() ); ?>-client_redirect"><?php esc_html_e( 'Redirect URI', 'wp-mail-smtp-pro' ); ?></label>
			</div>
			<div class="wp-mail-smtp-setting-field">
				<input type="text" readonly="readonly" onfocus="this.select();"
					value="<?php echo esc_attr( Auth::get_plugin_auth_url() ); ?>"
					id="wp-mail-smtp-setting-<?php echo esc_attr( $this->get_slug() ); ?>-client_redirect"
				/>
				<button type="button" class="wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-grey wp-mail-smtp-setting-copy"
					title="<?php esc_attr_e( 'Copy URL to clipboard', 'wp-mail-smtp-pro' ); ?>"
					data-source_id="wp-mail-smtp-setting-<?php echo esc_attr( $this->get_slug() ); ?>-client_redirect">
					<span class="dashicons dashicons-admin-page"></span>
				</button>
				<p class="desc">
					<?php esc_html_e( 'This is the page on your site that you will be redirected to after you have authenticated with Microsoft.', 'wp-mail-smtp-pro' ); ?>
					<br>
					<?php esc_html_e( 'You need to copy this URL into "Authentication > Redirect URIs" web field for your application on Microsoft Azure site for your project there.', 'wp-mail-smtp-pro' ); ?>
				</p>
			</div>
		</div>

		<!-- Auth users button -->
		<div id="wp-mail-smtp-setting-row-<?php echo esc_attr( $this->get_slug() ); ?>-authorize"
			class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-text wp-mail-smtp-clear">
			<div class="wp-mail-smtp-setting-label">
				<label><?php esc_html_e( 'Authorization', 'wp-mail-smtp-pro' ); ?></label>
			</div>
			<div class="wp-mail-smtp-setting-field">
				<?php $this->display_auth_setting_action(); ?>
			</div>
		</div>

		<?php
	}

	/**
	 * Display either an "Allow..." or "Remove..." button.
	 *
	 * @since 1.5.0
	 */
	protected function display_auth_setting_action() {

		// Do the processing on the fly, as having ajax here is too complicated.
		$this->process_provider_remove();

		$auth = new Auth( $this->connection );
		?>

		<?php if ( $auth->is_clients_saved() ) : ?>

			<?php if ( $auth->is_auth_required() ) : ?>

				<a href="<?php echo esc_url( $auth->get_auth_url() ); ?>" class="wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-orange">
					<?php esc_html_e( 'Allow plugin to send emails using your Microsoft account', 'wp-mail-smtp-pro' ); ?>
				</a>
				<p class="desc">
					<?php esc_html_e( 'Click the button above to confirm authorization.', 'wp-mail-smtp-pro' ); ?>
				</p>

			<?php else : ?>

				<a href="<?php echo esc_url( wp_nonce_url( ( new ConnectionSettings( $this->connection ) )->get_admin_page_url(), 'outlook_remove', 'outlook_remove_nonce' ) ); ?>#wp-mail-smtp-setting-row-<?php echo esc_attr( $this->get_slug() ); ?>-authorize" class="wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-red js-wp-mail-smtp-provider-remove">
					<?php esc_html_e( 'Remove OAuth Connection', 'wp-mail-smtp-pro' ); ?>
				</a>
				<span class="connected-as">
					<?php
					$user = $this->connection_options->get( $this->get_slug(), 'user_details' );

					if ( ! empty( $user['email'] ) && ! empty( $user['display_name'] ) ) {
						printf(
							/* translators: %s - Display name and email, as received from oAuth provider. */
							esc_html__( 'Connected as %s', 'wp-mail-smtp-pro' ),
							'<code>' . esc_html( $user['display_name'] . ' <' . $user['email'] . '>' ) . '</code>'
						);
					}
					?>
				</span>
				<p class="desc">
					<?php esc_html_e( 'Removing the OAuth connection will give you an ability to redo the OAuth connection or link to another Microsoft account.', 'wp-mail-smtp-pro' ); ?>
				</p>

			<?php endif; ?>

		<?php else : ?>

			<p class="inline-notice inline-error">
				<?php esc_html_e( 'To access this section, please add an Application ID and Application Password, then click the Save Settings button.', 'wp-mail-smtp-pro' ); ?>
			</p>

		<?php
		endif;
	}

	/**
	 * Remove Provider connection.
	 *
	 * @since 1.5.0
	 */
	public function process_provider_remove() {

		if ( ! current_user_can( wp_mail_smtp()->get_capability_manage_options() ) ) {
			return;
		}

		if (
			! isset( $_GET['outlook_remove_nonce'] ) ||
			! wp_verify_nonce( sanitize_key( $_GET['outlook_remove_nonce'] ), 'outlook_remove' )
		) {
			return;
		}

		if ( $this->connection->get_mailer_slug() !== $this->get_slug() ) {
			return;
		}

		$old_opt = $this->connection_options->get_all_raw();

		foreach ( $old_opt[ $this->get_slug() ] as $key => $value ) {
			// Unset everything except App ID and Password.
			if ( ! in_array( $key, array( 'client_id', 'client_secret' ), true ) ) {
				unset( $old_opt[ $this->get_slug() ][ $key ] );
			}
		}

		$this->connection_options->set( $old_opt );
	}
}
