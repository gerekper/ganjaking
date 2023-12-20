<?php

namespace Essential_Addons_Elementor\Pro\Classes\License;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Exception;
use WP_REST_Server;
use Essential_Addons_Elementor\Pro\Classes\License\Contracts\ApiAdapter;

#[\AllowDynamicProperties]
class RESTApi extends ApiAdapter {
    private $version = 'v1';

    public function register() {
        if ( ! isset( $this->namespace ) ) {
            throw new Exception( "namespace is missing in your rest configuration." );
        }

        add_action( 'rest_api_init', [$this, 'routes'] );
    }

    public function get_api_config() {
        return array_merge( parent::get_api_config(), [
            'api_url' => esc_url( trailingslashit( rest_url( $this->get_namespace() ) ) )
        ] );
    }

    public function routes() {
        $this->route( '/license/activate', [$this, 'activate'], $this->args() );
        $this->route( '/license/deactivate', [$this, 'deactivate'] );
        $this->route( '/license/submit-otp', [$this, 'submit_otp'], $this->args([
            'otp' => [
                'required'          => true,
                'validate_callback' => function ( $param, $request, $key ) {
                    return is_string( $param ) && ! empty( $param );
                }
            ]
        ]) );
        $this->route( '/license/resend-otp', [$this, 'resend_otp'], $this->args() );
        $this->route( '/license/get-license', [$this, 'get_license'] );
    }

    public function activate( $request ) {
		return $this->license_manager->activate( [
            'license_key' => sanitize_text_field( $request->get_param( 'license_key' ) )
        ] );
    }

    public function deactivate( $request ) {
        return $this->license_manager->deactivate();
    }

    /**
     * Handles OTP submission request.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|WP_REST_Response
     */
	public function submit_otp( $request ) {
		$args = [
			'otp'         => sanitize_text_field( $request->get_param( 'otp') ),
			'license_key' => sanitize_text_field( $request->get_param( 'license_key') )
		];

		return $this->license_manager->submit_otp( $args );
	}

    /**
     * Handles OTP resend request.
     *
     * @param WP_REST_Request $request Full details about the request.
     * @return WP_Error|WP_REST_Response
     */
	public function resend_otp( $request ) {
		$args = [
			'license_key' => sanitize_text_field( $request->get_param( 'license_key') )
		];

		return $this->license_manager->resend_otp( $args );
	}

    /**
     * Retrieves the license details.
     *
     * This method uses the LicenseManager to get the license data, hide the license key, and format the title.
     * It then returns an array with the title, hidden license key, and license status.
     *
     * @return array An array containing the title, hidden license key, and license status.
     */
    public function get_license(){
        $license_data = $this->license_manager->get_license_data();
        $license_key  = $this->license_manager->hide_license_key($license_data['license_key']);
        $status       = $license_data['license_status'];
        $title        = sprintf(__('%s License', $this->license_manager->textdomain), $this->license_manager->item_name);

        return ['title' => $title, 'key' => $license_key, 'status' => $status];
    }

    protected function args($args = []) {
        return wp_parse_args($args, [
            'license_key'       => [
                'required'          => true,
                'validate_callback' => function ( $param, $request, $key ) {
                    return is_string( $param ) && ! empty( $param );
                }
            ]
        ]);
    }

    private function get_namespace() {
        return $this->namespace . '/' . $this->version;
    }

    protected function route( $endpoint, $callback, $args = [] ) {
        return register_rest_route( $this->get_namespace(), $endpoint, [
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => $callback,
            'permission_callback' => [$this, 'permission_check'],
            'args'                => $args
        ] );
    }
}