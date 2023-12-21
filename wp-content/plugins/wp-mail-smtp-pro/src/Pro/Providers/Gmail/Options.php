<?php

namespace WPMailSMTP\Pro\Providers\Gmail;

use WPMailSMTP\Admin\ConnectionSettings;
use WPMailSMTP\ConnectionInterface;
use WPMailSMTP\Helpers\UI;
use WPMailSMTP\Providers\Gmail\Options as LiteOptions;
use WPMailSMTP\Providers\OptionsAbstract;

/**
 * Class Options.
 *
 * @since 3.11.0
 */
class Options extends OptionsAbstract {

	/**
	 * Mailer slug.
	 *
	 * @since 3.11.0
	 */
	const SLUG = 'gmail';

	/**
	 * Gmail Options constructor.
	 *
	 * @since 3.11.0
	 *
	 * @param ConnectionInterface $connection The Connection object.
	 */
	public function __construct( $connection = null ) {

		parent::__construct(
			[
				'logo_url'    => wp_mail_smtp()->assets_url . '/images/providers/google.svg',
				'slug'        => self::SLUG,
				'title'       => esc_html__( 'Google / Gmail', 'wp-mail-smtp-pro' ),
				'description' => sprintf(
					wp_kses( /* translators: %s - URL to our Gmail doc. */
						__( 'Our Gmail mailer works with any Gmail or Google Workspace account via the Google API. You can send WordPress emails from your main email address or a Gmail alias, and it\'s more secure than connecting to Gmail using SMTP credentials. We now have a One-Click Setup, which simply asks you to authorize your Google account to use our app and takes care of everything for you. Alternatively, you can connect manually, which involves several steps that are more technical than other mailer options, so we created a detailed guide to walk you through the process.<br><br>To get started, read our <a href="%s" target="_blank" rel="noopener noreferrer">Gmail documentation</a>.', 'wp-mail-smtp-pro' ),
						[
							'br' => [],
							'a'  => [
								'href'   => [],
								'rel'    => [],
								'target' => [],
							],
						]
					),
					esc_url( wp_mail_smtp()->get_utm_url( 'https://wpmailsmtp.com/docs/how-to-set-up-the-gmail-mailer-in-wp-mail-smtp/', 'Gmail documentation' ) )
				),
				'notices'     => [
					'educational' => wp_kses(
						__( 'The Gmail mailer works well for sites that send low numbers of emails. However, Gmail\'s API has rate limitations and a number of additional restrictions that can lead to challenges during setup.<br><br>If you expect to send a high volume of emails, or if you find that your web host is not compatible with the Gmail API restrictions, then we recommend considering a different mailer option.', 'wp-mail-smtp-pro' ),
						[
							'br' => [],
						]
					),
				],
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
	 * @since 3.11.0
	 */
	public function display_options() {

		// Do not display options if PHP version is not correct.
		if ( ! $this->is_php_correct() ) {
			$this->display_php_warning();

			return;
		}

		$lite_options            = new LiteOptions( $this->connection );
		$one_click_setup_enabled = $this->connection_options->get( 'gmail', 'one_click_setup_enabled' );
		?>

		<div class="wp-mail-smtp-setting-row">
			<div class="wp-mail-smtp-setting-label">
				<label for="wp-mail-smtp-setting-<?php echo esc_attr( $this->get_slug() ); ?>-one_click_setup_enabled">
					<?php esc_html_e( 'One-Click Setup', 'wp-mail-smtp-pro' ); ?>
				</label>
			</div>
			<div class="wp-mail-smtp-setting-field">
				<?php
				UI::toggle(
					[
						'name'    => 'wp-mail-smtp[' . esc_attr( $this->get_slug() ) . '][one_click_setup_enabled]',
						'id'      => 'wp-mail-smtp-setting-' . esc_attr( $this->get_slug() ) . '-one_click_setup_enabled',
						'checked' => $one_click_setup_enabled,
					]
				);
				?>
				<p class="desc">
					<?php esc_html_e( 'Provides a quick and easy way to connect to Google that doesn\'t require creating your own app.', 'wp-mail-smtp-pro' ); ?>
				</p>
			</div>
		</div>

		<div class="wp-mail-smtp-mailer-option__group wp-mail-smtp-mailer-option__group--gmail-custom" <?php echo $one_click_setup_enabled === true ? 'style="display: none;"' : ''; ?>>
			<?php $lite_options->display_options(); ?>
		</div>

		<div class="wp-mail-smtp-mailer-option__group wp-mail-smtp-mailer-option__group--gmail-one_click_setup" <?php echo $one_click_setup_enabled !== true ? 'style="display: none;"' : ''; ?>>
			<?php if ( ! wp_mail_smtp()->get_pro()->get_license()->is_valid() ) : ?>
				<!-- License notice. -->
				<div class="wp-mail-smtp-setting-row" style="margin-top: -10px;">
					<div class="wp-mail-smtp-setting-field">
						<?php $this->display_license_notice(); ?>
					</div>
				</div>
			<?php endif; ?>

			<!-- Authorization button. -->
			<div id="wp-mail-smtp-setting-row-gmail-one-click-setup-authorize"  class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-text">
				<div class="wp-mail-smtp-setting-label">
					<label><?php esc_html_e( 'Authorization', 'wp-mail-smtp-pro' ); ?></label>
				</div>
				<div class="wp-mail-smtp-setting-field">
					<?php $this->display_auth_setting_action(); ?>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Output license notice if it's not valid.
	 *
	 * @since 3.11.0
	 */
	private function display_license_notice() {

		$license                 = wp_mail_smtp()->get_pro()->get_license();
		$one_click_setup_enabled = $this->connection_options->get( 'gmail', 'one_click_setup_enabled' );
		?>
		<?php if ( $license->is_expired() ) : ?>
			<?php if ( $one_click_setup_enabled ) : ?>
				<p class="inline-notice inline-error">
					<?php
					printf(
						wp_kses( /* translators: %1$s - WPMailSMTP.com renew URL. */
							__( 'One-Click Setup requires an active license. <a href="%1$s" target="_blank" rel="noopener noreferrer">Renew your license</a> and reconnect your authorization.', 'wp-mail-smtp-pro' ),
							[
								'a' => [
									'href'   => [],
									'target' => [],
									'rel'    => [],
								],
							]
						),
						esc_url(
							$license->get_renewal_link(
								[
									'medium'  => 'gmail-one-click-setting-notice',
									'content' => 'Renew your license',
								]
							)
						)
					);
					?>
				</p>
			<?php else : ?>
				<p class="inline-notice inline-info">
					<?php esc_html_e( 'One-Click Setup requires an active license. You can renew your license above to proceed with this One-Click Setup.', 'wp-mail-smtp-pro' ); ?>
				</p>
			<?php endif; ?>
		<?php elseif ( ! $license->is_valid() ) : ?>
			<?php if ( $one_click_setup_enabled ) : ?>
				<p class="inline-notice inline-error">
					<?php esc_html_e( 'One-Click Setup requires an active license. Verify your license above to proceed with this One-Click Setup.', 'wp-mail-smtp-pro' ); ?>
				</p>
			<?php else : ?>
				<p class="inline-notice inline-info">
					<?php esc_html_e( 'One-Click Setup requires an active license. You can verify your license above to proceed with this One-Click Setup.', 'wp-mail-smtp-pro' ); ?>
				</p>
			<?php endif; ?>
		<?php endif; ?>
		<?php
	}

	/**
	 * Display either an "Allow..." or "Remove..." button.
	 *
	 * @since 3.11.0
	 */
	private function display_auth_setting_action() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		// Do the processing on the fly, as having ajax here is too complicated.
		$this->process_provider_remove();

		$auth    = new Auth( $this->connection );
		$license = wp_mail_smtp()->get_pro()->get_license();
		?>
		<?php if ( $this->connection->get_mailer_slug() === self::SLUG ) : ?>
			<?php if ( $auth->is_auth_required() ) : ?>
				<a href="<?php echo esc_url( $auth->get_auth_url() ); ?>" class="wp-mail-smtp-google-sign-in-btn<?php echo ! $license->is_valid() ? ' wp-mail-smtp-google-sign-in-btn--disabled' : ''; ?>">
					<div class="wp-mail-smtp-google-sign-in-btn__icon">
						<svg width="46" height="46" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><defs><rect id="wp-mail-smtp-google-sign-in-icon__b" x="0" y="0" width="40" height="40" rx="2"/><rect id="wp-mail-smtp-google-sign-in-icon__c" x="5" y="5" width="38" height="38" rx="1"/><filter x="-50%" y="-50%" width="200%" height="200%" filterUnits="objectBoundingBox" id="wp-mail-smtp-google-sign-in-icon__a"><feOffset dy="1" in="SourceAlpha" result="shadowOffsetOuter1"/><feGaussianBlur stdDeviation=".5" in="shadowOffsetOuter1" result="shadowBlurOuter1"/><feColorMatrix values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.168 0" in="shadowBlurOuter1" result="shadowMatrixOuter1"/><feOffset in="SourceAlpha" result="shadowOffsetOuter2"/><feGaussianBlur stdDeviation=".5" in="shadowOffsetOuter2" result="shadowBlurOuter2"/><feColorMatrix values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.084 0" in="shadowBlurOuter2" result="shadowMatrixOuter2"/><feMerge><feMergeNode in="shadowMatrixOuter1"/><feMergeNode in="shadowMatrixOuter2"/><feMergeNode in="SourceGraphic"/></feMerge></filter></defs><g fill="none" fill-rule="evenodd"><g class="wp-mail-smtp-google-sign-in-icon__border" transform="translate(3 3)" filter="url(#wp-mail-smtp-google-sign-in-icon__a)"><use fill="#4285F4" xlink:href="#wp-mail-smtp-google-sign-in-icon__b"/><use xlink:href="#wp-mail-smtp-google-sign-in-icon__b"/><use xlink:href="#wp-mail-smtp-google-sign-in-icon__b"/><use xlink:href="#wp-mail-smtp-google-sign-in-icon__b"/></g><g class="wp-mail-smtp-google-sign-in-icon__bg" transform="translate(-1 -1)"><use fill="#FFF" xlink:href="#wp-mail-smtp-google-sign-in-icon__c"/><use xlink:href="#wp-mail-smtp-google-sign-in-icon__c"/><use xlink:href="#wp-mail-smtp-google-sign-in-icon__c"/><use xlink:href="#wp-mail-smtp-google-sign-in-icon__c"/></g><path class="wp-mail-smtp-google-sign-in-icon__symbol" d="M31.64 23.205c0-.639-.057-1.252-.164-1.841H23v3.481h4.844a4.14 4.14 0 0 1-1.796 2.716v2.259h2.908c1.702-1.567 2.684-3.875 2.684-6.615Z" fill="#4285F4"/><path class="wp-mail-smtp-google-sign-in-icon__symbol" d="M23 32c2.43 0 4.467-.806 5.956-2.18l-2.908-2.259c-.806.54-1.837.86-3.048.86-2.344 0-4.328-1.584-5.036-3.711h-3.007v2.332A8.997 8.997 0 0 0 23 32Z" fill="#34A853"/><path class="wp-mail-smtp-google-sign-in-icon__symbol" d="M17.964 24.71a5.41 5.41 0 0 1-.282-1.71c0-.593.102-1.17.282-1.71v-2.332h-3.007A8.996 8.996 0 0 0 14 23c0 1.452.348 2.827.957 4.042l3.007-2.332Z" fill="#FBBC05"/><path class="wp-mail-smtp-google-sign-in-icon__symbol" d="M23 17.58c1.321 0 2.508.454 3.44 1.345l2.582-2.58C27.463 14.891 25.426 14 23 14a8.997 8.997 0 0 0-8.043 4.958l3.007 2.332c.708-2.127 2.692-3.71 5.036-3.71Z" fill="#EA4335"/><path d="M14 14h18v18H14V14Z"/></g></svg>
					</div>
					<div class="wp-mail-smtp-google-sign-in-btn__text">
						<?php esc_html_e( 'Sign in with Google', 'wp-mail-smtp-pro' ); ?>
					</div>
				</a>
			<?php elseif ( $auth->is_reauth_required() && $license->is_valid() ) : ?>
				<div class="wp-mail-smtp-connected-row">
					<a href="<?php echo esc_url( $auth->get_reauth_url() ); ?>" class="wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-blueish">
						<?php esc_html_e( 'Reconnect', 'wp-mail-smtp-pro' ); ?>
					</a>
					<a href="<?php echo esc_url( $this->get_remove_connection_url() ); ?>" class="wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-red js-wp-mail-smtp-provider-remove">
						<?php esc_html_e( 'Remove OAuth Connection', 'wp-mail-smtp-pro' ); ?>
					</a>
					<div class="wp-mail-smtp-connected-row__info">
						<?php
						$user = $auth->get_user_info();

						if ( ! empty( $user['email'] ) ) {
							printf(
								/* translators: %1$s - email address, as received from Google API. */
								esc_html__( 'Connected as %1$s', 'wp-mail-smtp-pro' ),
								'<code>' . esc_html( $user['email'] ) . '</code>'
							);
						}
						?>
					</div>
				</div>
				<p class="inline-notice inline-error">
					<?php esc_html_e( 'Your Google account connection has expired. Please reconnect your account.', 'wp-mail-smtp-pro' ); ?>
				</p>
			<?php else : ?>
				<div class="wp-mail-smtp-connected-row">
					<a href="<?php echo esc_url( $this->get_remove_connection_url() ); ?>" class="wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-red js-wp-mail-smtp-provider-remove">
						<?php esc_html_e( 'Remove OAuth Connection', 'wp-mail-smtp-pro' ); ?>
					</a>
					<div class="wp-mail-smtp-connected-row__info">
						<?php
						$user = $auth->get_user_info();

						if ( ! empty( $user['email'] ) ) {
							printf(
								/* translators: %1$s - email address, as received from Google API. */
								esc_html__( 'Connected as %1$s', 'wp-mail-smtp-pro' ),
								'<code>' . esc_html( $user['email'] ) . '</code>'
							);
						}
						?>
					</div>
				</div>
				<p class="desc">
					<?php
					printf(
						wp_kses( /* translators: %s - URL to Google Gmail alias documentation page. */
							__( 'If you want to use a different From Email address you can set up a Google email alias. <a href="%s" target="_blank" rel="noopener noreferrer">Follow these instructions</a> and then select the From Email at the top of this page.', 'wp-mail-smtp-pro' ),
							[
								'a' => [
									'href'   => [],
									'rel'    => [],
									'target' => [],
								],
							]
						),
						esc_url( wp_mail_smtp()->get_utm_url( 'https://wpmailsmtp.com/gmail-send-from-alias-wp-mail-smtp/', 'Gmail aliases description - Follow these instructions' ) )
					);
					?>
				</p>
				<p class="desc">
					<?php esc_html_e( 'You can also send emails with different From Email addresses, by disabling the Force From Email setting and using registered aliases throughout your WordPress site as the From Email addresses.', 'wp-mail-smtp-pro' ); ?>
				</p>
				<p class="desc">
					<?php esc_html_e( 'Removing the OAuth connection will give you an ability to redo the OAuth connection or link to another Google account.', 'wp-mail-smtp-pro' ); ?>
				</p>
			<?php endif; ?>
		<?php else : ?>
			<p class="inline-notice inline-error">
				<?php esc_html_e( 'You need to save settings before you can proceed.', 'wp-mail-smtp-pro' ); ?>
			</p>
		<?php endif; ?>
		<?php
	}

	/**
	 * Get OAuth connection remove URL.
	 *
	 * @since 3.11.0
	 *
	 * @return string
	 */
	private function get_remove_connection_url() {

		return wp_nonce_url( ( new ConnectionSettings( $this->connection ) )->get_admin_page_url(), 'gmail_one_click_setup_remove', 'gmail_one_click_setup_remove_nonce' ) . '#wp-mail-smtp-setting-row-gmail-one-click-setup-authorize';
	}

	/**
	 * Remove Provider OAuth connection.
	 *
	 * @since 3.11.0
	 */
	public function process_provider_remove() {

		if ( ! current_user_can( wp_mail_smtp()->get_capability_manage_options() ) ) {
			return;
		}

		if (
			! isset( $_GET['gmail_one_click_setup_remove_nonce'] ) ||
			! wp_verify_nonce( sanitize_key( $_GET['gmail_one_click_setup_remove_nonce'] ), 'gmail_one_click_setup_remove' )
		) {
			return;
		}

		if ( $this->connection->get_mailer_slug() !== $this->get_slug() ) {
			return;
		}

		$auth = new Auth( $this->connection );

		$auth->get_client()->remove_connection();

		$old_opt = $this->connection_options->get_all_raw();

		unset( $old_opt[ $this->get_slug() ]['one_click_setup_credentials'] );
		unset( $old_opt[ $this->get_slug() ]['one_click_setup_user_details'] );
		unset( $old_opt[ $this->get_slug() ]['one_click_setup_status'] );

		$this->connection_options->set( $old_opt );
	}
}
