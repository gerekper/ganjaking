<?php

namespace WPMailSMTP\Pro\Providers\AmazonSES;

use WPMailSMTP\ConnectionInterface;
use WPMailSMTP\Vendor\Aws\Ses\SesClient;
use WPMailSMTP\Vendor\Aws\SesV2\SesV2Client;
use WPMailSMTP\Debug;
use WPMailSMTP\Providers\AuthAbstract;

/**
 * Class Auth.
 *
 * @since 1.5.0
 */
class Auth extends AuthAbstract {

	/**
	 * The AWS SES regions.
	 *
	 * @link http://docs.aws.amazon.com/ses/latest/DeveloperGuide/regions.html
	 */
	// phpcs:disable WPForms.Comments.Since.MissingPhpDoc
	const AWS_US_EAST_1      = 'us-east-1';
	const AWS_US_EAST_2      = 'us-east-2';
	const AWS_US_WEST_1      = 'us-west-1';
	const AWS_US_WEST_2      = 'us-west-2';
	const AWS_EU_WEST_1      = 'eu-west-1';
	const AWS_EU_WEST_2      = 'eu-west-2';
	const AWS_EU_WEST_3      = 'eu-west-3';
	const AWS_EU_CENTRAL_1   = 'eu-central-1';
	const AWS_EU_NORTH_1     = 'eu-north-1';
	const AWS_EU_SOUTH_1     = 'eu-south-1';
	const AWS_AP_SOUTH_1     = 'ap-south-1';
	const AWS_AP_NORTHEAST_1 = 'ap-northeast-1';
	const AWS_AP_NORTHEAST_2 = 'ap-northeast-2';
	const AWS_AP_NORTHEAST_3 = 'ap-northeast-3';
	const AWS_AP_SOUTHEAST_1 = 'ap-southeast-1';
	const AWS_AP_SOUTHEAST_2 = 'ap-southeast-2';
	const AWS_AF_SOUTH_1     = 'af-south-1';
	const AWS_CA_CENTRAL_1   = 'ca-central-1';
	const AWS_ME_SOUTH_1     = 'me-south-1';
	const AWS_SA_EAST_1      = 'sa-east-1';
	const AWS_US_GOV_WEST_1  = 'us-gov-west-1';
	// phpcs:enable

