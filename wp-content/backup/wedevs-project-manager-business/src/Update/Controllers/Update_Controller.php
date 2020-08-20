<?php

namespace WeDevs\PM_Pro\Update\Controllers;

use WP_REST_Request;
use League\Fractal\Resource\Item as Item;
use League\Fractal\Resource\Collection as Collection;
use Illuminate\Database\Capsule\Manager as DB;
use WeDevs\PM\Common\Traits\Transformer_Manager;
use WeDevs\PM\Common\Traits\Request_Filter;
use WeDevs\PM_Pro\Core\Update\Update;

class Update_Controller {
	use Transformer_Manager, Request_Filter;

	public function index( WP_REST_Request $request ) {
  		$updates = new update( pm_pro_config( 'app.plan' ) );

        $data = array(
            'license' => $updates->get_license_key(),
            'status'  => $updates->get_license_status(),
            'message' => ''
        );

        if ( isset( $data['status']->update ) ) {
            $update = strtotime( $data['status']->update );
            $expired = false;

            if ( time() > $update ) {
                $string  = __( 'has been expired %s ago', 'pm-pro' );
                $expired = true;
            } else {
                $string = __( 'will expire in %s', 'pm-pro' );
            }

            $data['message'] = sprintf( __( 'Your license %s (%s).', 'pm-pro' ), sprintf( $string, human_time_diff( $update, time() ) ), date( 'F j, Y', $update ) );
        }

        wp_send_json_success( $data );
    }

    public function manage_license( WP_REST_Request $request ) {

		$updates = new update( pm_pro_config( 'app.plan' ) );

        $license_option     = $updates->option;
        $license_status_key = $updates->license_status;
        $email              = $request->get_param( 'email' );
        $key                = $request->get_param( 'key' );

        if ( empty( $email ) ) {
            wp_send_json_error( __( 'Please enter your email address', 'wedevs-updater' ) );
        }

        if ( empty( $key ) ) {
            wp_send_json_error( __( 'Please enter your valid license key', 'wedevs-updater' ) );
        }

        update_option( $license_option, array('email' => $_REQUEST['email'], 'key' => $_REQUEST['key']) );
        delete_transient( $license_option );

        $license_status = get_option( $license_status_key );

        if ( !isset( $license_status->activated ) || $license_status->activated != true ) {
            $response = $updates->activation( 'activation' );

            if ( $response && isset( $response->activated ) && $response->activated ) {
                update_option( $license_status_key, $response );

                $update = strtotime( $response->update );
                $expired = false;

                if ( time() > $update ) {
                    $greeting = __( 'Opps! license invalid. ', 'wedevs-updater' );
                    $string   = __( 'has been expired %s ago', 'wedevs-updater' );
                    $expired  = true;
                } else {
                    $greeting = __( 'Congrats! License activated successfully. ', 'wedevs-updater' );
                    $string   = __( 'will expire in %s', 'wedevs-updater' );
                }

                $message = sprintf( '%s Your license %s (%s).', $greeting, sprintf( $string, human_time_diff( $update, time() ) ), date( 'F j, Y', strtotime( $response->update ) ) );

                if ( $expired ) {
                    $message .= sprintf( '<a href="%s" target="_blank">%s</a>', 'https://wedevs.com/account/', __( 'Renew License', 'wedevs-updater' ) );
                }

                wp_send_json_success( array( 'data' => $response, 'message' => $message ) );
            }

            wp_send_json_success( array( 'data' => $response, 'message' => __( 'Invalid license', 'wedevs-updater' ) ) );

        } else {
            wp_send_json_success( $license_status );
        }

        wp_send_json_error( __( 'Something went wrong', 'wedevs-updater' ) );
    }

    public function delete_license() {

        $updates = new update( pm_pro_config( 'app.plan' ) );
        $option = $updates->get_license_key();

        global $wp_version;


        $params = array(
            'timeout'    => ( ( defined( 'DOING_CRON' ) && DOING_CRON ) ? 30 : 3 ),
            'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url( '/' ),
            'body'       => array(
                'request'     => 'deactivation',
                'email'       => $option['email'],
                'licence_key' => $option['key'],
                'product_id'  => $updates->product_id,
                'instance'    => home_url()
            )
        );

        $response = wp_remote_post( 'http://api.wedevs.com/activation', $params );
        $update   = wp_remote_retrieve_body( $response );
        $update   = json_decode( $update );

        if ( ! empty( $update->reset ) && $update->reset ) {
            $license_option     = $updates->option;
            $license_status_key = $updates->license_status;

            delete_option( $license_option );
            delete_transient( $license_option );
            delete_option( $license_status_key );
        }

        wp_send_json_success( __( 'License successfully deactivated', 'wedevs-updater' ) );
    }
}


