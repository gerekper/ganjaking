<?php namespace Premmerce\WooCommercePinterest\Pinterest\Api;

/**
 * Class Api
 * Responsible for Pinterest API calls
 *
 * @package Premmerce\WooCommercePinterest\Pinterest
 */
class Api {

	const CODE_AUTH_FAILED = 401;

	const CODE_TOO_MANY_REQUESTS = 429;

	/**
	 * Pinterest API base url
	 *
	 * @var string
	 */
	private $base = 'https://api.pinterest.com/';

	/**
	 * ApiState instance
	 *
	 * @var ApiState
	 */
	private $state;

	/**
	 * Api constructor.
	 *
	 * @param $state
	 */
	public function __construct( ApiState $state) {
		$this->state = $state;
	}

	/**
	 * Return ApiState instance
	 *
	 * @return ApiState
	 */
	public function getState() {
		return $this->state;
	}

	/**
	 * Create pin
	 *
	 * Structure
	 * data:
	 *      board (required): The board you want the new Pin to be on. In the format <username>/<board_name>.
	 *      note (required): The Pin’s description.
	 *      link (optional): The URL the Pin will link to when you click through.
	 *      image: Upload the image you want to pin using multipart form data.
	 *      image_url: The link to the image that you want to Pin.
	 *      image_base64: The link of a Base64 encoded image.
	 *
	 * @param array $data
	 *
	 * @return PinterestApiResponse
	 * @throws PinterestApiException
	 */
	public function createPin( $data) {
		$apiVersion = 'v1';

		$url = $this->buildRequestUrl($apiVersion, 'pins', array('access_token' => $this->state->getToken($apiVersion)));

		return $this->request($url, array(
			'method' => 'POST',
			'body'   => $this->sanitizePin($data),
		));
	}

	/**
	 * Delete pin from Pinterest
	 *
	 * @param $id
	 *
	 * @return PinterestApiResponse
	 * @throws PinterestApiException
	 */
	public function deletePin( $id) {
		$path       = 'pins/' . intval($id);
		$apiVersion = 'v1';

		$url = $this->buildRequestUrl($apiVersion, $path, array('access_token' => $this->state->getToken($apiVersion)));

		$response = $this->request($url, array(
			'method' => 'DELETE'
		));

		return $response;
	}

	/**
	 * Update pin on Pinterest
	 *
	 * @param string $id
	 * @param array $data
	 *
	 * data:
	 *      pin (required): The ID (unique string of numbers and letters) of the Pin you want to edit.
	 *      board (optional): The board you want to move the Pin to, in the format <username>/<board_name>.
	 *      note (optional): The new Pin description.
	 *      link (optional): The new Pin link.
	 *
	 * Note: You can only edit the link of a repinned Pin if the pinner owns the domain of the Pin in question,
	 * or if the Pin itself has been created by the pinner.
	 *
	 * @return PinterestApiResponse
	 * @throws PinterestApiException
	 */
	public function updatePin( $id, $data) {
		$path       = 'pins/' . intval($id);
		$apiVersion = 'v1';
		$url        = $this->buildRequestUrl($apiVersion, $path, array('access_token' => $this->state->getToken($apiVersion)));


		$response = $this->request($url, array(
			'method' => 'PATCH',
			'body'   => $this->sanitizePin($data)
		));

		return $response;
	}

	/**
	 * Get Pinterest user data from Pinterest
	 *
	 * @return PinterestApiResponse
	 * @throws PinterestApiException
	 */
	public function getUser() {
		$apiVersion = 'v1';
		$url        = $this->buildRequestUrl($apiVersion, 'me', array('access_token' => $this->state->getToken($apiVersion)));

		$response = $this->request($url, array('method' => 'GET'));

		return $response;
	}

	/**
	 * Get current user board list from Pinterest
	 *
	 * @return PinterestApiResponse
	 * @throws PinterestApiException
	 */
	public function getBoards() {
		$apiVersion             = 'v1';
		$params['access_token'] = $this->state->getToken($apiVersion);

		$url = $this->buildRequestUrl($apiVersion, 'me/boards', array('access_token' => $this->state->getToken($apiVersion)));

		$response = $this->request($url, array('method' => 'GET'));

		return $response;
	}

	/**
	 * Add a website and associate it with a user
	 *
	 * @param string $domain
	 *
	 * @return PinterestApiResponse
	 * @throws PinterestApiException
	 */
	public function setDomain( $domain) {
		$apiVersion = 'v3';

		$params['access_token'] = $this->state->getToken($apiVersion);
		$params['website_url']  = $domain;

		$url = $this->buildRequestUrl($apiVersion, 'users/me/domain', $params);

		$response = $this->request($url, array('method' => 'POST'));

		return $response;
	}

	/**
	 * Get the verification code to be placed at the bottom of index.html file
	 *
	 * @param string $domain
	 *
	 * @return PinterestApiResponse
	 * @throws PinterestApiException
	 */
	public function getVerificationCode( $domain) {
		$apiVersion             = 'v3';
		$params['access_token'] = $this->state->getToken($apiVersion);

		$path     = "domains/{$domain}/verification";
		$url      = $this->buildRequestUrl($apiVersion, $path, $params);
		$response = $this->request($url, array('method' => 'GET'));

		return $response;
	}

	/**
	 * Verify domain
	 *
	 * @param $domain
	 *
	 * @return PinterestApiResponse
	 * @throws PinterestApiException
	 */
	public function verifyDomain( $domain) {
		$apiVersion             = 'v3';
		$params['access_token'] = $this->state->getToken($apiVersion);
		$path                   = "domains/{$domain}/verification/metatag";

		$url = $this->buildRequestUrl($apiVersion, $path, $params);

		$response = $this->request($url, array('method' => 'POST'));

		return $response;
	}

	/**
	 * Sanitize pin data
	 *
	 * @param array $pin
	 *
	 * @return array
	 */
	protected function sanitizePin( array $pin) {
		$sanitizeCallbacks = array(
			'pin'       => 'sanitize_key',
			'board'     => 'sanitize_key',
			'note'      => 'esc_html',
			'link'      => 'esc_url_raw',
			'image_url' => 'esc_url_raw',
		);

		$sanitized = array();

		foreach ($sanitizeCallbacks as $field => $callback) {
			if (array_key_exists($field, $pin)) {
				$sanitized[$field] = call_user_func($callback, $pin[$field]);
			}
		}

		return apply_filters('woocommerce_pinterest_api_sanitize_pin', $sanitized, $pin);
	}

	/**
	 * Send Pinterest API request
	 *
	 * @param $url
	 * @param $params
	 *
	 * @return PinterestApiResponse
	 * @throws PinterestApiException
	 */
	protected function request( $url, $params) {
		$response = wp_remote_request($url, $params);

		$response = new PinterestApiResponse($response);

		if ($response->isFailed()) {
			$e = new PinterestApiException($response->getMessage(), $response->getCode());
			throw $e;
		}

		return $response;
	}

	/**
	 * Build url for Pinterest API request
	 *
	 * @param string $apiVersion
	 * @param string $path
	 * @param array $queryArgs
	 *
	 * @return string
	 */
	private function buildRequestUrl( $apiVersion, $path, array $queryArgs) {
		return $this->base . trailingslashit($apiVersion) . $path . '?' . http_build_query($queryArgs);
	}
}