	/**
	 * Array of domains and their data.
	 * keys: domain name
	 * values: array with status and verification values.
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
	 *
	 * @param ConnectionInterface $connection The Connection object.
	 */
	public function __construct( $connection = null ) {

		parent::__construct( $connection );

		if ( $this->mailer_slug !== Options::SLUG ) {
			return;
		}

		$this->options = $this->connection_options->get_group( $this->mailer_slug );

		$this->include_vendor_lib();
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
	 * @since 3.3.0 Added Osaka.
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
			self::AWS_AP_NORTHEAST_3 => esc_html__( 'Asia Pacific (Osaka)', 'wp-mail-smtp-pro' ),
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
	 * @since 3.3.0 Added Osaka.
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
			self::AWS_AP_NORTHEAST_3 => [
				'lat' => 34.693737,
				'lon' => 135.502167,
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
	 * @since 3.5.0 Added `SesV2Client` client support.
	 *
	 * @param string $version Client version.
	 *
	 * @return SesClient|SesV2Client
	 */
	public function get_client( $version = 'v1' ) {

		// Doesn't load client twice + gives ability to overwrite.
		if ( ! empty( $this->client[ $version ] ) ) {
			return $this->client[ $version ];
		}

		/**
		 * Filters AWS SES client arguments.
		 *
		 * @since 2.4.0
		 *
		 * @param array  $args    AWS SES client arguments.
		 * @param string $version AWS SES client version.
		 */
		$args = apply_filters(
			'wp_mail_smtp_providers_auth_aws_get_client_args',
			[
				'credentials' => [
					'key'    => $this->options['client_id'],
					'secret' => $this->options['client_secret'],
				],
				'region'      => empty( $this->options['region'] ) ? self::AWS_US_EAST_1 : self::prepare_region( $this->options['region'] ),
				'version'     => $version === 'v1' ? '2010-12-01' : '2019-09-27',
			],
			$version
		);

		$this->client[ $version ] = $version === 'v1' ? new SesClient( $args ) : new SesV2Client( $args );

		return $this->client[ $version ];
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
				if ( $attributes['IdentityType'] === 'DOMAIN' ) {
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
	 *      value: - IdentityType - DOMAIN or EMAIL_ADDRESS
	 *             - VerificationStatus
	 *             - VerificationToken (the TXT record value for the DNS setup) - only present for domains!
	 *             - DkimEnabled
	 *             - DkimVerificationStatus
	 *             - DkimTokens (the tokens for generate CNAME record values for the DKIM DNS setup)
	 *
	 * @since 2.4.0
	 * @since 3.3.0 Added DKIM verification attributes.
	 *
	 * @return array
	 */
	private function fetch_identities() {

		try {
			$identities_response = $this->get_client( 'v2' )->listEmailIdentities();
			$identities          = $identities_response->get( 'EmailIdentities' );
			$identities_list     = array_column( $identities, 'IdentityName' );

			$identities = array_merge_recursive(
				array_combine( $identities_list, $identities ),
				$this->get_verification_attributes( $identities_list ),
				$this->get_dkim_verification_attributes( $identities_list )
			);

		} catch ( \Exception $e ) {
			Debug::set( $e->getMessage() );

			return [];
		}

		if ( ! empty( $identities ) ) {
			Debug::clear();
		}

		return $identities;
	}

	/**
	 * Send a request to get the identities verification attributes.
	 *
	 * @since 3.3.0
	 *
	 * @param array|string $identities Identities array.
	 *
	 * @return array
	 */
	private function get_verification_attributes( $identities ) {

		$result            = [];
		$identities_chunks = array_chunk( (array) $identities, 100 );

		foreach ( $identities_chunks as $identities_chunk ) {
			$response = $this->get_client()->getIdentityVerificationAttributes( [ 'Identities' => $identities_chunk ] );

			if ( ! empty( $response->get( 'VerificationAttributes' ) ) ) {
				$result = array_merge( $result, $response->get( 'VerificationAttributes' ) );
			}
		}

		return $result;
	}

	/**
	 * Send a request to get the identities DKIM verification attributes.
	 *
	 * @since 3.3.0
	 *
	 * @param array|string $identities Identities array.
	 *
	 * @return array
	 */
	private function get_dkim_verification_attributes( $identities ) {

		$result            = [];
		$identities_chunks = array_chunk( (array) $identities, 100 );

		foreach ( $identities_chunks as $identities_chunk ) {
			$response = $this->get_client()->getIdentityDkimAttributes( [ 'Identities' => $identities_chunk ] );

			if ( ! empty( $response->get( 'DkimAttributes' ) ) ) {
				$result = array_merge( $result, $response->get( 'DkimAttributes' ) );
			}
		}

		return $result;
	}

	/**
	 * Send a request to get the identities DKIM verification tokens.
	 *
	 * @since 3.3.0
	 *
	 * @param string $domain Domain name.
	 *
	 * @return array|\WP_Error
	 */
	public function get_dkim_tokens( $domain ) {

		try {
			$attributes = $this->get_dkim_verification_attributes( $domain );
		} catch ( \Exception $e ) {
			return new \WP_Error( $e->getCode(), $e->getMessage() );
		}

		if ( empty( $attributes ) || empty( current( $attributes )['DkimTokens'] ) ) {
			return new \WP_Error(
				'disabled_dkim',
				sprintf(
					esc_html__( /* translators: %s - domain name. */
						'It looks like DKIM is not enabled for this domain. Please visit AWS SES console and enable DKIM for %s.',
						'wp-mail-smtp-pro'
					),
					esc_html( $domain )
				)
			);
		}

		return current( $attributes )['DkimTokens'];
	}

	/**
	 * Send a request to verify a domain DKIM.
	 *
	 * @since 3.3.0
	 *
	 * @param string $domain Domain to verify.
	 *
	 * @return bool|array False on error or the array of the tokens for generate CNAME record values for the DKIM DNS setup.
	 */
	public function do_verify_domain_dkim( $domain ) {

		try {
			$response = $this->get_client()->verifyDomainDkim( [ 'Domain' => $domain ] );
		} catch ( \Exception $e ) {
			Debug::set( $e->getMessage() );

			return false;
		}

		if (
			is_object( $response ) &&
			! empty( $response->get( 'DkimTokens' ) )
		) {
			Debug::clear();

			return $response->get( 'DkimTokens' );
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

	/**
	 * Send a request to delete a verified email address.
	 *
	 * Not used anymore.
	 *
	 * @deprecated 2.4.0
	 *
	 * @since 1.5.0
	 * @since 2.4.0 Switch to official AWS SDK.
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
	 * Send a request to get the list of verified emails.
	 *
	 * Not used anymore.
	 *
	 * @deprecated 2.4.0
	 *
	 * @since 1.5.0
	 * @since 2.4.0 Switch to official AWS SDK.
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
	 * @deprecated 3.3.0 Switched to DKIM verification.
	 *
	 * @since 2.4.0
	 *
	 * @param string $domain Domain to verify.
	 *
	 * @return bool|string False on error or the string TXT record value for the DNS setting.
	 */
	public function do_verify_domain( $domain ) {

		_deprecated_function( __METHOD__, '3.3.0', self::class . '::do_verify_domain_dkim' );

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
}
