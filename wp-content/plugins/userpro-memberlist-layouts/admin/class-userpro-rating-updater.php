<?php

class userpro_rating_updater {
	public $licence_key;
	public $plugin_path;
	public  $plugin_slug;
	public $api_url = 'http://userproplugin.com/update_api/';
	
	function __construct($licence_key , $plugin_path) {
		$this->licence_key = $licence_key;
		$this->plugin_path = $plugin_path;
		$plugin_slug = 'user-pro_rating';
		$this->plugin_slug = $plugin_slug;
		add_filter( 'pre_set_site_transient_update_plugins', array($this , 'check_update' ) );
	}
	
	function check_update($transient) {
		global $wp_version;
		
		if(empty($transient->checked)) return $transient;
		
		$request_args = array(
				'licence' => $this->licence_key,
				'slug' => $this->plugin_slug,
				'version' => $transient->checked[$this->plugin_path] ,
				'transient' =>$transient , 
				'wp_version' => $wp_version,
				'home_url'	=>	home_url() ,
				'plugin_path'	=> $this->plugin_path
		);
		
		$raw_response = wp_remote_post( $this->api_url ,
						 array( 'method' => 'POST' , 
						 		'body'=> $request_args ) );
		if( !is_wp_error($raw_response) && ($raw_response['response']['code'] == 200) )
		{
			$response = unserialize($raw_response['body']);		
			
		}
		if( is_object($response) && !empty($response) ) {
			// Feed the update data into WP updater

			$transient->response[$this->plugin_path] = $response;
		}
		
		if ( isset( $transient->response[$this->plugin_path] ) ) {
			if ( strpos( $transient->response[$this->plugin_path]->package, 'wordpress.org' ) !== false  ) {
				unset($transient->response[$this->plugin_path]);
			}
		}
		
		return $transient;
	}
}
?>