<?php

namespace Essential_Addons_Elementor\Pro\Classes\License;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

use Exception;
use Essential_Addons_Elementor\Pro\Classes\License\Contracts\ApiAdapter;

/**
 * @property string $textdomain
 * @property string $action_prefix
 */
#[\AllowDynamicProperties]
class AJAXApi extends ApiAdapter {

	/**
	 * @throws Exception
	 */
	public function register() {
		if ( ! isset( $this->action_prefix ) ) {
			throw new Exception( "action_prefix needs to be set in ajax configuration" );
		}

		add_action( "wp_ajax_{$this->action_prefix}/license/activate", [ $this, 'activate' ] );
		add_action( "wp_ajax_{$this->action_prefix}/license/deactivate", [ $this, 'deactivate' ] );
		add_action( "wp_ajax_{$this->action_prefix}/license/submit-otp", [ $this, 'submit_otp' ] );
		add_action( "wp_ajax_{$this->action_prefix}/license/resend-otp", [ $this, 'resend_otp' ] );
	}

	/**
	 * Get the API Config
	 * @return array
	 */
	public function get_api_config() {
		return array_merge( parent::get_api_config(), [
			'action'  => $this->action_prefix,
			'api_url' => esc_url( admin_url( 'admin-ajax.php' ) )
		] );
	}

	public function error( $code, $message ) {
		wp_send_json_error( [
			'code'    => $code,
			'message' => $message
		] );
	}

	private function nonce_permission_check(  ) {
		if ( ! isset( $_POST['_nonce'] ) || ! $this->verify_nonce( $_POST['_nonce'] ) ) {
			$this->error( 'nonce_error', __( 'Nonce Verifications Failed.', $this->textdomain ) );
		}

		if ( ! $this->permission_check() ) {
			$this->error( 'no_permission', __( 'You don\'t have permission to take this action.', $this->textdomain ) );
		}
	}

	/**
	 * @param $request array
	 *
	 * @return void
	 */
	public function activate( $request = [] ) {
		$this->nonce_permission_check();

		$response = $this->license_manager->activate( [
			'license_key' => sanitize_text_field( $_POST['license_key'] )
		] );

		if ( is_wp_error( $response ) ) {
			$this->error( $response->get_error_code(), $response->get_error_message() );
		}

		wp_send_json_success( $response );
	}

	/**
	 * @param $request array
	 *
	 * @return void
	 */
	public function deactivate( $request = [] ) {
		$response = $this->license_manager->deactivate();

		if ( is_wp_error( $response ) ) {
			$this->error( $response->get_error_code(), $response->get_error_message() );
		}

		wp_send_json_success( $response );
	}

	public function submit_otp( $request = [] ) {
		$this->nonce_permission_check();

		$args = [
			'otp' => sanitize_text_field( $_POST['otp'] ),
			'license_key' => sanitize_text_field( $_POST['license'] )
		];

		$response = $this->license_manager->submit_otp( $args );

		if ( is_wp_error( $response ) ) {
			$this->error( $response->get_error_code(), $response->get_error_message() );
		}

		wp_send_json_success( $response );
	}

	public function resend_otp( $request = [] ) {
		$this->nonce_permission_check();

		$args = [
			'license_key' => sanitize_text_field( $_POST['license'] )
		];

		$response = $this->license_manager->resend_otp( $args );

		if ( is_wp_error( $response ) ) {
			$this->error( $response->get_error_code(), $response->get_error_message() );
		}

		wp_send_json_success( $response );
	}
}
