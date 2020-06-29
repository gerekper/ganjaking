<?php

/*
add_action( 'wp_ajax_mailpoet.search_terms', 'WYSIJA_help_campaigns::ajax_search_terms' );


 */
defined('WYSIJA') or die('Restricted access');
class WYSIJA_help_campaigns extends WYSIJA_object{

	function __construct(){
	  parent::__construct();
	}

	function saveParameters($email_id, $key, $value)
	{
		// 1. get params field for given campaign
		$modelEmail = WYSIJA::get('email', 'model');
		$email = $modelEmail->getOne('params', array('email_id' => $email_id));
		$params = $email['params'];

		if(!is_array($params)) {
			$params = array();
		}

		// 2 update data for given key
		if(array_key_exists($key, $params)) {
			$params[$key] = $value;
		} else {
			$params = array_merge($params, array($key => $value));
		}

		// 3. update campaign
		return $modelEmail->update(array('params' => $params), array('email_id' => $email_id));
	}

	function getParameters($email_id, $key = null) {
		// 1. get params field for given campaign
		$modelEmail = WYSIJA::get('email', 'model');
		$email = $modelEmail->getOne('params', array('email_id' => $email_id));
		$params = $email['params'];

		if($key === null) {
			return $params;
		} else {
			if(!is_array($params) or array_key_exists($key, $params) === false) {
				return false;
			} else {
				return $params[$key];
			}
		}
	}
}