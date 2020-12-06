<?php
defined( 'ABSPATH' ) or die( "No script kiddies please!" );

if( !class_exists( 'LoginPress_Social_Utilities' ) ) {

  class LoginPress_Social_Utilities {

    /**
    * loginpress_site_url
    *
    * @since 1.0.0
    * @return site URL
    */
    function loginpress_site_url() {
      return site_url();
    }

    /**
    * loginpress_callback_url
    *
    * @since 1.0.0
    * @return callback URL
    */
    function loginpress_callback_url() {

      $url = wp_login_url();
      if( strpos( $url, '?' ) === false ) {
        $url.= '?';
      }
      else {
        $url.= '&';
      }
      return $url;
    }

    /**
    * Set header location.
    *
    * @since 1.0.0
    */
    function redirect( $redirect ) {
      if( headers_sent() ) {
        // Use JavaScript to redirect if content has been previously sent.
        echo '<script language="JavaScript" type="text/javascript">window.location=\'';
        echo $redirect;
        echo '\';</script>';
      }
      else { // Default Header Redirect.
        header( 'Location: ' . $redirect );
      }
      exit;
    }



    /**
    * function to access the protected object properties.
    *
    * @param $object and $property.
    * @since 1.0.0
    * @return $object value.
    */
    function loginpress_fetchGraphUser( $object, $property ) {

      // Using ReflectionClass that repots information about class.
      $reflection = new ReflectionClass( $object );
      // Gets a ReflectionProperty for a class property.
      $getproperty = $reflection->getProperty( $property );
      // Set method accessibility.
      $getproperty->setAccessible( true );
      // Return the property value w.r.t object.
      return $getproperty->getValue( $object );
    }

    /**
    * function to insert the user data into plugin's custom table.
    *
    * @param $user_id and $object.
    * @since 1.0.0
    */
    static function link_user( $user_id, $object ) {
      global $wpdb;
      $sha_verifier = sha1( $object->deutype.$object->deuid );
      $table_name   = "{$wpdb->prefix}loginpress_social_login_details";

      $first_name   = sanitize_text_field( $object->first_name );
      $last_name    = sanitize_text_field( $object->last_name );
      $profile_url  = sanitize_text_field( $object->url );
      $photo_url    = sanitize_text_field( $object->deuimage );
      $display_name = sanitize_text_field( $object->first_name . ' ' . $object->last_name );
      $description  = sanitize_text_field( $object->about );

      $submit_array = array(
        "user_id"        => $user_id,
        "provider_name"  => $object->deutype,
        "identifier"     => $object->deuid,
        "sha_verifier"   => $sha_verifier,
        "email"          => $object->email,
        "first_name"     => $first_name,
        "last_name"      => $last_name,
        "profile_url"    => $profile_url,
        "photo_url"      => $photo_url,
        "display_name"   => $display_name,
        "description"    => $description,
        "gender"         => $object->gender
      );

      $wpdb->insert( $table_name, $submit_array );
      if( ! $object ) {
        echo "Data insertion failed";
      }
    }

    /**
     * Redirect user after successfully login.
     * @param  obj $user
     * @param array $social_channel
     * @since 1.0.0
     * @version 1.3.0
     */
    function _home_url( $user, $social_channel = '' ){

      $user_id = $user->ID;
      if( ! $this->set_cookies( $user_id ) ) {
        return false;
      }

      if( isset( $_COOKIE['lg_redirect_to'] ) ) {
        $redirect = $_COOKIE['lg_redirect_to'];
        setcookie( 'lg_redirect_to', '', time() - 3600 );
      } elseif ( ! wp_get_referer() ) {
        $redirect = site_url();
      } elseif ( ! strpos( wp_get_referer(), 'wp-login.php' ) ) {
        $redirect = wp_get_referer();
      } else {
        $redirect = admin_url();
      }

      if ( class_exists( 'LoginPress_Set_Login_Redirect' ) && do_action( 'loginpress_redirect_autologin', $user ) ) {
				$user_login_url = do_action( 'loginpress_redirect_autologin', $user );
			} else {
        $user_login_url = apply_filters( 'login_redirect', $redirect, site_url(), wp_signon() );
      }

      /**
       * Login filter for social logins
       * @since 1.3.0
       */
      $login_filter = apply_filters( 'loginpress_social_login_redirect', false );

      if ( ! empty( $social_channel ) && is_array( $login_filter )  ) {
        switch ( $social_channel ) {
          case 'google_login':
            $social_redirect = $login_filter['google_login'];
            break;

          case 'facebook_login':
            $social_redirect = $login_filter['facebook_login'];
            break;

          case 'twitter_login':
            $social_redirect = $login_filter['twitter_login'];
            break;

          case 'linkedin_login':
            $social_redirect = $login_filter['linkedin_login'];
            break;
        }

        wp_redirect( esc_url( $social_redirect ) );
        exit();
      }

      wp_safe_redirect( $user_login_url );
      exit();
    }

    function set_cookies( $user_id = 0, $remember = true ) {
      if( ! function_exists( 'wp_set_auth_cookie' ) ) {
        return false;
      }
      if( ! $user_id ) {
        return false;
      }
      $user = get_user_by('id',  (int)$user_id) ;
      wp_clear_auth_cookie();
      wp_set_auth_cookie( $user_id, $remember );
      wp_set_current_user( $user_id );
      do_action( 'wp_login', $user->user_login, $user );
      return true;
    }


    function register_user( $user_name, $user_email ){
      $username = self:: get_username( $user_name );
      $random_password = wp_generate_password( 12, true, false );
      $user_id = wp_create_user( $username, $random_password, $user_email );

      return $user_id;
    }

    static function get_username( $user_login ) {

      if( username_exists( $user_login ) ) :

  			$i = 1;
  			$user_ID = $user_login;

  			do {
          $user_ID = $user_login . "_" . $i++;
        }
  			while( username_exists($user_ID) );

  			$user_login = $user_ID;
  		endif;

      return $user_login;
    }


    static function update_usermeta( $user_id, $object, $role ) {

      $meta_key = array( 'email', 'first_name', 'last_name', 'deuid', 'deutype', 'deuimage', 'description', 'sex' );
      $_object  = array( $object->email, $object->first_name, $object->last_name, $object->deuid, $object->deutype, $object->deuimage, $object->about, $object->gender );

      $i = 0;
      while ( $i < 8 ) :
        update_user_meta( $user_id, $meta_key[$i], $_object[$i] );
        $i++;
      endwhile;

      wp_update_user( array(
        'ID'            => $user_id,
        'display_name'  => $object->first_name . ' ' . $object->last_name,
        'role'          => $role,
        'user_url'      => $object->url
      ) );

      self::link_user( $user_id, $object );
    }

    /**
    * Show GPlus error.
    *
    * @since 1.0.0
    */
    static function gplus_login_error( $user, $username, $password ) {
      $WP_Error = new WP_Error();
      $WP_Error->add( 'gplus_login', sprintf(  __( '%1$sERROR%2$s: Invalid `Client ID` or `Client Secret` combination?', 'loginpress-social-login' ), '<strong>', '</strong>' ) );
      return $WP_Error;
    }

  }
}
?>
