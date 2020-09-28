<?php

if(session_status() == PHP_SESSION_NONE) {
	session_start();
}

require_once(userpro_path . 'lib/linkedin/vendor/autoload.php');

use LinkedIn\Client;
use LinkedIn\Scope;
use LinkedIn\AccessToken;


class LinkedinAuth
{

	protected $linkedin;


	public function __construct()
	{

		$cliendId = userpro_get_option('linkedin_app_key');
		$clientSecret =   userpro_get_option('linkedin_Secret_Key');
		$redirect_url = userpro_get_option('linkedin_redirect_url');
//		Validate redirect url from WP backend.
		$redirect_url = up_valid_url($redirect_url);

		$this->linkedin = new Client($cliendId,$clientSecret);
			$this->linkedin->setRedirectUrl($redirect_url);
	}

    public function authUrl()
    {

        $scopes = [
            Scope::READ_BASIC_PROFILE,
            Scope::READ_EMAIL_ADDRESS,
        ];

        $loginUrl = $this->linkedin->getLoginUrl($scopes); // get url on LinkedIn to start linking

        if (!isset($loginUrl))
            throw new Exception('Linkedin auth url is empty, please check your api');

        return $loginUrl;
    }


	public function login($code)
	{

		$this->linkedin->getAccessToken($code);

		$user_info = $this->linkedin->get(
			'me'
		);
		$emailAddress = $this->linkedin->get(
			'emailAddress?q=members&projection=(elements*(handle~))'
		);
		$emailAddress = $emailAddress['elements'][0]['handle~']['emailAddress'];
		
		$profile_exist = social_profile_check($emailAddress, $user_info['id'], 'linkedin');

		if($profile_exist == FALSE) {
				$api        = new userpro_api();
				$user_login = $user_info['firstName']['localized']['en_US'] . '_' . $user_info['lastName']['localized']['en_US'];
				$user_pass  = wp_generate_password($length = 12, $include_standard_special_chars = FALSE);
				$user_email = isset($emailAddress) ? $emailAddress : '';

				if($api->display_name_exists($user_login)) {
					$user_login = $api->unique_display_name($user_login);
				}
				$api->new_user($user_login, $user_pass, $user_email, $user_info, $type = 'linkedin');

				userpro_auto_login($user_login, TRUE, '', 'social');

		}else{

			if(!is_user_logged_in())
				userpro_auto_login($profile_exist, TRUE, '', 'social');

            throw new Exception('You are already logged in');

		}
	}

}