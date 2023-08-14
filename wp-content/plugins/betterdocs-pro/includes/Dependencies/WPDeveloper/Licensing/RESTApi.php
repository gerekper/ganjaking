<?php

namespace WPDeveloper\BetterDocsPro\Dependencies\WPDeveloper\Licensing;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Exception;
use WP_REST_Server;
use WPDeveloper\BetterDocsPro\Dependencies\WPDeveloper\Licensing\Contracts\ApiAdapter;

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
    }

    public function activate( $request ) {
		return $this->license_manager->activate( [
            'license_key' => sanitize_text_field( $request->get_param( 'license_key' ) )
        ] );
    }

    public function deactivate( $request ) {
        return $this->license_manager->deactivate();
    }

    protected function args() {
        return [
            'license_key' => [
                'validate_callback' => function ( $param, $request, $key ) {
                    return is_string( $param ) && ! empty( $param );
                }
            ]
        ];
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