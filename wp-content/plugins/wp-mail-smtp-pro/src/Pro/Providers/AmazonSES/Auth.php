<?php

namespace WPMailSMTP\Pro\Providers\AmazonSES;

use WPMailSMTP\Vendor\Aws\Ses\SesClient;
use WPMailSMTP\Debug;
use WPMailSMTP\Options as PluginOptions;
use WPMailSMTP\Providers\AuthAbstract;

/**
 * Class Auth
 *
 * @since 1.5.0
 */
class Auth extends AuthAbstract {

	/**
	 * The AWS SES regions.
	 *
	 * @link http://docs.aws.amazon.com/ses/latest/DeveloperGuide/regions.html
	 */
	const AWS_US_EAST_1      = 'us-east-1'; // phpcs:ignore
	const AWS_US_EAST_2      = 'us-east-2'; // phpcs:ignore
	const AWS_US_WEST_1      = 'us-west-1'; // phpcs:ignore
	const AWS_US_WEST_2      = 'us-west-2'; // phpcs:ignore
	const AWS_EU_WEST_1      = 'eu-west-1'; // phpcs:ignore
	const AWS_EU_WEST_2      = 'eu-west-2'; // phpcs:ignore
	const AWS_EU_WEST_3      = 'eu-west-3'; // phpcs:ignore
	const AWS_EU_CENTRAL_1   = 'eu-central-1'; // phpcs:ignore
	const AWS_EU_NORTH_1     = 'eu-north-1'; // phpcs:ignore
	const AWS_EU_SOUTH_1     = 'eu-south-1'; // phpcs:ignore
	const AWS_AP_SOUTH_1     = 'ap-south-1'; // phpcs:ignore
	const AWS_AP_NORTHEAST_1 = 'ap-northeast-1'; // phpcs:ignore
	const AWS_AP_NORTHEAST_2 = 'ap-northeast-2'; // phpcs:ignore
	const AWS_AP_SOUTHEAST_1 = 'ap-southeast-1'; // phpcs:ignore
	const AWS_AP_SOUTHEAST_2 = 'ap-southeast-2'; // phpcs:ignore
	const AWS_AF_SOUTH_1     = 'af-south-1'; // phpcs:ignore
	const AWS_CA_CENTRAL_1   = 'ca-central-1'; // phpcs:ignore
	const AWS_ME_SOUTH_1     = 'me-south-1'; // phpcs:ignore
	const AWS_SA_EAST_1      = 'sa-east-1'; // phpcs:ignore
	const AWS_US_GOV_WEST_1  = 'us-gov-west-1'; // phpcs:ignore

	/**
	 * Array of domains and their data.
	 * keys: domain name
	 * values: array with status and TXT DNS value.
	 *
	 * @since 2.4.0
	 *
	 * @var array
	 */
	protected $registered_domains;

	/**
	 * Array of email addresses and their data.
	 * keys: email address
	 * value: array with their status
	 *
	 * @since 2.4.0
	 *
	 * @var array
	 */
	protected $registered_email_addresses;

	/**
	 * Auth constructor.
	 *
	 * @since 1.5.0
	 */
	public function __construct() {

		$options           = new PluginOptions();
		$this->mailer_slug = $options->get( 'mail', 'mailer' );

		if ( $this->mailer_slug !== Options::SLUG ) {
			return;
		}

		$this->options = $options->get_group( $this->mailer_slug );

		$this->include_vendor_lib();
		$this->get_client();
	}

	/**
	 * Get AWS SES registered domains.
	 *
	 * @since 2.4.0
	 *
	 * @return array
	 */
	public function get_registered_domains() {

		if ( ! isset( $this->registered_domains ) ) {
			$this->populate_identities();
		}

		return $this->registered_domains;
	}

	/**
	 * Get AWS SES registered email addresses.
	 *
	 * @since 2.4.0
	 *
	 * @return array
	 */
	public function get_registered_emails() {

		if ( ! isset( $this->registered_email_addresses ) ) {
			$this->populate_identities();
		}

		return $this->registered_email_addresses;
	}

