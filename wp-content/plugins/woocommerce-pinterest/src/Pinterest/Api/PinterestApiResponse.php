<?php namespace Premmerce\WooCommercePinterest\Pinterest\Api;

/**
 * Class Response
 * Wrapper for wp response
 *
 * @package Premmerce\WooCommercePinterest\Pinterest\Api
 */
class PinterestApiResponse {

	/**
	 * API response code
	 *
	 * @var int|string
	 */
	private $code;

	/**
	 * API response body
	 *
	 * @var
	 */
	private $body;

	/** WP response data
	 *
	 * @var array
	 */
	private $wpResponse;

	public function __construct( $wpResponse) {
		$this->code = (int) wp_remote_retrieve_response_code($wpResponse);
		$body       = wp_remote_retrieve_body($wpResponse);

		if (! empty($body)) {
			$body = json_decode($body, true);
		}

		$this->body       = is_array($body) ? $body : array();
		$this->wpResponse = $wpResponse;
	}

	/**
	 * Check if response has error code
	 *
	 * @return bool
	 */
	public function isFailed() {
		return $this->code < 200 || $this->code >= 300;
	}

	/**
	 * Return response code
	 *
	 * @return int
	 */
	public function getCode() {
		return $this->code;
	}

	/**
	 * Return response body
	 *
	 * @return array
	 */
	public function getBody() {
		return $this->body;
	}

	/**
	 * Return data from response body
	 *
	 * @return array
	 */
	public function getData() {
		return isset($this->body['data']) && is_array($this->body['data']) ? $this->body['data'] : array();
	}

	/**
	 * Return message from response array
	 *
	 * @return string
	 */
	public function getMessage() {
		if (isset($this->body['message']) && is_string($this->body['message'])) {
			return $this->body['message'];
		}

		if ($this->isFailed()) {
			return __('Request failed', 'woocommerce-pinterest');
		}

		return '';
	}
}
