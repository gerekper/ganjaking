<?php

namespace WPMailSMTP\Pro\Providers\AmazonSES;

use WPMailSMTP\ConnectionInterface;
use WPMailSMTP\Debug;
use WPMailSMTP\Geo;
use WPMailSMTP\Providers\OptionsAbstract;

/**
 * Class Options
 *
 * @since 1.5.0
 */
class Options extends OptionsAbstract {

	/**
	 * Mailer slug.
	 *
	 * @since 1.5.0
	 */
	const SLUG = 'amazonses';

	/**
	 * Outlook Options constructor.
	 *
	 * @since 1.5.0
	 *
	 * @param ConnectionInterface $connection The Connection object.
	 */
	public function __construct( $connection = null ) {

		parent::__construct(
			array(
				'logo_url'    => wp_mail_smtp()->assets_url . '/images/providers/aws.svg',
				'slug'        => self::SLUG,
				'title'       => esc_html__( 'Amazon SES', 'wp-mail-smtp-pro' ),
				'description' => wp_kses( __( 'Amazon SES is a transactional email provider that allows you to send email via its API. We recommend this mailer for existing users of Amazon Web Services because the setup steps are a little more complicated than other mailers.', 'wp-mail-smtp-pro' ), [ 'b' => [] ] ) . '<br><br>' .
				                 // phpcs:disable
				                 sprintf(
					                 wp_kses( /* translators: %s - WPMailSMTP.com URL. */
						                 __( 'To get started, read our <a href="%s" target="_blank" rel="noopener noreferrer">Amazon SES documentation</a>.', 'wp-mail-smtp-pro' ),
						                 array(
							                 'a' => array(
								                 'href'   => array(),
								                 'rel'    => array(),
								                 'target' => array(),
							                 ),
						                 )
					                 ),
					                 esc_url( wp_mail_smtp()->get_utm_url( 'https://wpmailsmtp.com/docs/how-to-set-up-the-amazon-ses-mailer-in-wp-mail-smtp/', 'Amazon SES documentation' ) )
				                 ),
								// phpcs:enable
				'notices'     => array(
					'educational' => wp_kses(
						__( 'The Amazon SES mailer will be a good choice for technically advanced users who already have experience working with Amazon\'s web services.<br><br>If you aren\'t sure whether this mailer sounds like the right fit for your site, then we recommend considering one of our other mailer options.', 'wp-mail-smtp-pro' ),
						[
							'br' => [],
						]
					),
				),
				'php'         => '5.6',
				'supports'    => [
					'from_email'       => true,
					'from_name'        => true,
					'return_path'      => false,
					'from_email_force' => true,
					'from_name_force'  => true,
				],
			),
			$connection
		);
	}

