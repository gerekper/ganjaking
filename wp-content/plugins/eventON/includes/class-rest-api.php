<?php
/**
 * REST API for event access
 * @version 2.9
 */

class EVO_Rest_API{
	private $nonce = 'invalid';
	public function __construct(){
		add_action('wp_loaded', array($this, 'nonce_gen'));
		add_action( 'rest_api_init', array($this, 'rest_routes'));	

		
	}
	function nonce_gen(){
		$this->nonce = wp_create_nonce( 'rest_eventon' );
	}
	function rest_routes(){
		register_rest_route( 
			'eventon/v1','/data/', 
			array(
				'methods' => 'POST',
				'callback' => array($this,'rest_returns'),					
				'permission_callback' => function (WP_REST_Request $request) {
                	return true;
            	}
			) 
		);
	}

	function rest_returns( WP_REST_Request $request){
		$params = $request->get_params();

		$data = array();
		if(isset($params['action'])  ){			
			$nonce = wp_create_nonce( 'rest_'.EVO()->version );
			$response = EVO()->ajax->callback( $params['action'] , $nonce);
		}else{
			$response = array(
				'r'=> $params,
				'd'=> $data
			);
		}

						
		return $response;
	}
}