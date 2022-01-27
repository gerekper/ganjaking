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
	public function __construct( ApiState $state ) {
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
	 *      note (required): The Pin’s description
	 *      link (optional): The URL the Pin will link to when you click through.
	 *      image: Upload the image you want to pin using multipart form data.
	 *      image_url: The link to the image that you want to Pin.
	 *      image_base64: The link of a Base64 encoded image.
   *      title: Pin display title - 100 chars maximum.
	 *
	 * @param array $data
	 *
	 * @return PinterestApiResponse
	 * @throws PinterestApiException
	 */
	public function createPin( $data ) {

		$apiVersion = 'v3';

		$url = $this->buildRequestUrl( $apiVersion, 'pins', array() );

		return $this->request( $url, array(
			'method' => 'PUT',
			'body'   => $this->sanitizePin( $data ),
		) );
	}

  /**
   * Upload carousel images
   *
   * @param array $data
   *
   * @return PinterestApiResponse
   * @throws PinterestApiException
   */
  public function uploadCarouselImages( $data )
  {
    $apiVersion = 'v3';

    $url = $this->buildRequestUrl( $apiVersion, 'pincarousel/images/upload', array() );

    return $this->request( $url, array(
      'method' => 'PUT',
      'body'   => $this->sanitizePin( $data ),
    ) );
  }

  /**
   * Create carousel pin
   *
   * @param array $data
   *
   * @return PinterestApiResponse
   * @throws PinterestApiException
   */
  public function createCarouselPin( $data ) {

    $apiVersion = 'v3';

    $url = $this->buildRequestUrl( $apiVersion, 'pins', array() );

    return $this->request( $url, array(
      'method' => 'PUT',
      'body'   => $data,
    ) );
  }

  /**
   * Update carousel pin
   *
   * @param string $id
   * @param array $data
   *
   * @return PinterestApiResponse
   * @throws PinterestApiException
   */
  public function updateCarouselPin( $id, $data )
  {
    $path       = 'pins/' . intval( $id );
    $apiVersion = 'v3';
    $url        = $this->buildRequestUrl( $apiVersion, $path, array( 'access_token' => $this->state->getToken( $apiVersion ) ) );

    $response = $this->request( $url, array(
      'method' => 'POST',
      'body'   => $data
    ) );

    return $response;
  }

	/**
	 * Create board
	 *
	 * Structure
	 * data:
	 *      name (required): Board name.
	 *      description: Board description.
	 *
	 * @param array $data
	 *
	 * @return PinterestApiResponse
	 * @throws PinterestApiException
	 */
	public function createBoard( $data ) {

		$apiVersion = 'v3';

		$url = $this->buildRequestUrl( $apiVersion, 'boards', array() );

		return $this->request( $url, array(
			'method' => 'PUT',
			'body'   => array(
			  'name'        => esc_html( $data['name'] ),
			  'description' => esc_html( $data['description'] ),
      ),
		) );
	}

	/**
	 * Delete pin from Pinterest
	 *
	 * @param $id
	 *
	 * @return PinterestApiResponse
	 * @throws PinterestApiException
	 */
	public function deletePin( $id ) {
		$path       = 'pins/' . intval( $id );
		$apiVersion = 'v3';

		$url = $this->buildRequestUrl( $apiVersion, $path, array( 'access_token' => $this->state->getToken( $apiVersion ) ) );

		$response = $this->request( $url, array(
			'method' => 'DELETE'
		) );

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
	public function updatePin( $id, $data ) {
		$path       = 'pins/' . intval( $id );
		$apiVersion = 'v3';
		$url        = $this->buildRequestUrl( $apiVersion, $path, array( 'access_token' => $this->state->getToken( $apiVersion ) ) );

		$response = $this->request( $url, array(
			'method' => 'POST',
			'body'   => $this->sanitizePin( $data )
		) );

		return $response;
	}

	/**
	 * Get Pinterest user data from Pinterest
	 *
	 * @return PinterestApiResponse
	 * @throws PinterestApiException
	 */
	public function getUser() {
		$apiVersion = 'v3';
		$url        = $this->buildRequestUrl( $apiVersion, 'users/me', array( 'access_token' => $this->state->getToken( $apiVersion ) ) );

		$response = $this->request( $url, array( 'method' => 'GET' ) );

		return $response;
	}

	/**
	 * Get current user board list from Pinterest
	 *
	 * @param int $bookmark
	 *
	 * @return PinterestApiResponse
	 * @throws PinterestApiException
	 */
	public function getBoards( $bookmark = null ) {
		$apiVersion             = 'v3';
		$params['access_token'] = $this->state->getToken( $apiVersion );

		$args = array(
			'access_token' => $this->state->getToken( $apiVersion )
		);

		if ( $bookmark ) {
			$args['bookmark'] = $bookmark;
		}

		$url = $this->buildRequestUrl( $apiVersion, 'users/me/boards/feed/', $args );

		$response = $this->request( $url, array( 'method' => 'GET' ) );

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
	public function setDomain( $domain ) {
		$apiVersion = 'v3';

		$params['access_token'] = $this->state->getToken( $apiVersion );
		$params['website_url']  = $domain;

		$url = $this->buildRequestUrl( $apiVersion, 'users/me/domain', $params );

		$response = $this->request( $url, array( 'method' => 'POST' ) );

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
	public function getVerificationCode( $domain ) {
		$apiVersion             = 'v3';
		$params['access_token'] = $this->state->getToken( $apiVersion );

		$path     = "domains/{$domain}/verification";
		$url      = $this->buildRequestUrl( $apiVersion, $path, $params );
		$response = $this->request( $url, array( 'method' => 'GET' ) );

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
	public function verifyDomain( $domain ) {
		$apiVersion             = 'v3';
		$params['access_token'] = $this->state->getToken( $apiVersion );
		$path                   = "domains/{$domain}/verification/metatag";

		$url = $this->buildRequestUrl( $apiVersion, $path, $params );

		$response = $this->request( $url, array( 'method' => 'POST' ) );

		return $response;
	}

	/**
	 * Sanitize pin data
	 *
	 * @param array $pin
	 *
	 * @return array
	 */
	protected function sanitizePin( array $pin ) {
		$sanitizeCallbacks = array(
			'pin'         => 'sanitize_key',
			'board_id'    => 'sanitize_key',
			'title'       => 'esc_html',
			'alt_text'    => 'esc_html',
			'description' => 'esc_html',
			'source_url'  => 'esc_url_raw',
			'image_url'   => 'esc_url_raw',
			'image_urls'  => 'esc_url_raw',
		);

		$sanitized = array();

		foreach ( $sanitizeCallbacks as $field => $callback ) {
			if ( array_key_exists( $field, $pin ) ) {
				$sanitized[ $field ] = call_user_func( $callback, $pin[ $field ] );
			}
		}

		return apply_filters( 'woocommerce_pinterest_api_sanitize_pin', $sanitized, $pin );
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
	protected function request( $url, $params ) {

		$params['headers'] = array(
			'Authorization' => 'Bearer ' . $this->state->getToken( 'v3' ),
		);

		$response = wp_remote_request( $url, $params );
		$response = new PinterestApiResponse( $response );

		if ( $response->isFailed() ) {
			$e = new PinterestApiException( $response->getMessage(), $response->getCode() );
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
	private function buildRequestUrl( $apiVersion, $path, array $queryArgs ) {
		return $this->base . trailingslashit( $apiVersion ) . $path . '?' . http_build_query( $queryArgs );
	}
}
