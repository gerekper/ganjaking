<?php
defined( 'ABSPATH' ) or die( "No script kiddies please!" );

if( !class_exists( 'LoginPress_Social_Login_Check' ) ) {

  class LoginPress_Social_Login_Check {
    //constructor
    function __construct() {
      $this->set_redirect_to();
      $this->loginpress_check();
      // echo "Request ";
      // var_dump($_REQUEST);
      // echo "<br />session ";
      // var_dump(get_option('loginpress_twitter_oauth'));
      $lp_twitter_oauth = get_option('loginpress_twitter_oauth');
      if ( isset( $lp_twitter_oauth["oauth_token"] ) && isset( $_REQUEST['oauth_verifier'] ) ) {

        $this->onTwitterLogin();
      }

    }

    /**
     * Set Cookie for the `redirect_to` args
     * @since 1.3.0
     */
    function set_redirect_to(){

      if( isset( $_REQUEST['redirect_to'] ) ) {
        setcookie("lg_redirect_to", $_REQUEST['redirect_to'], time() + (60 * 20)); // 60 seconds ( 1 minute) * 20 = 20 minutes
      }
    }
    function loginpress_check() {
      if( isset( $_GET['lpsl_login_id'] ) ) {
        $exploder = explode( '_', $_GET['lpsl_login_id'] );

        if ( 'facebook' == $exploder[0] ) {
          if( version_compare( PHP_VERSION, '5.4.0', '<' ) ) {
            _e( 'The Facebook SDK requires PHP version 5.4 or higher. Please notify about this error to site admin.', 'loginpress-social-login' );
            die();
          }
          $this->onFacebookLogin();
        } elseif ( 'twitter' == $exploder[0] ) {
          $this->onTwitterLogin();
        } elseif ( 'gplus' == $exploder[0] ) {
          $this->onGPlusLogin();
        } elseif ( 'linkedin' == $exploder[0]  ) {
          $this->onLinkedIdLogin();
        }

      }
    }

    /**
    * Login with LinkedIn Account.
    * @since 1.0.0
    * @version 1.0.9
    */
    public function onLinkedIdLogin() {

      $_settings    = get_option('loginpress_social_logins');
      $clientId     = $_settings['linkedin_client_id'];      // LinkedIn client ID.
      $clientSecret = $_settings['linkedin_client_secret']; // LinkedIn client secret.
      $redirectURL  = $_settings['linkedin_redirect_uri']; // Callback URL.

      if ( ! isset( $_GET['code'] ) ) {

        wp_redirect( "https://www.linkedin.com/oauth/v2/authorization?response_type=code&client_id={$clientId}&redirect_uri={$redirectURL}&state=987654321&scope=r_liteprofile%20r_emailaddress" );
      } else {

        $get_access_token = wp_remote_post( 'https://www.linkedin.com/oauth/v2/accessToken', array(
          'body' => array(
            'grant_type'    => 'authorization_code',
            'code'          => $_GET['code'],
            'redirect_uri'  => $redirectURL,
            'client_id'     => $clientId,
            'client_secret' => $clientSecret
          ) ) );

          $_access_token = json_decode(  $get_access_token['body'] )->access_token;

          if ( ! $_access_token ) {
            $user_login_url = apply_filters( 'login_redirect', admin_url(), site_url(), wp_signon() );
            wp_safe_redirect( $user_login_url );
          }

          $get_user_details = wp_remote_get( 'https://api.linkedin.com/v2/me?projection=(id,firstName,lastName,profilePicture(displayImage~:playableStreams))', array(
            'method' => 'GET',
            'timeout' => 15,
            'headers' => array( 'Authorization' => "Bearer " . $_access_token ),
          ) );

          $get_user_email = wp_remote_get( 'https://api.linkedin.com/v2/emailAddress?q=members&projection=(elements*(handle~))', array(
            'method' => 'GET',
            'timeout' => 15,
            'headers' => array('Authorization' => "Bearer " . $_access_token ),
          ) );

          if( ! is_wp_error($get_user_details) && isset($get_user_details['response']['code']) && 200 === $get_user_details['response']['code'] && ! is_wp_error($get_user_email) && isset($get_user_email['response']['code']) && 200 === $get_user_email['response']['code'] ) {

            $lightDetailBody = json_decode(wp_remote_retrieve_body($get_user_details));
            $emailBody       = json_decode(wp_remote_retrieve_body($get_user_email));

            if( is_object($lightDetailBody) && isset($lightDetailBody->id) && $lightDetailBody->id && is_object($emailBody) && isset($emailBody->elements) ) {
              $lightDetailBody = json_decode(json_encode($lightDetailBody), true);
              $emailBody       = json_decode(json_encode($emailBody), true);
              $firstName       = isset($lightDetailBody['firstName']) && isset($lightDetailBody['firstName']['localized']) && isset($lightDetailBody['firstName']['preferredLocale']) && isset($lightDetailBody['firstName']['preferredLocale']['language']) && isset($lightDetailBody['firstName']['preferredLocale']['country']) ? $lightDetailBody['firstName']['localized'][$lightDetailBody['firstName']['preferredLocale']['language'] . '_' . $lightDetailBody['firstName']['preferredLocale']['country']] : '';
              $lastName        = isset($lightDetailBody['lastName']) && isset($lightDetailBody['lastName']['localized']) && isset($lightDetailBody['lastName']['preferredLocale']) && isset($lightDetailBody['lastName']['preferredLocale']['language']) && isset($lightDetailBody['lastName']['preferredLocale']['country']) ? $lightDetailBody['lastName']['localized'][$lightDetailBody['lastName']['preferredLocale']['language'] . '_' . $lightDetailBody['lastName']['preferredLocale']['country']] : '';
              $smallAvatar     = isset($lightDetailBody['profilePicture']) && isset($lightDetailBody['profilePicture']['displayImage~']) && isset($lightDetailBody['profilePicture']['displayImage~']['elements']) && is_array($lightDetailBody['profilePicture']['displayImage~']['elements']) && isset($lightDetailBody['profilePicture']['displayImage~']['elements'][0]['identifiers']) && is_array($lightDetailBody['profilePicture']['displayImage~']['elements'][0]['identifiers'][0]) && isset($lightDetailBody['profilePicture']['displayImage~']['elements'][0]['identifiers'][0]['identifier']) ? $lightDetailBody['profilePicture']['displayImage~']['elements'][0]['identifiers'][0]['identifier'] : '';
              $largeAvatar     = isset($lightDetailBody['profilePicture']) && isset($lightDetailBody['profilePicture']['displayImage~']) && isset($lightDetailBody['profilePicture']['displayImage~']['elements']) && is_array($lightDetailBody['profilePicture']['displayImage~']['elements']) && isset($lightDetailBody['profilePicture']['displayImage~']['elements'][3]['identifiers']) && is_array($lightDetailBody['profilePicture']['displayImage~']['elements'][3]['identifiers'][0]) && isset($lightDetailBody['profilePicture']['displayImage~']['elements'][3]['identifiers'][0]['identifier']) ? $lightDetailBody['profilePicture']['displayImage~']['elements'][3]['identifiers'][0]['identifier'] : '';
              $emailAddress    = isset($emailBody['elements']) && is_array($emailBody['elements']) && isset($emailBody['elements'][0]['handle~']) && isset($emailBody['elements'][0]['handle~']['emailAddress']) ? $emailBody['elements'][0]['handle~']['emailAddress'] : '';
            }
          }

          include_once LOGINPRESS_SOCIAL_DIR_PATH . 'classes/loginpress-utilities.php';
          $loginpress_utilities = new LoginPress_Social_Utilities;

          $result    = new stdClass();

          $result->status        = 'SUCCESS';
          $result->deuid         = $lightDetailBody['id'];
          $result->deutype       = 'linkedin';
          $result->first_name    = $firstName;
          $result->last_name     = $lastName;
          $result->email         = $emailAddress != '' ? $emailAddress : $lightDetailBody['id'] . '@linkedin.com' ;
          $result->username      = strtolower( $firstName . '_' . $lastName );
          $result->gender        = 'N/A';
          $result->url           = '';
          $result->about         = ''; // LinkedIn doesn't return user about details.
          $result->deuimage      = $largeAvatar;
          $result->error_message = '';

          global $wpdb;
          $sha_verifier = sha1( $result->deutype.$result->deuid );
          $identifier   = $lightDetailBody['id'];
          $sql =  $wpdb->prepare(  "SELECT * FROM `{$wpdb->prefix}loginpress_social_login_details` WHERE `provider_name` LIKE %s AND `identifier` LIKE %d AND `sha_verifier` LIKE %s", $result->deutype, $result->deuid, $sha_verifier );
          $row = $wpdb->get_results( $sql );

          $user_object = get_user_by( 'email', $result->email );
          if( ! $row ) {
            //check if there is already a user with the email address provided from social login already
            if( $user_object != false ) {
              //user already there so log him in
              $id  = $user_object->ID;
              $sql = $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}loginpress_social_login_details` WHERE `user_id` LIKE %d", $id );
              $row = $wpdb->get_results($sql);

              if( ! $row ){
                $loginpress_utilities->link_user( $id, $result );
              }
              $loginpress_utilities->_home_url( $user_object ); // v1.0.7
              // add_filter( 'login_redirect', array($this,'my_login_redirect'), 10, 3 );
              die();
            }

            $loginpress_utilities->register_user( $result->username, $result->email );
            $user_object  = get_user_by( 'email', $result->email );
            $id           = $user_object->ID;
            $role         = get_option( 'default_role' ); // v1.0.9
            $loginpress_utilities->update_usermeta( $id, $result, $role );
            // add_filter( 'login_redirect', array($this,'my_login_redirect'), 10, 3 );
            $loginpress_utilities->_home_url( $user_object ); // v1.0.7
            exit();
          } else {

            if( ( $row[0]->provider_name == $result->deutype ) && ( $row[0]->identifier == $result->deuid ) ) {
              //echo "user found in our database";
              $user_object  = get_user_by( 'email', $result->email );
              $id           = $user_object->ID;
              $loginpress_utilities->_home_url( $user_object ); // v1.0.7

              exit();
            } else {
              // user not found in our database
              // need to handle an exception
            }
          }

        }

      }

      /**
      * Login with Google Account.
      * @since 1.0.0
      * @version 1.4.0
      */
      public function onGPlusLogin() {
        include_once LOGINPRESS_SOCIAL_DIR_PATH . 'sdk/google-client/vendor/autoload.php';
        include_once LOGINPRESS_SOCIAL_DIR_PATH . 'sdk/google-client/vendor/google/apiclient/src/Client.php';
        include_once LOGINPRESS_SOCIAL_DIR_PATH . 'sdk/google-client/vendor/google/apiclient-services/src/Oauth2.php';

        $_settings    = get_option('loginpress_social_logins');
        $clientId     = $_settings['gplus_client_id']; //Google client ID
        $clientSecret = $_settings['gplus_client_secret']; //Google client secret
        $redirectURL  = $_settings['gplus_redirect_uri']; //Callback URL
		$gClient      = new Google_Client();
        $gClient->setApplicationName( 'LoginPress Social Login' );
        $gClient->setClientId( $clientId );
        $gClient->setClientSecret( $clientSecret );
        $gClient->setRedirectUri( $redirectURL );
        $gClient->addScope('profile email openid');

        $google_oauthV2 = new Google_Service_Oauth2( $gClient );

        include_once LOGINPRESS_SOCIAL_DIR_PATH . 'classes/loginpress-utilities.php';

        $loginpress_utilities = new LoginPress_Social_Utilities;

        if ( ! isset( $_GET['code'] ) ) {
          wp_redirect( $gClient->createAuthUrl() );
        }
        else {
          //Getting and settings access token for user
          try {
            $args = array(
              'body' => array(
                'code'          =>  $_GET['code'],
                'client_id'     =>  $clientId,
                'client_secret' =>  $clientSecret,
                'redirect_uri'  =>  $redirectURL,
                'grant_type'    => 'authorization_code',
              ),
            );
            $response = wp_remote_post( 'https://www.googleapis.com/oauth2/v4/token', $args );
            $body = json_decode( $response['body'] );
            if ( '' != $body->access_token ) {
              $this->access_token = $body->access_token;
              $gClient->setAccessToken($this->access_token);
            }

             $gClient->authenticate($_GET['code']);
          } catch (Exception $e) {
            add_filter( 'authenticate', array( 'LoginPress_Social_Utilities', 'gplus_login_error' ), 40, 3 );
          }

          if ( $gClient->getAccessToken() ) {

            $gpUserProfile = $google_oauthV2->userinfo->get();

            $result        = new stdClass();

            $result->status     = 'SUCCESS';
            $result->deuid      = $gpUserProfile['id'];
            $result->deutype    = 'glpus';
            $result->first_name = $gpUserProfile['given_name'];
            $result->last_name  = $gpUserProfile['family_name'];
            $result->email      = $gpUserProfile['email'];
            $result->username   = ( $gpUserProfile['given_name'] !='' ) ? strtolower( $gpUserProfile['given_name'] ) : $gpUserProfile['email'];
            $result->gender     = isset( $gpUserProfile['gender'] ) ? $gpUserProfile['gender'] : '';
            $result->url        = isset( $gpUserProfile['link'] ) ? $gpUserProfile['link'] : '';
            $result->about      = ''; //gplus doesn't return user about details.
            $result->deuimage   = $gpUserProfile['picture'];


            global $wpdb;
            $sha_verifier = sha1( $result->deutype.$result->deuid );
            $identifier   = $gpUserProfile['id'];
            $sql = $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}loginpress_social_login_details` WHERE `provider_name` LIKE %s AND `identifier` LIKE %d AND `sha_verifier` LIKE %s", $result->deutype, $result->deuid, $sha_verifier );
            $row = $wpdb->get_results( $sql );

            $user_object = get_user_by( 'email', $gpUserProfile['email'] );
            if( ! $row ) {
              //check if there is already a user with the email address provided from social login already
              if( $user_object != false ) {
                //user already there so log him in
                $id  = $user_object->ID;
                $sql = $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}loginpress_social_login_details` WHERE `user_id` LIKE %d", $id );
                $row = $wpdb->get_results($sql);

                if( ! $row ){
                  $loginpress_utilities->link_user( $id, $result );
                }
                $loginpress_utilities->_home_url( $user_object, 'google_login'  ); // v1.0.7
                // add_filter( 'login_redirect', array($this,'my_login_redirect'), 10, 3 );
                die();
              }

              $loginpress_utilities->register_user( $result->username, $result->email );
              $user_object  = get_user_by( 'email', $result->email );
              $id           = $user_object->ID;
              $role         = get_option( 'default_role' ); // v1.0.9
              $loginpress_utilities->update_usermeta( $id, $result, $role );
              // add_filter( 'login_redirect', array($this,'my_login_redirect'), 10, 3 );
              $loginpress_utilities->_home_url( $user_object, 'google_login' ); // v1.0.7
              exit();
            } else {

              if( ( $row[0]->provider_name == $result->deutype ) && ( $row[0]->identifier == $result->deuid ) ) {
                //echo "user found in our database";
                $user_object  = get_user_by( 'email', $result->email );
                $id           = $user_object->ID;
                $loginpress_utilities->_home_url( $user_object, 'google_login' ); // v1.0.7

                exit();
              } else {
                // user not found in our database
                // need to handle an exception
              }
            }
          }

        }
      }

      /**
      * Login with Facebook Account.
      * @since 1.0.0
      * @version 1.0.9
      */
      public function onFacebookLogin() {

        include_once LOGINPRESS_SOCIAL_DIR_PATH . 'classes/loginpress-facebook.php';
        include_once LOGINPRESS_SOCIAL_DIR_PATH . 'classes/loginpress-utilities.php';
        $response_class       = new stdClass();
        $facebook_login       = new LoginPress_Facebook;
        $loginpress_utilities = new LoginPress_Social_Utilities;
        $result               = $facebook_login->facebookLogin( $response_class );

        if( isset( $result->status ) && $result->status == 'SUCCESS' ) {

          global $wpdb;
          $sha_verifier = sha1( $result->deutype.$result->deuid );
          $sql = $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}loginpress_social_login_details` WHERE `provider_name` LIKE %s AND `identifier` LIKE %d AND `sha_verifier` LIKE %s", $result->deutype, $result->deuid, $sha_verifier );
          $row = $wpdb->get_results( $sql );

			if ( ! isset( $row[0]->email ) && $result->email === $result->deuid . '@facebook.com' ) {
				$result->email = $result->email;

			} else if (  $result->email === $result->deuid . '@facebook.com' ) {
				$result->email = $row[0]->email;
			}
          $user_object = get_user_by( 'email', $result->email );

          if( ! $row ) {
            //check if there is already a user with the email address provided from social login already
            if( $user_object != false ){
              //user already there so log him in
              $id  = $user_object->ID;
              $sql = $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}loginpress_social_login_details` WHERE `user_id` LIKE %d", $id );
              $row = $wpdb->get_results($sql);
              if( ! $row ){
                $loginpress_utilities->link_user( $id, $result );
              }
              $loginpress_utilities->_home_url( $user_object ); // v1.0.7
              // add_filter( 'login_redirect', array($this,'my_login_redirect'), 10, 3 );
              die();
            }

            $loginpress_utilities->register_user( $result->username, $result->email );
            $user_object  = get_user_by( 'email', $result->email );
            $id           = $user_object->ID;
            $role         = get_option( 'default_role' ); // v1.0.9
            $loginpress_utilities->update_usermeta( $id, $result, $role );
            // add_filter( 'login_redirect', array($this,'my_login_redirect'), 10, 3 );
            $loginpress_utilities->_home_url( $user_object ); // v1.0.7
            exit();
          } else {
            if( ( $row[0]->provider_name == $result->deutype ) && ( $row[0]->identifier == $result->deuid ) ) {
              //echo "user found in our database";
              $user_object  = get_user_by( 'email', $result->email );
              $id           = $user_object->ID;
              $loginpress_utilities->_home_url( $user_object ); // v1.0.7

              exit();
            } else {
              // user not found in our database
              // need to handle an exception
            }
          }
        } else {
          if( isset( $_REQUEST['error'] ) ) {


            $redirect_url = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : site_url();
            $loginpress_utilities->redirect( $redirect_url );
          }
          die();
        }

      } //!onFacebookLogin()

      /**
      * Login with Twitter Account.
      * @since 1.0.0
      * @version 1.0.9
      */
      public function onTwitterLogin() {

        include_once LOGINPRESS_SOCIAL_DIR_PATH . 'classes/loginpress-twitter.php';
        include_once LOGINPRESS_SOCIAL_DIR_PATH . 'classes/loginpress-utilities.php';

        $response_class       = new stdClass();
        $twitter_login        = new LoginPress_Twitter;
        $loginpress_utilities = new LoginPress_Social_Utilities;
        $result               = $twitter_login->twitterLogin( $response_class );


        if( isset( $result->status ) && $result->status == 'SUCCESS' ) {
          global $wpdb;
          $sha_verifier = sha1( $result->deutype.$result->deuid );
          $sql = $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}loginpress_social_login_details` WHERE `provider_name` LIKE %s AND `identifier` LIKE %d AND `sha_verifier` LIKE %s", $result->deutype, $result->deuid, $sha_verifier );
          $row = $wpdb->get_results( $sql );

          if( ! $row ) {
            //check if there is already a user with the email address provided from social login already
            $user_object = get_user_by( 'email', $result->email );

            if( $user_object != false ){
              //user already there so log him in
              $id  = $user_object->ID;
              $sql = $wpdb->prepare( "SELECT * FROM `{$wpdb->prefix}loginpress_social_login_details` WHERE `user_id` LIKE %d", $id );
              $row = $wpdb->get_results($sql);

              // var_dump($row);
              if( ! $row ) {
                $loginpress_utilities->link_user( $id, $result );
              }
              $loginpress_utilities->_home_url( $user_object ); // v1.0.7
              die();
            }

            $loginpress_utilities->register_user( $result->username, $result->email );
            $user_object  = get_user_by( 'email', $result->email );
            $id           = $user_object->ID;
            $role         = get_option( 'default_role' ); // v1.0.9
            $loginpress_utilities->update_usermeta( $id, $result, $role );
            $loginpress_utilities->_home_url( $user_object ); // v1.0.7
            exit();
          } else {

            if( ( $row[0]->provider_name == $result->deutype ) && ( $row[0]->identifier == $result->deuid ) ){
              //echo "user found in our database";
              $user_object  = get_user_by( 'email', $result->email );
              $id           = $user_object->ID;
              $loginpress_utilities->_home_url( $user_object ); // v1.0.7
              exit();
            } else {
              // user not found in our database
              // need to handle an exception
            }
          }

        } else {
          if ( isset( $_REQUEST['denied'] ) ) {
            $redirect_url = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : site_url();
            $loginpress_utilities->redirect( $redirect_url );
          }
          die();
        }
      }

    }
  }
  $lpsl_login_check = new LoginPress_Social_Login_Check();
