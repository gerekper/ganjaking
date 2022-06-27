<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once(userpro_path . 'lib/google-auth/vendor/autoload.php');

use League\OAuth2\Client\Provider\Google;

class GoogleAuth
{

    protected $google;

    public function __construct()
    {

        $redirectURL = userpro_get_option('google_redirect_uri');
        //Validate Redirect URL
        $redirectURL = up_valid_url($redirectURL);

        $this->google = new Google(
            [
                'clientId' => userpro_get_option('google_client_id'),
                'clientSecret' => userpro_get_option('google_client_secret'),
                'redirectUri' => $redirectURL,
            ]
        );
    }

    public function login()
    {

        if (!empty($_GET['error'])) {

            // Got an error, probably user denied access
            exit('Got error: ' . htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'));
        } elseif (empty($_GET['code'])) {

            $_SESSION['googleoauth2state'] = $this->google->getState();
        } else {
            // Try to get an access token (using the authorization code grant)
            $token = $this->google->getAccessToken('authorization_code', [
                'code' => $_GET['code'],
            ]);
        }
        // Optional: Now you have a token you can look up a users profile data
        try {
//         Get google user information
            $ownerDetails = $this->google->getResourceOwner($token);

            $user_info['id'] = $ownerDetails->getId();
            $user_info['first_name'] = $ownerDetails->getFirstName();
            $user_info['last_name'] = $ownerDetails->getLastName();
            $user_info['email'] = !empty($ownerDetails->getEmail()) ? $ownerDetails->getEmail() : '';
            $user_info['image']['url'] = $ownerDetails->getAvatar();
            $user_info['user_login'] = $user_info['first_name'] . '_' . $user_info['last_name'];
//				Replace default avatar size
            $user_info['image']['url'] = str_replace('sz=50', 'sz=400', $user_info['image']['url']);

//				Check if user exist , if exist -  login  if no - register
            $profile_exist = social_profile_check($user_info['email'], $user_info['id'], 'google');

            if ($profile_exist == false) {


                $api = new userpro_api();

                $user_pass = wp_generate_password($length = 12, $include_standard_special_chars = false);

//				Check if user login exist
                if ($api->display_name_exists($user_info['user_login'])) {
                    $user_info['user_login'] = $api->unique_display_name($user_info['user_login']);
                }

                $api->new_user($user_info['user_login'], $user_pass, $user_info['email'], $user_info, $type = 'google');
                userpro_auto_login($user_info['user_login'], true, '', 'social');
            } else {
//				userpro_update_profile_via_google($profile_exist, $user_info);
                userpro_auto_login($profile_exist, true, '', 'social');
            }
        } catch (Exception $e) {
            // Failed to get user details
            up_error('Google auth something went wrong: ' . $e->getMessage());

            exit;
        }
    }

    public function authUrl()
    {

        // If we don't have an authorization code then get one
        $authUrl = $this->google->getAuthorizationUrl();

        return $authUrl;
    }
}