	/**
	 * Get the list of supported AWS regions.
	 *
	 * @since 1.5.0
	 * @since 2.4.0 Added Ohio, Canada, Mumbai, Tokyo, Seoul, Singapore, Sydney, London, Frankfurt and São Paulo.
	 * @since 3.0.0 Added Cape Town, Milan, Bahrain and GovCloud (US-West).
	 *
	 * @return array
	 */
	public static function get_regions_names() {

		return [
			self::AWS_US_EAST_1      => esc_html__( 'US East (N. Virginia)', 'wp-mail-smtp-pro' ),
			self::AWS_US_EAST_2      => esc_html__( 'US East (Ohio)', 'wp-mail-smtp-pro' ),
			self::AWS_US_WEST_1      => esc_html__( 'US West (N. California)', 'wp-mail-smtp-pro' ),
			self::AWS_US_WEST_2      => esc_html__( 'US West (Oregon)', 'wp-mail-smtp-pro' ),
			self::AWS_EU_WEST_1      => esc_html__( 'EU (Ireland)', 'wp-mail-smtp-pro' ),
			self::AWS_EU_WEST_2      => esc_html__( 'EU (London)', 'wp-mail-smtp-pro' ),
			self::AWS_EU_WEST_3      => esc_html__( 'EU (Paris)', 'wp-mail-smtp-pro' ),
			self::AWS_EU_CENTRAL_1   => esc_html__( 'EU (Frankfurt)', 'wp-mail-smtp-pro' ),
			self::AWS_EU_NORTH_1     => esc_html__( 'EU (Stockholm)', 'wp-mail-smtp-pro' ),
			self::AWS_EU_SOUTH_1     => esc_html__( 'EU (Milan)', 'wp-mail-smtp-pro' ),
			self::AWS_AP_SOUTH_1     => esc_html__( 'Asia Pacific (Mumbai)', 'wp-mail-smtp-pro' ),
			self::AWS_AP_NORTHEAST_1 => esc_html__( 'Asia Pacific (Tokyo)', 'wp-mail-smtp-pro' ),
			self::AWS_AP_NORTHEAST_2 => esc_html__( 'Asia Pacific (Seoul)', 'wp-mail-smtp-pro' ),
			self::AWS_AP_SOUTHEAST_1 => esc_html__( 'Asia Pacific (Singapore)', 'wp-mail-smtp-pro' ),
			self::AWS_AP_SOUTHEAST_2 => esc_html__( 'Asia Pacific (Sydney)', 'wp-mail-smtp-pro' ),
			self::AWS_AF_SOUTH_1     => esc_html__( 'Africa (Cape Town)', 'wp-mail-smtp-pro' ),
			self::AWS_CA_CENTRAL_1   => esc_html__( 'Canada (Central)', 'wp-mail-smtp-pro' ),
			self::AWS_ME_SOUTH_1     => esc_html__( 'Middle East (Bahrain)', 'wp-mail-smtp-pro' ),
			self::AWS_SA_EAST_1      => esc_html__( 'South America (São Paulo)', 'wp-mail-smtp-pro' ),
			self::AWS_US_GOV_WEST_1  => esc_html__( 'GovCloud (US-West)', 'wp-mail-smtp-pro' ),
		];
	}