	/**
	 * Output the mailer provider options.
	 *
	 * @since 1.5.0
	 * @since 3.10.0 Added WPMS_AMAZONSES_DISPLAY_IDENTITIES constant check to control display of identity list.
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

		<!-- Access Key ID -->
		<div id="wp-mail-smtp-setting-row-<?php echo esc_attr( $this->get_slug() ); ?>-client_id"
			class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-text wp-mail-smtp-clear">
			<div class="wp-mail-smtp-setting-label">
				<label for="wp-mail-smtp-setting-<?php echo esc_attr( $this->get_slug() ); ?>-client_id">
					<?php esc_html_e( 'Access Key ID', 'wp-mail-smtp-pro' ); ?>
				</label>
			</div>
			<div class="wp-mail-smtp-setting-field">
				<input name="wp-mail-smtp[<?php echo esc_attr( $this->get_slug() ); ?>][client_id]" type="text"
					value="<?php echo esc_attr( $this->connection_options->get( $this->get_slug(), 'client_id' ) ); ?>"
					<?php echo $this->connection_options->is_const_defined( $this->get_slug(), 'client_id' ) ? 'disabled' : ''; ?>
					id="wp-mail-smtp-setting-<?php echo esc_attr( $this->get_slug() ); ?>-client_id" spellcheck="false"
				/>
			</div>
		</div>

		<!-- Secret Access Key -->
		<div id="wp-mail-smtp-setting-row-<?php echo esc_attr( $this->get_slug() ); ?>-client_secret"
			class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-text wp-mail-smtp-clear">
			<div class="wp-mail-smtp-setting-label">
				<label for="wp-mail-smtp-setting-<?php echo esc_attr( $this->get_slug() ); ?>-client_secret">
					<?php esc_html_e( 'Secret Access Key', 'wp-mail-smtp-pro' ); ?>
				</label>
			</div>
			<div class="wp-mail-smtp-setting-field">
				<?php if ( $this->connection_options->is_const_defined( $this->get_slug(), 'client_secret' ) ) : ?>
					<input type="text" disabled value="****************************************"
						id="wp-mail-smtp-setting-<?php echo esc_attr( $this->get_slug() ); ?>-client_secret"
					/>
					<?php $this->display_const_set_message( 'WPMS_AMAZONSES_CLIENT_SECRET' ); ?>
				<?php else : ?>
					<input type="password" spellcheck="false"
						name="wp-mail-smtp[<?php echo esc_attr( $this->get_slug() ); ?>][client_secret]"
						value="<?php echo esc_attr( $this->connection_options->get( $this->get_slug(), 'client_secret' ) ); ?>"
						id="wp-mail-smtp-setting-<?php echo esc_attr( $this->get_slug() ); ?>-client_secret"
					/>
				<?php endif; ?>
			</div>
		</div>

		<!-- Closest Region -->
		<div id="wp-mail-smtp-setting-row-<?php echo esc_attr( $this->get_slug() ); ?>-region"
			class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-text wp-mail-smtp-clear">
			<div class="wp-mail-smtp-setting-label">
				<label for="wp-mail-smtp-setting-<?php echo esc_attr( $this->get_slug() ); ?>-region">
					<?php esc_html_e( 'Closest Region', 'wp-mail-smtp-pro' ); ?>
				</label>
			</div>
			<div class="wp-mail-smtp-setting-field">
				<?php
				if ( $this->get_slug() === $this->connection->get_mailer_slug() ) {
					$is_region_guessed = false;
					$current_region    = Auth::prepare_region( $this->connection_options->get( $this->get_slug(), 'region' ) );

					if ( empty( $current_region ) ) {
						$current_region = $this->get_closest_region();

						$is_region_guessed = ! empty( $current_region );
					}
					?>
					<select
						<?php echo $this->connection_options->is_const_defined( $this->get_slug(), 'region' ) ? 'disabled' : ''; ?>
						name="wp-mail-smtp[<?php echo esc_attr( $this->get_slug() ); ?>][region]"
						id="wp-mail-smtp-setting-<?php echo esc_attr( $this->get_slug() ); ?>-region">
						<option value=""><?php esc_html_e( '--- Select region ---', 'wp-mail-smtp-pro' ); ?></option>
						<?php foreach ( Auth::get_regions_names() as $region => $label ) : ?>
							<option value="<?php echo esc_attr( $region ); ?>" <?php selected( $current_region, $region ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
					<p class="desc">
						<?php if ( $is_region_guessed ) { ?>
							<?php esc_html_e( 'The closest Amazon SES region to your website was preselected.', 'wp-mail-smtp-pro' ); ?><br/>
						<?php } else { ?>
							<?php esc_html_e( 'Please select the Amazon SES API region which is the closest to where your website is hosted.', 'wp-mail-smtp-pro' ); ?><br/>
						<?php } ?>
						<?php esc_html_e( 'This can help to decrease network latency between your site and Amazon SES, which will speed up email sending.', 'wp-mail-smtp-pro' ); ?>
					</p>
				<?php } else { ?>
					<p class="inline-notice inline-error"><?php esc_html_e( 'To access this section, please click the Save Settings button.', 'wp-mail-smtp-pro' ); ?></p>
				<?php } ?>
			</div>
		</div>

		<?php if ( ! defined( 'WPMS_AMAZONSES_DISPLAY_IDENTITIES' ) || WPMS_AMAZONSES_DISPLAY_IDENTITIES === true ) : ?>
		<!-- SES Identities (registered domains and emails) -->
		<div id="wp-mail-smtp-setting-row-<?php echo esc_attr( $this->get_slug() ); ?>-senders"
		     class="wp-mail-smtp-setting-row wp-mail-smtp-setting-row-text wp-mail-smtp-clear">
			<div class="wp-mail-smtp-setting-label">
				<label><?php esc_html_e( 'SES Identities', 'wp-mail-smtp-pro' ); ?></label>
			</div>
			<div class="wp-mail-smtp-setting-field">
				<div class="js-wp-mail-smtp-ses-identities-setting">
					<?php
						$ses_settings = $this->connection_options->get_group( $this->get_slug() );

						if (
							empty( $ses_settings['client_id'] ) ||
							empty( $ses_settings['client_secret'] ) ||
							empty( $ses_settings['region'] )
						) {
							echo '<p class="inline-notice inline-error">' . esc_html( $this->get_connection_not_ready_error_text() ) . '</p>';
						} else {
							echo wp_mail_smtp()->prepare_loader(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
					?>
				</div>
				<?php wp_nonce_field( 'wp_mail_smtp_pro_amazonses_load_ses_identities', 'wp_mail_smtp_pro_amazonses_load_ses_identities' ); ?>
			</div>
		</div>
		<?php endif; ?>

		<?php
	}

	/**
	 * Prepare the SES identities setting content.
	 * Will be used via AJAX request.
	 *
	 * @since 2.4.0
	 *
	 * @return false|string
	 */
	public function prepare_ses_identities_content() {

		ob_start();

		if ( ! $this->get_slug() === $this->connection->get_mailer_slug() ) {
			echo '<p class="inline-notice inline-error">' . esc_html( $this->get_connection_not_ready_error_text() ) . '</p>';

			return ob_get_clean();
		}

		$auth = new Auth( $this->connection );

		if ( ! $auth->is_connection_ready() ) {
			echo '<p class="inline-notice inline-error">' . esc_html( $this->get_connection_not_ready_error_text() ) . '</p>';

			return ob_get_clean();
		}

		// Prepare the SES identities.
		$table = new IdentitiesTable( $this->connection );

		$table->prepare_items();

		$error = Debug::get_last();

		if ( ! $table->has_items() && ! empty( $error ) ) {
			// Display an error message to a user.
			echo '<p class="inline-notice inline-error">' . esc_html( $error ) . '</p>';
			Debug::clear();

			return ob_get_clean();
		}
		?>

		<?php if ( $table->has_items() ) : ?>

			<div class="wp-mail-smtp-ses-identities-table">
				<?php $table->display(); ?>
			</div>

			<p class="desc" style="margin: 0 0 20px">
				<?php esc_html_e( 'Here are the domains and email addresses that have been verified and can be used as the From Email address.', 'wp-mail-smtp-pro' ); ?>
			</p>

		<?php else : ?>

			<p style="margin-bottom: 10px">
				<strong><?php esc_html_e( 'No registered domains or emails.', 'wp-mail-smtp-pro' ); ?></strong>
				<?php esc_html_e( 'You will not be able to send emails until you verify at least one domain or email address.', 'wp-mail-smtp-pro' ); ?>
			</p>

		<?php endif; ?>

		<p>
			<button type="button" title="<?php esc_attr_e( 'Add new SES identity', 'wp-mail-smtp-pro' ); ?>"
			   class="js-wp-mail-smtp-providers-<?php echo esc_attr( $this->get_slug() ); ?>-register-identity-modal-button wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-blueish">
				<?php esc_html_e( 'Add New', 'wp-mail-smtp-pro' ); ?>
			</button>
		</p>
		<?php

		return ob_get_clean();
	}

