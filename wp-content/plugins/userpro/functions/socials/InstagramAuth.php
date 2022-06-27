<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once(userpro_path . 'lib/instagram/vendor/autoload.php');

use MetzWeb\Instagram\Instagram;

class InstagramAuth
{
    protected $instagram;

    public function __construct()
    {
        $redirectURL = userpro_get_option('instagram_redirect_url');
        //Validate Redirect URL
        $redirectURL = up_valid_url($redirectURL);
        $this->instagram = new Instagram(array(
            'apiKey' => userpro_get_option('instagram_app_key'),
            'apiSecret' => userpro_get_option('instagram_Secret_Key'),
            'apiCallback' => $redirectURL
        ));
    }

    public function authUrl()
    {
        $url = $this->instagram->getLoginUrl();
        $url = urldecode($url);
        return $url;
    }

    public function login()
    {
        try {
            $user_info = $this->instagram->getOAuthToken($_GET['code']);
            $user['id'] = $user_info->user->id;
            $user['full_name'] = $user_info->user->full_name;
            $user['username'] = $user_info->user->username;
            $user['profile_picture'] = $user_info->user->profile_picture;
            // login , check if user exist with same email
            $users = get_users([
                'meta_key' => 'userpro_instagram_id',
                'meta_value' => $user['id'],
                'meta_compare' => '=',
            ]);
            if (!empty($users)) {

                userpro_auto_login($users[0]->user_login, true, '', 'social');
            } else {
                $api = new userpro_api();
                $user_pass = wp_generate_password($length = 12, $include_standard_special_chars = false);
//				Check if user login exist
                if ($api->display_name_exists($user['username'])) {
                    $user['username'] = $api->unique_display_name($user['username']);
                }
                $api->new_user($user['username'], $user_pass, '', $user, $type = 'instagram');
                userpro_auto_login($user['username'], true, '', 'social');
            }
        } catch (Exception $e) {
            up_error('UserPro error instagramAuth ( instagramUser function )' . $e);
            die();
        }
    }
}