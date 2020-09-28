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
            'license' => 'B5E0B5F8DD8689E6ACA49DD6E6E1A930',
            'status'  => 'valid',
            'message' => 'success'
        );

       
            $update = '01 jan,2030';
            $expired = false;

           
            $data['message'] = sprintf( __( 'Your license %s (%s).', 'pm-pro' ), sprintf( $string, human_time_diff( $update, time() ) ), date( 'F j, Y', $update ) );
        

        wp_send_json_success( $data );
    }

    public function manage_license( WP_REST_Request $request ) {

		$updates = new update( pm_pro_config( 'app.plan' ) );

        $license_option     = $updates->option;
        $license_status_key = 'B5E0B5F8DD8689E6ACA49DD6E6E1A930';
        $email              = 'noreply@gpl.com';
        $key                = 'B5E0B5F8DD8689E6ACA49DD6E6E1A930';

      

        update_option( $license_option, array('email' => $email, 'key' => $key) );
        delete_transient( $license_option );

        $license_status = 'valid';

        
         wp_send_json_success( $license_status );
   
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

      

        wp_send_json_success( __( 'License successfully deactivated', 'wedevs-updater' ) );
    }
}