	/**
	 * The content of the "Add new identity" modal window.
	 *
	 * @since 2.4.0
	 */
	public static function prepare_add_new_identity_content() {

		$slug = self::SLUG;

		ob_start();
		?>
		<div id="wp-mail-smtp-providers-<?php echo esc_attr( $slug ); ?>-register-identity-holder">
			<div id="wp-mail-smtp-providers-<?php echo esc_attr( $slug ); ?>-register-identity">
				<?php wp_nonce_field( 'wp_mail_smtp_pro_amazonses_register_identity', 'wp_mail_smtp_pro_amazonses_register_identity' ); ?>
				<p id="wp-mail-smtp-providers-<?php echo esc_attr( $slug ); ?>-domain-desc">
					<?php esc_html_e( 'Enter the domain name to verify it on Amazon SES and generate the required DNS CNAME records.' , 'wp-mail-smtp-pro' ); ?>
				</p>
				<p id="wp-mail-smtp-providers-<?php echo esc_attr( $slug ); ?>-email-desc" style="display: none;">
					<?php esc_html_e( 'Enter a valid email address. A verification email will be sent to the email address you entered.' , 'wp-mail-smtp-pro' ); ?>
				</p>
				<p>
					<input id="wp-mail-smtp-providers-<?php echo esc_attr( $slug ); ?>-domain-type" class="js-wp-mail-smtp-providers-<?php echo esc_attr( $slug ); ?>-register-identity-radio-button wp-mail-smtp-not-form-input" type="radio" name="identity-type" value="domain" checked="checked">
					<label for="wp-mail-smtp-providers-<?php echo esc_attr( $slug ); ?>-domain-type"><?php esc_html_e( 'Verify Domain', 'wp-mail-smtp-pro' ); ?></label>
					<input id="wp-mail-smtp-providers-<?php echo esc_attr( $slug ); ?>-email-type" class="js-wp-mail-smtp-providers-<?php echo esc_attr( $slug ); ?>-register-identity-radio-button wp-mail-smtp-not-form-input" type="radio" name="identity-type" value="email">
					<label for="wp-mail-smtp-providers-<?php echo esc_attr( $slug ); ?>-email-type"><?php esc_html_e( 'Verify Email Address', 'wp-mail-smtp-pro' ); ?></label>
				</p>
				<p>
					<input type="text" id="wp-mail-smtp-providers-<?php echo esc_attr( $slug ); ?>-domain-input" class="wp-mail-smtp-not-form-input" placeholder="<?php esc_attr_e( 'Please enter a domain', 'wp-mail-smtp-pro' ); ?>">
					<input type="email" id="wp-mail-smtp-providers-<?php echo esc_attr( $slug ); ?>-email-input" class="wp-mail-smtp-not-form-input" style="display: none;" placeholder="<?php esc_attr_e( 'Please enter a valid email address', 'wp-mail-smtp-pro' ); ?>">
				</p>
				<button class="wp-mail-smtp-btn wp-mail-smtp-btn-md wp-mail-smtp-btn-orange wp-mail-smtp-providers-<?php echo esc_attr( $slug ); ?>-register-identity js-wp-mail-smtp-providers-<?php echo esc_attr( $slug ); ?>-register-identity">
					<?php esc_html_e( 'Verify', 'wp-mail-smtp-pro' ); ?>
				</button>
			</div>
		</div>
		<?php

		return ob_get_clean();
	}

