<?php

/**
 * Description of A2W_RestApi
 *
 * @author Andrey
 * 
 * @autoload: a2w_init
 */

if (!class_exists('A2W_RestApi')) {

    class A2W_RestApi {
        public function __construct() {
            add_action('rest_api_init', array($this, 'register_routes'));
        }
        
        public function register_routes() {
            register_rest_route('a2w-api/v1', '/info', array(
                'methods' => WP_REST_Server::READABLE,
                'callback' => array($this, 'info'),
                'permission_callback' => '__return_true'
            ));
        }

        public function info($request) {
            $result = array();

            $result['server_ping'] = A2W_SystemInfo::server_ping();
            $result['plugin_version'] = A2W()->version;
            
            return rest_ensure_response($result);
        }       
    }
}