	/**
	 * Get the list of supported AWS regions coordinates.
	 *
	 * @since 1.5.0
	 * @since 2.4.0 Added Ohio, Canada, London, São Paulo, Tokyo, Seoul and Singapore.
	 * @since 3.0.0 Added Cape Town, Milan and Bahrain.
	 *
	 * @return array
	 */
	public static function get_regions_coordinates() {

		return [
			self::AWS_US_EAST_1      => [
				'lat' => 38.837392,
				'lon' => - 77.447313,
			],
			self::AWS_US_EAST_2      => [
				'lat' => 40.417286,
				'lon' => - 82.907120,
			],
			self::AWS_US_WEST_1      => [
				'lat' => 36.778259,
				'lon' => - 119.417931,
			],
			self::AWS_US_WEST_2      => [
				'lat' => 45.3573,
				'lon' => - 122.6068,
			],
			self::AWS_EU_WEST_1      => [
				'lat' => 53.305494,
				'lon' => - 7.737649,
			],
			self::AWS_EU_WEST_2      => [
				'lat' => 51.5074,
				'lon' => 0.1278,
			],
			self::AWS_EU_WEST_3      => [
				'lat' => 48.864716,
				'lon' => 2.349014,
			],
			self::AWS_EU_CENTRAL_1   => [
				'lat' => 50.1109,
				'lon' => 8.6821,
			],
			self::AWS_EU_NORTH_1     => [
				'lat' => 59.334591,
				'lon' => 18.063240,
			],
			self::AWS_EU_SOUTH_1     => [
				'lat' => 45.4642,
				'lon' => 9.1900,
			],
			self::AWS_AP_SOUTH_1     => [
				'lat' => 19.0760,
				'lon' => 72.8777,
			],
			self::AWS_AP_NORTHEAST_1 => [
				'lat' => 35.689487,
				'lon' => 139.691711,
			],
			self::AWS_AP_NORTHEAST_2 => [
				'lat' => 37.566536,
				'lon' => 126.977966,
			],
			self::AWS_AP_SOUTHEAST_1 => [
				'lat' => 1.352083,
				'lon' => 103.819839,
			],
			self::AWS_AP_SOUTHEAST_2 => [
				'lat' => 33.8688,
				'lon' => 151.2093,
			],
			self::AWS_AF_SOUTH_1     => [
				'lat' => - 33.9249,
				'lon' => 18.4241,
			],
			self::AWS_CA_CENTRAL_1   => [
				'lat' => 51.2538,
				'lon' => 85.3232,
			],
			self::AWS_ME_SOUTH_1     => [
				'lat' => 26.0667,
				'lon' => 50.5577,
			],
			self::AWS_SA_EAST_1      => [
				'lat' => 23.5505,
				'lon' => 46.6333,
			],
		];
	}

	/**
	 * Init and get AWS SES client to work with.
	 *
	 * @since 1.5.0
	 * @since 2.4.0 Switch to official AWS SDK.
	 *
	 * @return SesClient
	 */
	public function get_client() {

		// Doesn't load client twice + gives ability to overwrite.
		if ( ! empty( $this->client ) ) {
			return $this->client;
		}

		$args = apply_filters(
			'wp_mail_smtp_providers_auth_aws_get_client_args',
			[
				'credentials' => [
					'key'    => $this->options['client_id'],
					'secret' => $this->options['client_secret'],
				],
				'region'      => empty( $this->options['region'] ) ? self::AWS_US_EAST_1 : self::prepare_region( $this->options['region'] ),
				'version'     => '2010-12-01',
			]
		);

		$this->client = new SesClient( $args );

		return $this->client;
	}

	/**
	 * Prepare the selected region.
	 * The old format for saving regions was: 'email.us-east-1.amazonaws.com'.
	 * The new one is: 'us-east-1'.
	 *
	 * So, if the old one is matched, filter it down to the new format.
	 *
	 * @since 2.4.0
	 *
	 * @param string $region The AWS region in various formats.
	 *
	 * @return string
	 */
	public static function prepare_region( $region ) {

		if ( preg_match( '/email\.(.*)\.amazonaws\.com/', $region, $match ) ) {
			$region = ! empty( $match[1] ) ? $match[1] : $region;
		}

		return $region;
	}

	/**
	 * Populate the registered domains and email addresses by fetching the data from AWS API
	 * and filter the results.
	 *
	 * The domains have the `VerificationToken` attribute, while the emails don't.
	 *
	 * @since 2.4.0
	 */
	private function populate_identities() {

		$identities = $this->fetch_identities();
		$domains    = [];
		$emails     = [];

		if ( ! empty( $identities ) ) {
			foreach ( $identities as $identity => $attributes ) {
				if ( ! empty( $attributes['VerificationToken'] ) ) {
					$domains[ $identity ] = $attributes;
				} else {
					$emails[ $identity ] = $attributes;
				}
			}
		}

		$this->registered_domains         = $domains;
		$this->registered_email_addresses = $emails;
	}

