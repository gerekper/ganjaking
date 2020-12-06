<?php
defined( 'ABSPATH' ) or die( "No script kiddies please!" );

if( !class_exists( 'LoginPress_Facebook' ) ) {

  class LoginPress_Facebook {

    public function facebooklogin() {

      include_once LOGINPRESS_SOCIAL_DIR_PATH . 'classes/loginpress-utilities.php';
      $loginpress_utilities = new LoginPress_Social_Utilities;

      $request      = $_REQUEST;
      $site         = $loginpress_utilities->loginpress_site_url();
      $callBackUrl  = $loginpress_utilities->loginpress_callback_url();
      $response     = new stdClass();
      $lp_fb_user_details = new stdClass();
      $exploder     = explode( '_', $_GET['lpsl_login_id'] );
      $action       = $exploder[1];
      $width        = 150;
      $height       = 150;
      $_social_logins = get_option( 'loginpress_social_logins' );

      $config = array(
        'app_id'                  => $_social_logins['facebook_app_id'],
        'app_secret'              => $_social_logins['facebook_app_secret'],
        'default_graph_version'   => 'v2.9',
        'persistent_data_handler' => 'session'
      );

      include LOGINPRESS_SOCIAL_DIR_PATH . 'sdk/facebook/autoload.php';
      $fb = new Facebook\Facebook( $config );

      $encoded_url = isset( $_GET['redirect_to'] ) ? $_GET['redirect_to'] : '';
      if( isset( $encoded_url ) && $encoded_url != '' ) {
        $callback = $callBackUrl . 'lpsl_login_id' . '=facebook_check&redirect_to=' . $encoded_url;
      }
      else {
        $callback = $callBackUrl . 'lpsl_login_id' . '=facebook_check';
      }

      if( $action == 'login' ) {
        // Well looks like we are a fresh dude, login to Facebook!
        $helper = $fb->getRedirectLoginHelper();
        $permissions = array('email', 'public_profile'); // optional
        $loginUrl = $helper->getLoginUrl( $callback, $permissions );
        $loginpress_utilities->redirect( $loginUrl );
      } else {

        if( isset( $_REQUEST['error'] ) ) {
          $response->status = 'ERROR';
          $response->error_code = 2;
          $response->error_message = 'INVALID AUTHORIZATION';
          return $response;
          die();
        }

        if( isset( $_REQUEST['code'] ) ) {
          $helper = $fb->getRedirectLoginHelper();
          // Trick below will avoid "Cross-site request forgery validation failed. Required param "state" missing." from Facebook
          $_SESSION['FBRLH_state'] = $_REQUEST['state'];
          try {
            $accessToken = $helper->getAccessToken();
          }
          catch( Facebook\Exceptions\FacebookResponseException $e ) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
          }
          catch( Facebook\Exceptions\FacebookSDKException $e ) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
          }

          if( isset( $accessToken ) ) {
            // Logged in!
            $_SESSION['facebook_access_token'] = (string)$accessToken;
            $fb->setDefaultAccessToken( $accessToken );

            try {
              $response = $fb->get( '/me?fields=email,name, first_name, last_name, gender, link, about, birthday, education, hometown, is_verified, languages, location, website' );
              $userNode = $response->getGraphUser();
            }
            catch( Facebook\Exceptions\FacebookResponseException $e ) {
              // When Graph returns an error
              echo 'Graph returned an error: ' . $e->getMessage();
              exit;
            }
            catch( Facebook\Exceptions\FacebookSDKException $e ) {
              // When validation fails or other local issues
              echo 'Facebook SDK returned an error: ' . $e->getMessage();
              exit;
            }
            // get the user profile details
            $user_profile = $loginpress_utilities->loginpress_fetchGraphUser( $userNode, 'items' );

            if( $user_profile != null ) {

              $lp_fb_user_details->status     = 'SUCCESS';
              $lp_fb_user_details->deuid      = $user_profile['id'];
              $lp_fb_user_details->deutype    = 'facebook';
              $lp_fb_user_details->first_name = $user_profile['first_name'];
              $lp_fb_user_details->last_name  = $user_profile['last_name'];
              if(isset($user_profile['email']) || $user_profile['email'] != '') {

                $user_email = $user_profile['email'];
              } else {

                $user_email = $user_profile['id'].'@facebook.com';
              }
              $lp_fb_user_details->email      = $user_email;
              $lp_fb_user_details->username   = ($user_profile['first_name'] !='') ? strtolower( $user_profile['first_name'] ) : $user_email;
              $lp_fb_user_details->gender     = isset($user_profile['gender']) ? $user_profile['gender'] : 'N/A';
              $lp_fb_user_details->url        = $user_profile['link'];
              $lp_fb_user_details->about      = ''; //facebook doesn't return user about details.
              $headers = get_headers( 'https://graph.facebook.com/' . $user_profile['id'] . '/picture?width='.$width.'&height='.$height, 1 );
              // just a precaution, check whether the header isset...
              if( isset( $headers['Location'] ) ) {

                $lp_fb_user_details->deuimage = $headers['Location']; // string
              }
              else {

                $lp_fb_user_details->deuimage = false; // nothing there? .. weird, but okay!
              }
              $lp_fb_user_details->error_message = '';
            }
            else {

              $lp_fb_user_details->status         = 'ERROR';
              $lp_fb_user_details->error_code     = 2;
              $lp_fb_user_details->error_message  = 'INVALID AUTHORIZATION';
            }
          } // isset($accessToken).
        } else {
          // Well looks like we are a fresh dude, login to Facebook!
          $helper       = $fb->getRedirectLoginHelper();
          $permissions  = array( 'email', 'public_profile' ); // optional
          $loginUrl     = $helper->getLoginUrl( $callback, $permissions );
          $loginpress_utilities->redirect( $loginUrl );
        } // $_REQUEST['code'].
      } // else action = login.
      return $lp_fb_user_details;
    } // facebooklogin();
  }
}
?>
