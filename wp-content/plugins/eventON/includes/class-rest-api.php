<?php
/**
 * REST API for event access
 * @version 4.5.2
 */

class EVO_Rest_API{
	private $nonce = 'invalid';
	static $version = 'v1';

	public function __construct(){
		add_action('wp_loaded', array($this, 'nonce_gen'));
		add_action( 'rest_api_init', array($this, 'rest_routes'));
	}
	function nonce_gen(){
		$this->nonce = wp_create_nonce( 'rest_eventon' );
	}
	// get rest api url
	public static function get_rest_api( $request = ''){
		return esc_url_raw( add_query_arg('evo-ajax', $request, get_rest_url(null,'eventon/'. self::$version . '/data') ));
	}
	function rest_routes(){
		register_rest_route( 
			'eventon/'. self::$version ,'/data', 
			array(
				'methods' => 'POST',
				'callback' => array($this,'rest_returns'),					
				'permission_callback' => function (WP_REST_Request $request) {
                	return true;
            	}
			) 
		);

		register_rest_route( 
			'evo-admin' ,
			'data', 
			array(
				'methods'   => WP_REST_Server::READABLE,
				'callback' => array($this,'rest_admin'),					
				'permission_callback' => function (WP_REST_Request $request) {
                	return true;
            	}
			) 
		);
	}

	function rest_admin(){
		return new WP_REST_Response('Howdy!!');
	}

	function rest_returns( WP_REST_Request $request){
		$params = $request->get_params();
		$data = array();
		if(isset($params['evo-ajax'])  ){			
			$nonce = wp_create_nonce( 'rest_'.EVO()->version );


			$action = $params['evo-ajax'];
			$action = sanitize_text_field( $action );

			return apply_filters('evo_ajax_rest_'. $action, array('html'=>'test'), $params);


		}else{
			$response = array(
				'r'=> $params,
				'd'=> $data
			);
		}

						
		return $response;
	}
}