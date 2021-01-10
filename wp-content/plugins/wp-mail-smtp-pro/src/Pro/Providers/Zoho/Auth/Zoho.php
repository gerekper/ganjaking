<?php

namespace WPMailSMTP\Pro\Providers\Zoho\Auth;

use WPMailSMTP\Vendor\League\OAuth2\Client\Provider\AbstractProvider;
use WPMailSMTP\Vendor\League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use WPMailSMTP\Vendor\League\OAuth2\Client\Token\AccessToken;
use WPMailSMTP\Vendor\Psr\Http\Message\ResponseInterface;

/**
 * Class Zoho - OAuth2 client provider for Zoho Mail.
 *
 * @since 2.3.0
 *
 * @link https://github.com/shahariaazam/zoho-oauth2/blob/master/src/Provider/Zoho.php
 */
class Zoho extends AbstractProvider {

	/**
	 * The root endpoint of the zoho authentication URL.
	 *
	 * The domain extension will be added later since the Zoho Mail API is divided by regions/domains.
	 *
	 * @var string
	 */
	protected $endpoint = 'https://accounts.zoho.';

	/**
	 * The access type attribute for the Zoho OAuth2 request.
	 *
	 * @since 2.3.0
	 *
	 * @var string
	 */
	protected $access_type = 'offline';

	/**
	 * The Zoho API domain for particular Zoho user account.
	 *
	 * Currently available option: 'com', 'eu', 'in', 'com.cn', 'com.au'.
	 *
	 * @var string
	 */
	protected $domain;

	/**
	 * The default Zoho Mail scopes.
	 *
	 * @since 2.3.0
	 *
	 * @var array
	 */
	public $default_scopes = [
		'ZohoMail.messages.CREATE',
		'ZohoMail.accounts.READ',
	];

	/**
	 * Zoho constructor.
	 *
	 * @since 2.3.0
	 *
	 * @param array $options       The provider options.
	 * @param array $collaborators The array of collaborators that may be used to
	 *                             override this provider's default behavior. Collaborators include
	 *                             `grantFactory`, `requestFactory`, and `httpClient`.
	 *
	 * @throws \InvalidArgumentException If the required options are not present.
	 */
	public function __construct( array $options = [], array $collaborators = [] ) {

		parent::__construct( $options, $collaborators );

		foreach ( [ 'domain', 'clientId', 'clientSecret', 'redirectUri' ] as $key ) {
			if ( ! isset( $options[ $key ] ) ) {
				throw new \InvalidArgumentException( $key . ' is missing' );
			}
		}
	}

	/**
	 * Returns the base URL for authorizing a client.
	 *
	 * Eg. https://accounts.zoho.eu/oauth/v2/auth
	 *
	 * @since 2.3.0
	 *
	 * @return string
	 */
	public function getBaseAuthorizationUrl() {

		return $this->endpoint . $this->domain . '/oauth/v2/auth';
	}

	/**
	 * Returns the base URL for requesting an access token.
	 *
	 * Eg. https://accounts.zoho.eu/oauth/v2/token
	 *
	 * @since 2.3.0
	 *
	 * @param array $params The token query parameters.
	 *
	 * @return string
	 */
	public function getBaseAccessTokenUrl( array $params ) {

		return $this->endpoint . $this->domain . '/oauth/v2/token';
	}

	/**
	 * Returns the URL for requesting the resource owner's details.
	 *
	 * @since 2.3.0
	 *
	 * @param AccessToken $token The access token.
	 *
	 * @return string
	 */
	public function getResourceOwnerDetailsUrl( AccessToken $token ) {

		return 'https://mail.zoho.' . $this->domain . '/api/accounts';
	}

	/**
	 * Get the Authorization header.
	 *
	 * @since 2.3.0
	 *
	 * @param mixed|null $token The access token.
	 *
	 * @return array
	 */
	protected function getAuthorizationHeaders( $token = null ) {

		return [
			'Authorization' => 'Zoho-oauthtoken ' . $token,
		];
	}

	/**
	 * Get the authorization parameters.
	 *
	 * @since 2.3.0
	 *
	 * @param array $options The client provider options.
	 *
	 * @return array
	 */
	protected function getAuthorizationParameters( array $options ) {

		if ( empty( $options['state'] ) ) {
			$options['state'] = $this->state;
		}

		if ( empty( $options['scope'] ) ) {
			$options['scope'] = $this->getDefaultScopes();
		}

		$options += [
			'response_type' => 'code',
			'prompt'        => 'consent',
		];

		if ( is_array( $options['scope'] ) ) {
			$separator        = $this->getScopeSeparator();
			$options['scope'] = implode( $separator, $options['scope'] );
		}

		// Store the state as it may need to be accessed later on.
		$this->state = $options['state'];

		// Business code layer might set a different redirect_uri parameter depending on the context, leave it as-is.
		if ( ! isset( $options['redirect_uri'] ) ) {
			$options['redirect_uri'] = $this->redirectUri; // phpcs:ignore
		}

		$options['client_id'] = $this->clientId; // phpcs:ignore

		if (
			isset( $this->access_type ) &&
			in_array( $this->access_type, [ 'offline', 'online' ], true )
		) {
			$options['access_type'] = $this->access_type;
		}

		return $options;
	}

	/**
	 * Returns the default scopes used by this provider.
	 *
	 * This should only be the scopes that are required to request the details
	 * of the resource owner, rather than all the available scopes.
	 *
	 * @since 2.3.0
	 *
	 * @return array
	 */
	protected function getDefaultScopes() {

		return $this->default_scopes;
	}

	/**
	 * Checking response to see if provider returned any error.
	 *
	 * @since 2.3.0
	 *
	 * @param ResponseInterface $response The response object.
	 * @param array|string      $data     Parsed response data.
	 *
	 * @return void
	 *
	 * @throws IdentityProviderException If the response has an error.
	 */
	protected function checkResponse( ResponseInterface $response, $data ) {

		$responseData = $this->parseResponse( $response ); // phpcs:ignore
		if ( array_key_exists( 'error', $responseData ) ) { // phpcs:ignore
			throw new IdentityProviderException( $responseData['error'], $response->getStatusCode(), $response ); // phpcs:ignore
		}
	}

	/**
	 * Generates a resource owner object from a successful resource owner
	 * details request.
	 *
	 * @since 2.3.0
	 *
	 * @param array       $response The response data.
	 * @param AccessToken $token    The access token.
	 *
	 * @return ZohoUser
	 */
	protected function createResourceOwner( array $response, AccessToken $token ) {

		return new ZohoUser( $response );
	}
}
