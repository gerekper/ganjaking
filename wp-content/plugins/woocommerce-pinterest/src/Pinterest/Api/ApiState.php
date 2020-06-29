<?php namespace Premmerce\WooCommercePinterest\Pinterest\Api;

/**
 * Class ApiState
 * Responsible for storing Api state
 *
 * @package Premmerce\WooCommercePinterest\Pinterest\Api
 *
 * @todo think about refactoring. Maybe have method getApiStatus with status codes.
 */
class ApiState {

	const V1_TOKEN_OPTION_NAME = 'woocommerce_pinterest_token_v1';

	const V3_TOKEN_OPTION_NAME = 'woocomerce_pinterest_token_v3';

	const API_AUTH_FAILED_OPTION_NAME = 'woocommerce_pinterest_api_failed';


	/**
	 * Current user token
	 *
	 * @param string $version
	 *
	 * @return string|null
	 */
	public function getToken( $version) {
		return get_option('v1' === $version ? self::V1_TOKEN_OPTION_NAME : self::V3_TOKEN_OPTION_NAME, null);
	}


	/**
	 * Current user
	 *
	 * @return array|null
	 */
	public function getUser() {
		return get_option('woocommerce_pinterest_user', null);
	}

	/**
	 * Check if default board is defined in settings
	 *
	 * @return bool
	 */
	public function hasDefaultBoard() {
		$settings = get_option('woocommerce_pinterest_settings');

		return ! empty($settings['board']);
	}

	/**
	 * Current user id
	 *
	 * @return string
	 */
	public function getUserId() {
		$user = $this->getUser();

		return empty($user['id']) ? null : $user['id'];
	}

	/**
	 * Disconnect current user
	 *
	 * @param string $apiVersion
	 *
	 * @return bool
	 */
	public function disconnect( $apiVersion) {

		$result = delete_option(self::V3_TOKEN_OPTION_NAME);


		if ('v1' === $apiVersion) {
			$result = delete_option(self::V1_TOKEN_OPTION_NAME);
			$result = delete_option('woocommerce_pinterest_user') && $result;
		}

		return $result;
	}

	/**
	 * Waiting time
	 *
	 * @return int
	 */
	public function getWaitingTime() {
		return get_option('woocommerce_pinterest_wait', 0);
	}

	/**
	 * Disable api for time in seconds
	 *
	 * Used when request failed with 403 code (TOO_MANY_REQUESTS )
	 *
	 * @param int $time
	 *
	 * @return bool
	 */
	public function wait( $time = 3600) {
		return update_option('woocommerce_pinterest_wait', time() + $time);
	}

	/**
	 * Set pins background creating on
	 *
	 * @return bool
	 */
	public function scheduleBg() {
		return update_option('woocommerce_pinterest_start_bg', true);
	}

	/**
	 * Set pins background creating off
	 *
	 * @return bool
	 */
	public function unScheduleBg() {
		return update_option('woocommerce_pinterest_start_bg', false);
	}

	/**
	 * Background processing is scheduled to start
	 *
	 * @return bool
	 */
	public function isScheduledBg() {
		return get_option('woocommerce_pinterest_start_bg', false);
	}

	/**
	 * Api is ready and bg scheduled
	 *
	 * @return bool
	 */
	public function canStartProcessing() {
		return $this->isReady() && $this->isScheduledBg();
	}

	/**
	 * Api is ready to process requests
	 *
	 * @return bool
	 */
	public function isReady() {
		return $this->isConnected('v1') && ! $this->isWaiting();
	}

	/**
	 * Api is connected
	 *
	 * @param string $apiVersion
	 *
	 * @return bool
	 */
	public function isConnected( $apiVersion) {
		return $this->getToken($apiVersion) && $this->getUserId();
	}

	/**
	 * Api temporary disabled
	 *
	 * @return bool
	 */
	public function isWaiting() {
		return $this->getWaitingTime() > time();
	}

	public function setApiAuthFailed() {
		update_option(self::API_AUTH_FAILED_OPTION_NAME, true);
	}

	/**
	 * Check if API auth was failed
	 *
	 * @return bool
	 */
	public function isApiAuthFailed() {
		return (bool) get_option(self::API_AUTH_FAILED_OPTION_NAME);
	}

	public function deleteApiAuthFailed() {
		delete_option(self::API_AUTH_FAILED_OPTION_NAME);
	}

	/**
	 * Return current API connection status message
	 *
	 * @return string
	 */
	public function getStateMessage() {
		$stateMessage = '';

		if (! $this->isConnected('v1')) {
			$stateMessage = __('Your account is not connected', 'woocommerce-pinterest');
		} elseif ($this->isWaiting()) {
			$stateMessage = __('API is temporary unavailable', 'woocommerce-pinterest');
		} elseif (! $this->hasDefaultBoard()) {
			$stateMessage = __('Default board is not selected', 'woocommerce-pinterest');
		} elseif (! $this->isConnected('v3')) {
			$stateMessage = __('Your account is connected to the basic API only. You will be able to pin products, but not automatically verify your domain and more.', 'woocommerce-pinterest' );
		}

		return $stateMessage;
	}
}
