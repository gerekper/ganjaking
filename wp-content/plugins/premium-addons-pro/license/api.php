<?php

/**
 * PAPRO License API.
 */
namespace PremiumAddonsPro\License;

use PremiumAddonsPro\Admin\Includes\Admin_Helper;

class API {

	/**
	 * PAPRO Activate License
	 *
	 * Handles license activation
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public static function papro_activate_license( $license_key ) {
		// data to send in our API request
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license_key,
			'item_id'    => PAPRO_ITEM_ID,
			'item_name'  => rawurlencode( PAPRO_ITEM_NAME ),
			'url'        => home_url(),
		);

		// Call the custom API.
		$response = self::call_custom_api( PAPRO_STORE_URL, $api_params );

		// make sure the response came back okay
		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			$message = ( is_wp_error( $response ) ) ? $response->get_error_message() : __( 'An error occurred, please try again.', 'premium-addons-pro' );
		} else {
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			if ( false === $license_data->success ) {

				switch ( $license_data->error ) {

					case 'expired':
						$message = sprintf(
							__( 'Your license key expired on %s.', 'premium-addons-pro' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license_data->expires, current_time( 'timestamp' ) ) )
						);
						break;

					case 'revoked':
						$message = __( 'Your license key has been disabled.', 'premium-addons-pro' );
						break;

					case 'missing':
						$message = __( 'Invalid license.', 'premium-addons-pro' );
						break;

					case 'invalid':
					case 'site_inactive':
						$message = __( 'Your license is not active for this URL.', 'premium-addons-pro' );
						break;

					case 'item_name_mismatch':
						$message = sprintf( __( 'This appears to be an invalid license key for %s.', 'premium-addons-pro' ), PAPRO_ITEM_NAME );
						break;

					case 'no_activations_left':
						$message = __( 'Your license key has reached its activation limit. You can manage sites from your account settings page.', 'premium-addons-pro' );
						break;

					default:
						$message = __( 'An error occurred, please try again.', 'premium-addons-pro' );
						break;

				}
			}
		}

		if ( ! empty( $message ) ) {
			$base_url = 'admin.php?page=premium-addons#tab=license';
			$redirect = add_query_arg(
				array(
					'sl_activation' => 'false',
					'message'       => urlencode( $message ),
				),
				$base_url
			);
			wp_redirect( $redirect );
			exit();
		}

		update_option( 'papro_license_key', $license_key );
		update_option( 'papro_license_status', $license_data->license );

		wp_redirect( 'admin.php?page=premium-addons#tab=license' );

		exit();
	}

	/**
	 * PAPRO Deactivate License
	 *
	 * Handles license deactivation
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public static function papro_deactivate_license( $license_key ) {
		// data to send in our API request
		$api_params = array(
			'edd_action' => 'deactivate_license',
			'license'    => $license_key,
			'item_id'    => PAPRO_ITEM_ID,
			'item_name'  => rawurlencode( PAPRO_ITEM_NAME ),
			'url'        => home_url(),
		);

		// Call the custom API.
		$response = self::call_custom_api( PAPRO_STORE_URL, $api_params );

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {

			if ( is_wp_error( $response ) ) {
				$message = $response->get_error_message();
			} else {
				$message = __( 'An error occurred, please try again.', 'premium-addons-pro' );
			}

			$base_url = 'admin.php?page=premium-addons#tab=license';
			$redirect = add_query_arg(
				array(
					'sl_activation' => 'false',
					'message'       => urlencode( $message ),
				),
				$base_url
			);

			wp_redirect( $redirect );
			exit();

		}

		delete_option( 'papro_license_status' );

		delete_option( 'papro_license_key' );

		wp_redirect( 'admin.php?page=premium-addons#tab=license' );

		exit();

	}

	/**
	 * Call Custom API
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param string $url URL to retrieve
	 * @param array  $args request paramters
	 */
	public static function call_custom_api( $url, $args ) {

		$response = wp_remote_post(
			$url,
			array(
				'timeout'   => 40,
				'sslverify' => false,
				'body'      => $args,
			)
		);

		return $response;
	}

}
