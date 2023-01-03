<?php
/**
 * Smush API class that handles communications with WPMU DEV API: API class
 *
 * @since 3.0
 * @package Smush\Core\Api
 */

namespace Smush\Core\Api;

use WP_Error;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class Smush_API.
 */
class Smush_API extends Abstract_API {

	/**
	 * Endpoint name.
	 *
	 * @since 3.0
	 *
	 * @var string
	 */
	public $name = 'smush';

	/**
	 * Endpoint version.
	 *
	 * @since 3.0
	 *
	 * @var string
	 */
	public $version = 'v1';

	/**
	 * Check CDN status (same as verify the is_pro status).
	 *
	 * @since 3.0
	 *
	 * @param bool $manual  If it's a manual check. Only manual on button click.
	 *
	 * @return mixed|WP_Error
	 */
	public function check( $manual = false ) {
		if ( isset( $_SERVER['WPMUDEV_HOSTING_ENV'] ) && 'staging' === $_SERVER['WPMUDEV_HOSTING_ENV'] ) {
			return new WP_Error( '503', __( 'Unable to check status on staging.', 'wp-smushit' ) );
		}

		return $this->backoff_sync( function () {
			return $this->request->get(
				"check/{$this->api_key}",
				array(
					'api_key' => $this->api_key,
					'domain'  => $this->request->get_this_site(),
				)
			);
		}, $manual );
	}

	/**
	 * Enable CDN for site.
	 *
	 * @since 3.0
	 *
	 * @param bool $manual  If it's a manual check. Overwrites the exponential back off.
	 *
	 * @return mixed|WP_Error
	 */
	public function enable( $manual = false ) {
		return $this->backoff_sync( function () {
			return $this->request->post(
				'cdn',
				array(
					'api_key' => $this->api_key,
					'domain'  => $this->request->get_this_site(),
				)
			);
		}, $manual );
	}

	private function backoff_sync( $operation, $manual ) {
		$defaults = array(
			'time'  => time(),
			'fails' => 0,
		);

		$last_run = get_site_option( 'wp-smush-last_run_sync', $defaults );

		$backoff = min( pow( 5, $last_run['fails'] ), HOUR_IN_SECONDS ); // Exponential 5, 25, 125, 625, 3125, 3600 max.
		if ( $last_run['fails'] && $last_run['time'] > ( time() - $backoff ) && ! $manual ) {
			$last_run['time'] = time();
			update_site_option( 'wp-smush-last_run_sync', $last_run );

			return new WP_Error( 'api-backoff', __( '[WPMUDEV API] Skipped sync due to API error exponential backoff.', 'wp-smushit' ) );
		}

		$response = call_user_func( $operation );

		$last_run['time'] = time();

		// Clear the API backoff if it's a manual scan or the API call was a success.
		if ( $manual || ( ! is_wp_error( $response ) && 200 === wp_remote_retrieve_response_code( $response ) ) ) {
			$last_run['fails'] = 0;
		} else {
			// For network errors, perform exponential backoff.
			$last_run['fails'] = $last_run['fails'] + 1;
		}

		update_site_option( 'wp-smush-last_run_sync', $last_run );

		return $response;
	}
}
