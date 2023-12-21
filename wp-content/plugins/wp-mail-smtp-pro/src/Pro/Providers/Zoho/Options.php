<?php

namespace WPMailSMTP\Pro\Providers\Zoho;

use WPMailSMTP\Admin\ConnectionSettings;
use WPMailSMTP\ConnectionInterface;
use WPMailSMTP\Debug;
use WPMailSMTP\Providers\OptionsAbstract;

/**
 * Zoho mailer Options.
 *
 * @since 2.3.0
 */
class Options extends OptionsAbstract {

	/**
	 * Mailer slug.
	 *
	 * @since 2.3.0
	 */
	const SLUG = 'zoho';

	/**
	 * The list of Zoho domains.
	 * The region in which the user's Zoho Account Data resides.
	 * (One of the valid domains hosted with Zoho)
	 *
	 * @since 2.3.0
	 *
	 * @var array
	 */
	private $zoho_domains = [
		'com'    => 'United States (US)',
		'eu'     => 'Europe (EU)',
		'in'     => 'India (IN)',
		'com.cn' => 'China (CN)',
		'com.au' => 'Australia (AU)',
		'jp'     => 'Japan (JP)',
	];

	/**
	 * Outlook Options constructor.
	 *
	 * @since 2.3.0
	 *
	 * @param ConnectionInterface $connection The Connection object.
	 */
	public function __construct( $connection = null ) {

		parent::__construct(
			[
				'logo_url'    => wp_mail_smtp()->assets_url . '/images/providers/zoho.svg',
				'slug'        => self::SLUG,
				'title'       => esc_html__( 'Zoho Mail', 'wp-mail-smtp-pro' ),
				'description' => sprintf(
					wp_kses( /* translators: %1$s - URL to Zoho Mail page, %2$s - URL to Zoho Mail documentation page on WP Mail SMTP. */
						__( '<a href="%1$s" target="_blank" rel="noopener noreferrer">Zoho Mail</a> allows you to create secure email accounts for your business without providing your credit card details. It\'s easy to connect to Zoho Mail using its API. This allows you to send WordPress emails from your Zoho Mail address.<br><br>To get started, read our <a href="%2$s" target="_blank" rel="noopener noreferrer">Zoho Mail documentation</a>.', 'wp-mail-smtp-pro' ),
						[
							'br' => [],
							'a'  => [
								'href'   => [],
								'rel'    => [],
								'target' => [],
							],
						]
					),
					'https://www.zoho.com/mail/',
					esc_url( wp_mail_smtp()->get_utm_url( 'https://wpmailsmtp.com/docs/how-to-set-up-the-zoho-mailer-in-wp-mail-smtp/', 'Zoho Mail documentation' ) )
				),
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
	 * @since 2.3.0
	 */
	public function display_options() {

		// Do not display options if the PHP version requirement is not met.
		if ( ! $this->is_php_correct() ) {
			$this->display_php_warning();

			return;
		}

		$current_domain = $this->connection_options->get( $this->get_slug(), 'domain' );
		$current_domain = ! empty( $current_domain ) ? $current_domain : 'com';
		?>

		<!-- Domain / Region -->
		<div id="wp-mail-smtp-setting-row-<?php echo esc_attr( $this->get_slug() ); ?>-domain"
		     class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-text wp-mail-smtp-clear">
			<div class="wp-mail-smtp-setting-label">
				<label for="wp-mail-smtp-setting-<?php echo esc_attr( $this->get_slug() ); ?>-domain"><?php esc_html_e( 'Region', 'wp-mail-smtp-pro' ); ?></label>
			</div>
			<div class="wp-mail-smtp-setting-field">
				<select
					<?php echo $this->connection_options->is_const_defined( $this->get_slug(), 'domain' ) ? 'disabled' : ''; ?>
					name="wp-mail-smtp[<?php echo esc_attr( $this->get_slug() ); ?>][domain]"
					id="wp-mail-smtp-setting-<?php echo esc_attr( $this->get_slug() ); ?>-domain">
					<?php foreach ( $this->zoho_domains as $key => $label ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $current_domain, $key ); ?>><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
				</select>
				<p class="desc">
					<?php esc_html_e( 'The data center location used by your Zoho account', 'wp-mail-smtp-pro' ); ?><br/>
				</p>
			</div>
		</div>

		<!-- Client ID -->
		<div id="wp-mail-smtp-setting-row-<?php echo esc_attr( $this->get_slug() ); ?>-client_id"
			class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-text wp-mail-smtp-clear">
			<div class="wp-mail-smtp-setting-label">
				<label for="wp-mail-smtp-setting-<?php echo esc_attr( $this->get_slug() ); ?>-client_id"><?php esc_html_e( 'Client ID', 'wp-mail-smtp-pro' ); ?></label>
			</div>
			<div class="wp-mail-smtp-setting-field">
				<input name="wp-mail-smtp[<?php echo esc_attr( $this->get_slug() ); ?>][client_id]" type="text"
					value="<?php echo esc_attr( $this->connection_options->get( $this->get_slug(), 'client_id' ) ); ?>"
					<?php echo $this->connection_options->is_const_defined( $this->get_slug(), 'client_id' ) ? 'disabled' : ''; ?>
					id="wp-mail-smtp-setting-<?php echo esc_attr( $this->get_slug() ); ?>-client_id" spellcheck="false"
				/>
			</div>
		</div>

		<!-- Client secret -->
		<div id="wp-mail-smtp-setting-row-<?php echo esc_attr( $this->get_slug() ); ?>-client_secret"
			class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-text wp-mail-smtp-clear">
			<div class="wp-mail-smtp-setting-label">
				<label for="wp-mail-smtp-setting-<?php echo esc_attr( $this->get_slug() ); ?>-client_secret"><?php esc_html_e( 'Client Secret', 'wp-mail-smtp-pro' ); ?></label>
			</div>
			<div class="wp-mail-smtp-setting-field">
				<?php if ( $this->connection_options->is_const_defined( $this->get_slug(), 'client_secret' ) ) : ?>
					<input type="text" disabled value="****************************************"
						id="wp-mail-smtp-setting-<?php echo esc_attr( $this->get_slug() ); ?>-client_secret"
					/>
					<?php $this->display_const_set_message( 'WPMS_ZOHO_CLIENT_SECRET' ); ?>
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
				<input type="text" readonly="readonly"
					value="<?php echo esc_attr( Auth::get_plugin_auth_url() ); ?>"
					id="wp-mail-smtp-setting-<?php echo esc_attr( $this->get_slug() ); ?>-client_redirect"
				/>
				<button type="button" class="wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-grey wp-mail-smtp-setting-copy"
					title="<?php esc_attr_e( 'Copy URL to clipboard', 'wp-mail-smtp-pro' ); ?>"
					data-source_id="wp-mail-smtp-setting-<?php echo esc_attr( $this->get_slug() ); ?>-client_redirect">
					<span class="dashicons dashicons-admin-page"></span>
				</button>
				<p class="desc">
					<?php esc_html_e( 'This is the page on your site that you will be redirected to after you have authenticated with Zoho Mail.', 'wp-mail-smtp-pro' ); ?>
					<br>
					<?php esc_html_e( 'You need to copy this URL into "Client Details > Authorized Redirect URIs" web field for your application on Zoho Developer Console.', 'wp-mail-smtp-pro' ); ?>
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
	 * Display either an "Allow..." or "Remove..." authentication button.
	 *
	 * @since 2.3.0
	 */
	protected function display_auth_setting_action() {

		// Do the processing on the fly, as having ajax here is too complicated.
		$this->process_provider_remove();

		$auth = new Auth( $this->connection );
		?>

		<?php if ( $auth->is_clients_saved() ) : ?>

			<?php if ( $auth->is_auth_required() ) : ?>

				<a href="<?php echo esc_url( $auth->get_auth_url() ); ?>" class="wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-orange">
					<?php esc_html_e( 'Allow plugin to send emails using your Zoho Mail account', 'wp-mail-smtp-pro' ); ?>
				</a>
				<p class="desc">
					<?php esc_html_e( 'Click the button above to confirm authorization.', 'wp-mail-smtp-pro' ); ?>
				</p>

			<?php else : ?>

				<a href="<?php echo esc_url( wp_nonce_url( ( new ConnectionSettings( $this->connection ) )->get_admin_page_url(), 'zoho_remove', 'zoho_remove_nonce' ) ); ?>#wp-mail-smtp-setting-row-<?php echo esc_attr( $this->get_slug() ); ?>-authorize" class="wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-red js-wp-mail-smtp-provider-remove">
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
					<?php esc_html_e( 'Removing the OAuth connection will give you the ability to redo the OAuth connection or link to another Zoho Mail account.', 'wp-mail-smtp-pro' ); ?>
				</p>

			<?php endif; ?>

		<?php else : ?>

			<p class="inline-notice inline-error">
				<?php esc_html_e( 'To access this section, please add a Domain, Client ID and Client Secret, then click the Save Settings button.', 'wp-mail-smtp-pro' ); ?>
			</p>

		<?php
		endif;
	}

	/**
	 * Remove Provider connection.
	 *
	 * @since 2.3.0
	 */
	public function process_provider_remove() {

		if ( ! current_user_can( wp_mail_smtp()->get_capability_manage_options() ) ) {
			return;
		}

		if (
			! isset( $_GET['zoho_remove_nonce'] ) ||
			! wp_verify_nonce( sanitize_key( $_GET['zoho_remove_nonce'] ), 'zoho_remove' )
		) {
			return;
		}

		if ( $this->connection->get_mailer_slug() !== $this->get_slug() ) {
			return;
		}

		$old_opt = $this->connection_options->get_all_raw();

		foreach ( $old_opt[ $this->get_slug() ] as $key => $value ) {
			// Unset everything except Domain, Client ID and Client Secret.
			if ( ! in_array( $key, [ 'domain', 'client_id', 'client_secret' ], true ) ) {
				unset( $old_opt[ $this->get_slug() ][ $key ] );
			}
		}

		$this->connection_options->set( $old_opt );

		Debug::clear();
	}

	/**
	 * Get zoho domains in label/value pairs.
	 *
	 * @since 2.6.0
	 *
	 * @return array
	 */
	public function get_zoho_domains() {

		$data = [];

		foreach ( $this->zoho_domains as $value => $label ) {
			$data[] = [
				'label' => $label,
				'value' => $value,
			];
		}

		return $data;
	}
}