	/**
	 * Send a request to get the list of all AWS SES registered identities (emails and domains).
	 *
	 * Array example:
	 *      key: domain name of email address
	 *      value: - VerificationStatus
	 *             - VerificationToken (the TXT record value for the DNS setup ) - only present for domains!
	 *
	 * @since 2.4.0
	 *
	 * @return array
	 */
	private function fetch_identities() {

		$identities = [];

		try {
			$identities_response = $this->get_client()->listIdentities();
			$response            = $this->get_client()->getIdentityVerificationAttributes( [ 'Identities' => $identities_response->get( 'Identities' ) ] );
		} catch ( \Exception $e ) {
			Debug::set( $e->getMessage() );

			return [];
		}

		if ( ! empty( $response->get( 'VerificationAttributes' ) ) ) {
			$identities = $response->get( 'VerificationAttributes' );

			Debug::clear();
		}

		return $identities;
	}

	/**
	 * Send a request to get the list of verified emails.
	 *
	 * Not used anymore.
	 *
	 * @since 1.5.0
	 * @since 2.4.0 Switch to official AWS SDK.
	 *
	 * @deprecated 2.4.0
	 *
	 * @return array
	 */
	public function get_verified_emails() {

		_deprecated_function(
			__METHOD__,
			'2.4.0',
			'WPMailSMTP\Pro\Providers\AmazonSES\Auth::get_registered_emails()'
		);

		return array_keys( $this->get_registered_emails() );
	}

	/**
	 * Send a request to verify a domain.
	 *
	 * @since 2.4.0
	 *
	 * @param string $domain Domain to verify.
	 *
	 * @return bool|string False on error or the string TXT record value for the DNS setting.
	 */
	public function do_verify_domain( $domain ) {

		try {
			$response = $this->get_client()->verifyDomainIdentity( [ 'Domain' => $domain ] );
		} catch ( \Exception $e ) {
			Debug::set( $e->getMessage() );

			return false;
		}

		if (
			is_object( $response ) &&
			! empty( $response->get( 'VerificationToken' ) )
		) {
			Debug::clear();

			return $response->get( 'VerificationToken' );
		}

		return false;
	}

	/**
	 * Send a request to verify an email address.
	 *
	 * @since 1.5.0
	 * @since 2.4.0 Switch to official AWS SDK.
	 *
	 * @param string $email Email address to verify.
	 *
	 * @return bool
	 */
	public function do_verify_email( $email ) {

		try {
			$response = $this->get_client()->verifyEmailAddress( [ 'EmailAddress' => $email ] );
		} catch ( \Exception $e ) {
			Debug::set( $e->getMessage() );

			return false;
		}

		if (
			is_object( $response ) &&
			! empty( $response )
		) {
			Debug::clear();

			return true;
		}

		return false;
	}

	/**
	 * Send a request to delete a verified email address or domain.
	 *
	 * @since 2.4.0
	 *
	 * @param string $identity Email address or domain to remove.
	 *
	 * @return bool
	 */
	public function do_delete_identity( $identity ) {

		try {
			$response = $this->get_client()->deleteIdentity( [ 'Identity' => $identity ] );
		} catch ( \Exception $e ) {
			Debug::set( $e->getMessage() );

			return false;
		}

		if (
			is_object( $response ) &&
			! empty( $response )
		) {
			Debug::clear();

			return true;
		}

		return false;
	}

	/**
	 * Send a request to delete a verified email address.
	 *
	 * Not used anymore.
	 *
	 * @since 1.5.0
	 * @since 2.4.0 Switch to official AWS SDK.
	 *
	 * @deprecated 2.4.0
	 *
	 * @param string $email Email address to remove.
	 *
	 * @return bool
	 */
	public function do_delete_verified_email( $email ) {

		_deprecated_function(
			__METHOD__,
			'2.4.0',
			'WPMailSMTP\Pro\Providers\AmazonSES\Auth::do_delete_identity()'
		);

		return $this->do_delete_identity( $email );
	}

	/**
	 * AmazonSES requires a selected region AND both keys.
	 *
	 * @since 1.5.0
	 *
	 * @return bool
	 */
	public function is_connection_ready() {

		return $this->is_clients_saved() && ! empty( $this->options['region'] );
	}

	/**
	 * Whether we should perform an extra auth step.
	 *
	 * @since 1.5.0
	 *
	 * @return bool
	 */
	public function is_auth_required() {

		return false;
	}
}