	/**
	 * The title of the "Add new identity" modal window.
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */
	public static function prepare_add_new_identity_title() {

		return esc_html__( 'Verify SES identity', 'wp-mail-smtp-pro' );
	}

	/**
	 * Get an error text when the connection is not yet ready,
	 * basically, when we can't make requests to AmazonSES API.
	 *
	 * @since 1.5.0
	 *
	 * @return string
	 */
	protected function get_connection_not_ready_error_text() {
		return esc_html__( 'To access this section, please add an Access Key ID and Secret Access Key, select the Closest Region, then click the Save Settings button.', 'wp-mail-smtp-pro' );
	}

	/**
	 * Get the closest region based on Amazon SES region coordinates.
	 *
	 * @since 1.5.0
	 *
	 * @return string Region identifier.
	 */
	private function get_closest_region() {

		$site = Geo::get_location_by_ip( Geo::get_ip_by_domain( Geo::get_site_domain() ) );

		if ( empty( $site ) ) {
			return '';
		}

		$distance_to = array();

		foreach ( Auth::get_regions_coordinates() as $region => $coords ) {
			$distance_to[ $region ] = Geo::get_distance_between(
				$site['latitude'],
				$site['longitude'],
				$coords['lat'],
				$coords['lon']
			);
		}

		return array_search( min( $distance_to ), $distance_to, true );
	}

