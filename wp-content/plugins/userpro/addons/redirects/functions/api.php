<?php

class userpro_rd_api {

	function __construct() {

	}
	
	function map_url( $url, $user ){
		
		$array = array(
			'{username}' => $user->user_login,
		);
		
		foreach( get_option('userpro_fields') as $key=>$arr){
			$array['{' . $key . '}'] = $this->filter( userpro_profile_data( $key, $user->ID ) );
		}
		
		$search = array_keys($array);
		$replace = array_values($array);
		$url = str_replace( $search, $replace, $url );
		return $url;
		
	}
	
	function filter($data){
		$data = str_replace(' ','', $data);
		$data = strtolower($data);
		return $data;
	}
	
}

$GLOBALS['userpro_redirection'] = new userpro_rd_api();