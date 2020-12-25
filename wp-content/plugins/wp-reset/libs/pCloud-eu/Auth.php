<?php

namespace pCloud;

class Auth {

	public static function getAuth($credentialPath) {
        global $wp_reset;
		$options = $wp_reset->get_options();
        if(!array_key_exists('pcloudeu', $options['cloud_data'])){
            return false;
        }
		$credential['access_token'] = json_decode($options['cloud_data']['pcloudeu']['token']);
       
        if (!isset($credential["access_token"]) || empty($credential["access_token"])) {
			throw new Exception("Couldn't find \"access_token\"");			
		}

		return $credential;
	}
}