	/**
	 * Get domain DKIM DNS records.
	 *
	 * @since 3.3.0
	 *
	 * @param string              $domain      Domain.
	 * @param array               $dkim_tokens DKIM tokens.
	 * @param ConnectionInterface $connection  The Connection object.
	 *
	 * @return array
	 */
	public static function prepare_dkim_dns_records( $domain, $dkim_tokens, $connection = null ) {

		$result = [];

		$region = $connection->get_options()->get( self::SLUG, 'region' );

		if ( ! empty( $region ) ) {
			$region = Auth::prepare_region( $region );
		} else {
			$region = Auth::AWS_US_EAST_1;
		}

		foreach ( $dkim_tokens as $token ) {
			$region_part = '';

			// Include region to record value for particular regions.
			// Domains list in "DKIM Domains" section: https://docs.aws.amazon.com/general/latest/gr/ses.html.
			if ( in_array( $region, [ 'af-south-1', 'ap-northeast-3', 'eu-south-1' ], true ) ) {
				$region_part = '.' . $region;
			}

			$result[] = [
				'name'  => $token . '._domainkey.' . $domain,
				'value' => $token . '.dkim' . $region_part . '.amazonses.com',
			];
		}

		return $result;
	}

	/**
	 * Prepare the HTML output for domain DKIM DNS records notice.
	 *
	 * @since 3.3.0
	 *
	 * @param string              $domain      Domain.
	 * @param array               $dkim_tokens DKIM tokens.
	 * @param ConnectionInterface $connection  The Connection object.
	 *
	 * @return string
	 */
	public static function prepare_domain_dkim_records_notice( $domain, $dkim_tokens, $connection = null ) {

		ob_start();
		?>
		<div class="wp-mail-smtp-ses-dkim-records">

			<p class="wp-mail-smtp-ses-dkim-records__notice">
				<?php esc_html_e( 'Add the following CNAME records to your domain\'s DNS settings:', 'wp-mail-smtp-pro' ); ?>
			</p>

			<div class="wp-mail-smtp-ses-dkim-records__table">
				<div class="wp-mail-smtp-ses-dkim-records__row wp-mail-smtp-ses-dkim-records__row--heading">
					<div class="wp-mail-smtp-ses-dkim-records__col wp-mail-smtp-ses-dkim-records__col--heading">
						<?php esc_html_e( 'Name', 'wp-mail-smtp-pro' ); ?>
					</div>
					<div class="wp-mail-smtp-ses-dkim-records__col wp-mail-smtp-ses-dkim-records__col--heading">
						<?php esc_html_e( 'Value', 'wp-mail-smtp-pro' ); ?>
					</div>
				</div>

				<?php foreach ( self::prepare_dkim_dns_records( $domain, $dkim_tokens, $connection ) as $record ) : ?>
					<div class="wp-mail-smtp-ses-dkim-records__row wp-mail-smtp-ses-dkim-records__row--record">
						<div class="wp-mail-smtp-ses-dkim-records__col wp-mail-smtp-ses-dkim-records__col--record">
							<input type="text" class="js-wp-mail-smtp-ses-dkim-records-input" value="<?php echo esc_attr( $record['name'] ); ?>" readonly="readonly"/>
							<button class="js-wp-mail-smtp-ses-dkim-records-copy-btn">
								<span class="dashicons dashicons-admin-page"></span>
							</button>
						</div>
						<div class="wp-mail-smtp-ses-dkim-records__col wp-mail-smtp-ses-dkim-records__col--record">
							<input type="text" class="js-wp-mail-smtp-ses-dkim-records-input" value="<?php echo esc_attr( $record['value'] ); ?>" readonly="readonly"/>
							<button class="js-wp-mail-smtp-ses-dkim-records-copy-btn">
								<span class="dashicons dashicons-admin-page"></span>
							</button>
						</div>
					</div>
				<?php endforeach; ?>
			</div>

			<p class="wp-mail-smtp-ses-dkim-records__notice">
				<?php
				printf(
					wp_kses( /* translators: %s - URL to Amazon SES documentation. */
						__( 'For information on how to add CNAME DNS records, <br><a href="%s" target="_blank" rel="noopener noreferrer">please refer to the Amazon SES documentation</a>.', 'wp-mail-smtp-pro' ),
						[
							'br' => true,
							'a'  => [
								'href'   => true,
								'target' => true,
								'rel'    => true,
							],
						]
					),
					'https://docs.aws.amazon.com/ses/latest/dg/creating-identities.html#verify-domain-procedure'
				);
				?>
			</p>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * Prepare the HTML output for domain DNS TXT record notice.
	 *
	 * @deprecated 3.3.0 Switched to DKIM verification.
	 *
	 * @since 2.4.0
	 *
	 * @param string $name  The name of the TXT record.
	 * @param string $value The value of the TXT record.
	 *
	 * @return string
	 */
	public static function prepare_domain_txt_record_notice( $name = '', $value = '' ) {

		_deprecated_function( __METHOD__, '3.3.0', self::class . '::prepare_domain_dkim_records_notice' );

		$name   = empty( $name ) ? '%name%' : '_amazonses.' . $name;
		$value  = empty( $value ) ? '%value%' : $value;
		$notice = '';

		$notice .= '<p style="margin-bottom: 28px;">' . esc_html__( 'Please add this TXT record to your domain\'s DNS settings:', 'wp-mail-smtp-pro' ) . '</p>';
		$notice .= '<p class="wp-mail-smtp-providers-amazonses-txt-record js-wp-mail-smtp-providers-amazonses-txt-record">
			<span class="wp-mail-smtp-providers-amazonses-txt-record-label">' . esc_html__( 'Name:', 'wp-mail-smtp-pro' ) . '</span>&nbsp;
			<input type="text" value="' . esc_attr( $name ) . '" readonly="readonly"/>&nbsp;
			<button><span class="dashicons dashicons-admin-page"></span></button>
		</p>';
		$notice .= '<p class="wp-mail-smtp-providers-amazonses-txt-record js-wp-mail-smtp-providers-amazonses-txt-record" style="margin-bottom: 28px;">
			<span class="wp-mail-smtp-providers-amazonses-txt-record-label">' . esc_html__( 'Value:', 'wp-mail-smtp-pro' ) . '</span>&nbsp;
			<input type="text" value="' . esc_attr( $value ) . '" readonly="readonly"/>&nbsp;
			<button><span class="dashicons dashicons-admin-page"></span></button>
		</p>';
		$notice .= '<p>' .
			sprintf(
				wp_kses( /* translators: %s - URL to Amazon SES documentation. */
					__( 'For information on how to add TXT DNS records, please <br><a href="%s" target="_blank" rel="noopener noreferrer">refer to the Amazon SES documentation</a>.', 'wp-mail-smtp-pro' ),
					[
						'br' => true,
						'a'  => [
							'href'   => true,
							'target' => true,
							'rel'    => true,
						],
					]
				),
				'https://docs.aws.amazon.com/ses/latest/DeveloperGuide/dns-txt-records.html'
			) .
			'</p>';

		return $notice;
	}
}
