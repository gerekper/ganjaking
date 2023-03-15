<?php // phpcs:disable WordPress.Files.FileName.InvalidClassFileName
/**
 * OAuth2.0 Storage helper classfor WooCommerce Xero implementation.
 *
 * @package    WooCommerce Xero
 * @since      1.7.24
 */

use Automattic\WooCommerce\Xero\Vendor\League\OAuth2\Client\Provider\GenericProvider;

/**
 * OAuth2.0 Storage Class.
 *
 * @package    WooCommerce Xero
 * @since      1.7.24
 */
class WC_XR_OAuth20_Storage_Class {
	/**
	 * Xero client ID.
	 *
	 * @var String
	 */
	private static $client_id;

	/**
	 * Xero client Secret.
	 *
	 * @var String
	 */
	private static $client_secret;

	/**
	 * Generic OAuth2.0 provider.
	 *
	 * @var GenericProvider
	 */
	private static $provider;

	/**
	 * Constructor.
	 *
	 * @param string $client_id Xero OAugh2.0 clieng id.
	 * @param string $client_secret Xero OAugh2.0 clieng secret.
	 */
	public function __construct( $client_id, $client_secret ) {
		static::$client_id     = $client_id;
		static::$client_secret = $client_secret;
		static::$provider      = $this->create_provider();
	}

	/**
	 * Set received data.
	 *
	 * @param string $token Xero OAugh2.0 access token.
	 * @param int    $expires token valid timestamp.
	 * @param string $tenant_id Start clear.
	 * @param string $refresh_token token used to get new access token.
	 */
	public static function set_data( $token, $expires, $tenant_id, $refresh_token ) {
		$logger    = new WC_XR_Oauth20_Logger();
		$log_text  = 'XERO New connection made, storing token and other params\n';
		$log_text .= '     AccessToken = <<redacted>>\n';
		$log_text .= '     Expires time = ' . $expires . '\n';
		$log_text .= '     TenantId = ' . $tenant_id . '\n';
		$log_text .= '     RefreshToken = <<redacted>>\n';
		$logger->write( $log_text );

		$wc_xr_data_encryption = new WC_XR_Data_Encryption();

		$xero_oauth_options = [ // phpcs:ignore
			'token'         => $wc_xr_data_encryption->encrypt( $token ),
			'expires'       => $expires,
			'tenant_id'     => $tenant_id,
			'refresh_token' => $wc_xr_data_encryption->encrypt( $refresh_token ),
		];

		update_option( 'xero_oauth_options', $xero_oauth_options, false );
	}

	/**
	 * Get all storred OAuth2.0 parameters.
	 */
	public static function get_data() {
		$xero_oauth_options = get_option( 'xero_oauth_options' );

		if ( false === $xero_oauth_options ) {
			return null;
		}

		$wc_xr_data_encryption = new WC_XR_Data_Encryption();

		// Decrypt token only if value is non-empty. Default value of token is NULL.
		if ( $xero_oauth_options['token'] ) {
			$xero_oauth_options['token'] = $wc_xr_data_encryption->decrypt( $xero_oauth_options['token'] );
		}

		// Decrypt refresh_token only if value is non-empty. Default value of refresh_token is NULL.
		if ( $xero_oauth_options['refresh_token'] ) {
			$xero_oauth_options['refresh_token'] = $wc_xr_data_encryption->decrypt( $xero_oauth_options['refresh_token'] );
		}

		return $xero_oauth_options;
	}

	/**
	 * Get one of the OAuth2.0 parameters.
	 *
	 * @param string $value name of the parameter to fetch.
	 */
	private static function get_options_value( $value ) {
		$xero_oauth_options = static::get_data();
		if ( null !== $xero_oauth_options ) {
			return $xero_oauth_options[ $value ];
		}
		return null;
	}

	/**
	 * Clear data.
	 */
	public static function clear_data() {
		$logger = new WC_XR_Oauth20_Logger();
		$logger->write( 'XERO Clearing OAuth20 data.' );

		$xero_oauth_options = [ // phpcs:ignore
			'token'         => null,
			'expires'       => null,
			'tenant_id'     => null,
			'refresh_token' => null,
		];
		update_option( 'xero_oauth_options', $xero_oauth_options, false );
	}

	/**
	 * Token getter.
	 */
	public static function get_token() {
		$token_expired = static::get_has_expired();
		if ( $token_expired ) {
			static::refresh_access_token();
		}
		$token  = static::get_options_value( 'token' );
		$logger = new WC_XR_Oauth20_Logger();
		$logger->write( 'XERO Returning OAuth2.0 token=' . $token );
		return $token;
	}

	/**
	 * Refresh token getter.
	 */
	private static function get_refresh_token() {
		return static::get_options_value( 'refresh_token' );
	}

	/**
	 * Get token expiration timestamp.
	 */
	private static function get_expires() {
		return static::get_options_value( 'expires' );
	}

	/**
	 * Xero tenanatn id getter.
	 */
	public static function get_xero_tenant_id() {
		return static::get_options_value( 'tenant_id' );
	}

	/**
	 * Access token refresh procedure.
	 */
	private static function refresh_access_token() {
		$provider         = static::get_provider();
		$new_access_token = $provider->getAccessToken(
			'refresh_token',
			[ 'refresh_token' => static::get_refresh_token() ] // phpcs:ignore
		);

		$logger    = new WC_XR_Oauth20_Logger();
		$log_text  = 'XERO New access token from refresh token fetched\n';
		$log_text .= '     New AccessToken = ' . $new_access_token->getToken() . '\n';
		$log_text .= '     New Expires time = ' . $new_access_token->getExpires() . '\n';
		$log_text .= '     TenantId ( we use one we got previously )  = ' . static::get_xero_tenant_id() . '\n';
		$log_text .= '     New RefreshToken = ' . $new_access_token->getRefreshToken();
		$logger->write( $log_text );

		// Save my token, expiration and refresh token.
		static::set_data(
			$new_access_token->getToken(),
			$new_access_token->getExpires(),
			static::get_xero_tenant_id(),
			$new_access_token->getRefreshToken()
		);
	}

	/**
	 * Test expiration of the access token.
	 */
	private static function get_has_expired() {
		$expires = static::get_expires();
		$now     = time();
		if ( null !== $expires && ( $now < $expires ) ) {
			return false;
		}
		$logger = new WC_XR_Oauth20_Logger();
		$logger->write( 'XERO OAuth20 Token has expired timestamp=' . $expires . ' now=' . $now );
		return true;
	}

	/**
	 * Provider getter.
	 */
	public static function get_provider() {
		return static::$provider;
	}

	/**
	 * Create provider using League implementaion.
	 */
	private static function create_provider() {
		return new GenericProvider(
			[ // phpcs:ignore
				'clientId'                => static::$client_id,
				'clientSecret'            => static::$client_secret,
				'redirectUri'             => WC_XR_OAuth20::build_redirect_uri(),
				'urlAuthorize'            => 'https://login.xero.com/identity/connect/authorize',
				'urlAccessToken'          => 'https://identity.xero.com/connect/token',
				'urlResourceOwnerDetails' => 'https://api.xero.com/api.xro/2.0/Organisation',
			]
		);
	}
}
