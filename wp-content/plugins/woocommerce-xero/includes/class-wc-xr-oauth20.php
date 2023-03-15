<?php
/**
 * Main OAuth2.0 for WooCommerce Xero implementation file.
 *
 * @package    WooCommerce Xero
 * @since      1.7.24
 */

// Load helper class.
use Automattic\WooCommerce\Xero\Vendor\GuzzleHttp\Client;
use Automattic\WooCommerce\Xero\Vendor\XeroAPI\XeroPHP\Api\IdentityApi;
use Automattic\WooCommerce\Xero\Vendor\XeroAPI\XeroPHP\Configuration;
use Automattic\WooCommerce\Xero\Vendor\XeroAPI\XeroPHP\Api\AccountingApi;

/**
 * OAuth2.0 Class for WooCommerce Xero implementation file.
 *
 * @package    WooCommerce Xero
 * @since      1.7.24
 */
class WC_XR_OAuth20 {

	/**
	 * Instance for singleton pattern.
	 *
	 * @var WC_XR_OAuth20
	 */
	private static $instance;

	/**
	 * Storage class static.
	 *
	 * @var WC_XR_OAuth20_Storage_Class
	 */
	private static $storage;

	/**
	 * Const used to define success.
	 *
	 * String Successful OAuth connection marker.
	 */
	const OAUTH_SUCCESS = 'xero_oauth_success';

	/**
	 * Const used to define success.
	 *
	 * @param string $client_id Xero OAugh2.0 clieng id.
	 * @param string $client_secret Xero OAugh2.0 clieng secret.
	 * @return WC_XR_OAuth20 instance.
	 */
	public static function get_instance( $client_id = null, $client_secret = null ) {
		if ( null === static::$instance ) {
			static::$instance = new static( $client_id, $client_secret );
		}
		return static::$instance;
	}

	/**
	 * Simple constructor.
	 *
	 * @param string $client_id Xero OAugh2.0 clieng id.
	 * @param string $client_secret Xero OAugh2.0 clieng secret.
	 */
	public function __construct( $client_id, $client_secret ) {
		require_once __DIR__ . '/../lib/packages/autoload.php';
		require_once 'class-wc-xr-oauth20-storage.php';

		static::$storage = new WC_XR_OAuth20_Storage_Class( $client_id, $client_secret );
	}

	/**
	 * Generate redirect url for Xero OAuth2.0 redirect.
	 *
	 * @return string redirect url.
	 */
	public static function build_redirect_uri() {
		return admin_url( 'admin.php?page=woocommerce_xero_oauth' ); // phpcs:ignore
	}

	/**
	 * Used when we are authorized.
	 */
	public static function mark_successful_connection() {
		/*
		 * This option marks any succesfull connections.
		 * From now on only the new OAuth interface will be available.
		 * One needs to delete this option to see the OAuth 1.0 settings.
		 */
		update_option( self::OAUTH_SUCCESS , true ); // phpcs:ignore
	}

	/**
	 * Is system is ready for API operations using OAuth2.0.
	 */
	public static function can_use_oauth20() {
		return get_option( self::OAUTH_SUCCESS, false );
	}

	/**
	 * Reset status.
	 */
	public static function clear_connection_status() {
		static::$storage->clear_data();
	}

	/**
	 * Check if we have all requuired settings.
	 */
	public static function is_api_ready() {
		$data = static::$storage->get_data();
		if ( null === $data ) {
			return false;
		}
		foreach ( $data as $setting ) {
			if ( is_null( $setting ) ) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Check connection status.
	 */
	public static function get_connection_status() {
		if ( ! static::is_api_ready() ) {
			return array(
				'correctRequest' => false,
				'errorMessage'   => 'no_connection',
			);
		}

		try {
			$config = Configuration::getDefaultConfiguration()->setAccessToken( (string) static::$storage->get_token() );
			$config->setHost( 'https://api.xero.com/api.xro/2.0' );

			$api_instance = new AccountingApi( new Client(), $config );

			// Get Organisation details.
			$xero_tenant_id = (string) static::$storage->get_xero_tenant_id();
			$api_response   = $api_instance->getOrganisations( $xero_tenant_id );

			return array(
				'correctRequest'   => true,
				'connectedCompany' => $api_response->getOrganisations()[0]->getName(),
			);
		} catch ( Exception $e ) {
			return array(
				'correctRequest' => false,
				'errorMessage'   => $e->getMessage(),
			);
		}
	}

	/**
	 * Get provider.
	 */
	public static function get_provider() {
		return static::$storage->get_provider();
	}

	/**
	 * Return authorization url.
	 *
	 * @param array $options authorization url type.
	 */
	public static function get_authorization_url( $options ) {
		return static::get_provider()->getAuthorizationUrl( $options );
	}

	/**
	 * Fetch access token
	 *
	 * @param string $authorization_code authorization url type.
	 */
	public static function get_access_token_using_authorization_code( $authorization_code ) {
		$provider = static::get_provider();
		// Try to get an access token using the authorization code grant.
		$access_token = $provider->getAccessToken(
			'authorization_code',
			[ 'code' => $_GET['code'] ] // phpcs:ignore
		);

		$config = Configuration::getDefaultConfiguration()->setAccessToken( (string) $access_token->getToken() );

		$config->setHost( 'https://api.xero.com' );
		$identity_instance = new IdentityApi( new Client(), $config );

		$result = $identity_instance->getConnections();

		// Save data.
		static::$storage->set_data(
			$access_token->getToken(),
			$access_token->getExpires(),
			$result[0]->getTenantId(),
			$access_token->getRefreshToken()
		);
	}

	/**
	 * State getter.
	 */
	public function get_state() {
		return static::$storage->get_provider()->getState();
	}

	/**
	 * Access token getter.
	 */
	public static function get_access_token() {
		return static::$storage->get_token();
	}

	/**
	 * Tennant id getter.
	 */
	public static function get_xero_tenant_id() {
		return static::$storage->get_xero_tenant_id();
	}

	/**
	 * Prevent cloning.
	 */
	public function __clone() {
		wc_doing_it_wrong( __FUNCTION__, __( 'Cloning instances of this class is forbidden.', 'woocommerce' ) );
	}

	/**
	 * Prevent unserialize.
	 */
	public function __wakeup() {
		wc_doing_it_wrong( __FUNCTION__, __( 'Unserializing instances of this class is forbidden.', 'woocommerce' ) );
	}
}
