<?php

namespace WPDeveloper\BetterDocsPro\Dependencies\WPDeveloper\Licensing;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

use Exception;
use WPDeveloper\BetterDocsPro\Dependencies\WPDeveloper\Licensing\Contracts\ApiAdapter;

#[\AllowDynamicProperties]
class AJAXApi extends ApiAdapter {

    public function register() {
        if ( ! isset( $this->action_prefix ) ) {
            throw new Exception( "action_prefix needs to be set in ajax configuration" );
        }

        add_action( "wp_ajax_{$this->action_prefix}/license/activate", [$this, 'activate'] );
        add_action( "wp_ajax_{$this->action_prefix}/license/dectivate", [$this, 'deactivate'] );
    }

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

    public function activate( $request = [] ) {
        if ( ! isset( $_POST['_nonce'] ) || ! $this->verify_nonce( $_POST['_nonce'] ) ) {
            $this->error( 'nonce_error', __( 'Nonce Verifications Failed.', $this->textdomain ) );
        }

        if ( ! $this->permission_check() ) {
            $this->error( 'no_permission', __( 'You don\'t have permission to take this action.', $this->textdomain ) );
        }

        $response = $this->license_manager->activate( [
            'license_key' => sanitize_text_field( $_POST['license_key'] )
        ] );

        if ( is_wp_error( $response ) ) {
            $this->error( $response->get_error_code(), $response->get_error_message() );
        }

        wp_send_json_success( $response );
    }

    public function deactivate( $request = [] ) {
        $response = $this->license_manager->deactivate();

        if ( is_wp_error( $response ) ) {
            $this->error( $response->get_error_code(), $response->get_error_message() );
        }

        wp_send_json_success( $response );
    }
}
