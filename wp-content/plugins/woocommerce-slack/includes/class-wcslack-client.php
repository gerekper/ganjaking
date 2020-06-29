<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WC_Slack_Client {
	private $client_secret;
	private $access_token;

	protected $slack_endpoint = "https://slack.com/";

	protected $auth = [
		'client_id'    => null,
		'state'        => null,
		'scope'        => null,
		'team'         => null,
		'redirect_uri' => null
	];

	/**
	 * Set client secret.
	 *
	 * @param string $secret Client Secret as given by registration of the Slack application.
	 */
	public function setClientSecret( $secret ) {
		$this->client_secret = $secret;
	}

	/**
	 * Get access token.
	 *
	 * @return string Access token.
	 */
	public function getAccessToken() {
		return $this->access_token;
	}

	/**
	 * Get client id.
	 *
	 * @return string $client_id Client ID as given by registration of the Slack application.
	 */
	public function getClientId() {
		return $this->auth['client_id'];
	}

	/**
	 * Set access token.
	 *
	 * @param string $access_token Access token.
	 */
	public function setAccessToken( $access_token ) {
		$this->access_token = $access_token;
	}

	/**
	 * Set client id.
	 *
	 * @param string $client_id Client ID as given by registration of the Slack application.
	 */
	public function setClientId( $client_id ) {
		$this->auth['client_id'] = $client_id;
	}

	/**
	 * Set application name.
	 *
	 * @param string $state Unique string to be passed back with Slack's redirect for verification.
	 */
	public function setApplicationName( $state ) {
		$this->auth['state'] = $state;
	}

	/**
	 * Set scopes.
	 *
	 * @param array $scope
	 */
	public function setScopes( $scope ) {
		$this->auth['scope'] = implode( ',', $scope );
	}

	/**
	 * Set team name.
	 *
	 * @param string $team Team ID to request authorization.
	 */
	public function setTeam( $team ) {
		$this->auth['team'] = $team;
	}

	/**
	 * Set redirect URI.
	 *
	 * @param string $redirect URL for Slack to redirect after authorization.
	 */
	public function setRedirectUri( $redirect ) {
		$this->auth['redirect_uri'] = $redirect;
	}

	/**
	 * Exchanges an OAuth code for an API access token. Forms a payload to send to the Slack OAuth/Access API call.
	 *
	 * @param string $code The code returned from Slack's redirect OAuth/Authorize.
	 * @return Object Generic object that is the JSON decoded string returned from the payload.
	 * @throws Exception If the Object->ok property is false, will throw with the response's error.
	 */
	public function fetchAccessTokenWithAuthCode( $code ) {
		$payload                          = [];
		$payload['url']                   = $this->slack_endpoint . 'api/oauth.access';
		$payload['post']['client_id']     = $this->auth['client_id'];
		$payload['post']['client_secret'] = $this->client_secret;
		$payload['post']['code']          = $code;
		$payload['post']['redirect_uri']  = $this->auth['redirect_uri'];
		$response                         = $this->post( $payload );

		if ( ! $response->ok ) {
			throw new Exception( 'OAuth.Access: ' . $response->error );
		}

		$this->access_token = $response->access_token;
		$this->auth['team'] = $response->team_name;

		return $this->access_token;
	}

	/**
	 * Revokes a token.
	 *
	 * @return bool Whether token was revoked
	 * @throws Exception If the Object->ok property is false, will throw with the response's error.
	 */
	public function revokeToken() {
		$payload                     = [];
		$payload['url']              = $this->slack_endpoint . 'api/auth.revoke';
		$payload['post']['token']    = $this->getAccessToken();
		$response                    = $this->post( $payload );

		if ( ! $response->ok ) {
			throw new Exception( 'OAuth.Access: ' . $response->error );
		}

		return $response->revoked;
	}

	/**
	 * Creates a URL to authenticate the application.
	 *
	 * @return string Slack URL: OAuth/Authorize with Client ID and possible State, Scope, Team, and Redirect fields.
	 */
	public function createAuthUrl() {
		return $this->slack_endpoint . 'oauth/authorize?' . http_build_query( array_filter( $this->auth ), '', '&' );
	}

	/**
	 * Test connection with token.
	 *
	 * @return bool Whether connection is successful or not.
	 * @throws Exception If the Object->ok property is false, will throw with the response's error.
	 */
	public function testConnection() {
		$payload                          = [];
		$payload['url']                   = $this->slack_endpoint . 'api/auth.test';
		$payload['post']['token']         = $this->getAccessToken();
		$response                         = $this->post( $payload );

		if ( ! $response->ok ) {
			throw new Exception( 'OAuth.Access: ' . $response->error );
		}

		return $response->ok;
	}

	/**
	 * Posts a payload to a URL.
	 *
	 * @param array $payload The payload to be posted.
	 * @return WP_Error|array The response or WP_Error on failure.
	 */
	private function post( $payload ) {
		$response = wp_remote_post( $payload['url'], array(
			'body' => $payload['post'],
		) );

		if ( empty( $response['body'] ) ) {
			throw new Exception( 'Got invalid response object, possibly a WP_Error.' );
		}

		return json_decode( $response['body'] );
	}
}
