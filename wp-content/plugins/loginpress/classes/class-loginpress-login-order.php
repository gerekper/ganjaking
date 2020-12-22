<?php

/**
* LoginPress_Login_Order.
*
* @description Enable user to login using their username and/or email address.
* @since 1.0.18
*/

if ( ! class_exists( 'LoginPress_Login_Order' ) ) :

	class LoginPress_Login_Order {

		/**
	  * Variable that Check for LoginPress Key.
	  * @access public
	  * @var string
	  */
	  public $loginpress_key;

		/* * * * * * * * * *
    * Class constructor
    * * * * * * * * * */
    public function __construct() {

			$this->loginpress_key = get_option( 'loginpress_customization' );
      $this->_hooks();
    }

		public function _hooks(){

			$wp_version = get_bloginfo( 'version' );
			$loginpress_setting = get_option( 'loginpress_setting' );
			$login_order = isset(	$loginpress_setting['login_order'] ) ? $loginpress_setting['login_order'] : '';

			remove_filter( 'authenticate', 	'wp_authenticate_username_password', 20, 3 );
			add_filter( 'authenticate', array( $this, 'loginpress_login_order' ), 20, 3 );

			if ( 'username' == $login_order && '4.5.0' < $wp_version ) {
		 		// For WP 4.5.0 remove email authentication.
				remove_filter( 'authenticate', 'wp_authenticate_email_password', 20 );
			}
		}

		/**
		* If an email address is entered in the username field, then look up the matching username and authenticate as per normal, using that.
		*
		* @param string $user
		* @param string $username
		* @param string $password
		* @since 1.0.18
		* @version 1.0.22
		* @return Results of autheticating via wp_authenticate_username_password(), using the username found when looking up via email.
		*/
		function loginpress_login_order( $user, $username, $password ) {

			if ( $user instanceof WP_User ) {
				return $user;
			}

			// Is username or password field empty?
			if ( empty( $username ) || empty( $password ) ) {

				if ( is_wp_error( $user ) )
					return $user;

				$error = new WP_Error();

				$empty_username	= isset( $this->loginpress_key['empty_username'] ) && ! empty( $this->loginpress_key['empty_username'] ) ? $this->loginpress_key['empty_username'] : sprintf( __( '%1$sError:%2$s The username field is empty.', 'loginpress' ), '<strong>', '</strong>' );

	      $empty_password	= isset( $this->loginpress_key['empty_password'] ) && ! empty( $this->loginpress_key['empty_password'] ) ? $this->loginpress_key['empty_password'] : sprintf( __( '%1$sError:%2$s The password field is empty.', 'loginpress' ), '<strong>', '</strong>' );

				if ( empty( $username ) )
					$error->add( 'empty_username', $empty_username );

				if ( empty( $password ) )
					$error->add( 'empty_password', $empty_password );

				return $error;
			} // close empty_username || empty_password.

			$loginpress_setting = get_option( 'loginpress_setting' );
			$login_order = isset(	$loginpress_setting['login_order'] ) ? $loginpress_setting['login_order'] : '';

			// Is login order is set to be 'email'.
			if ( 'email' == $login_order ) {

				if ( ! empty( $username ) && ! is_email( $username ) ) {

					$error = new WP_Error();

					$force_email_login= isset( $this->loginpress_key['force_email_login'] ) && ! empty( $this->loginpress_key['force_email_login'] ) ? $this->loginpress_key['force_email_login'] : sprintf( __( '%1$sError:%2$s Invalid Email Address', 'loginpress' ), '<strong>', '</strong>' );

					$error->add( 'loginpress_use_email', $force_email_login );

					return $error;
				}

				if ( ! empty( $username ) && is_email( $username ) ) {

					$username = str_replace( '&', '&amp;', stripslashes( $username ) );
					$user = get_user_by( 'email', $username );

					if ( isset( $user, $user->user_login, $user->user_status ) && 0 === intval( $user->user_status ) )
					$username = $user->user_login;
					return wp_authenticate_username_password( null, $username, $password );
				}
			} // login order 'email'.

			// Is login order is set to be 'username'.
			if ( 'username' == $login_order ) {
				$user = get_user_by('login', $username);

				$invalid_usrname = array_key_exists( 'incorrect_username', $this->loginpress_key ) && ! empty( $this->loginpress_key['incorrect_username'] ) ? $this->loginpress_key['incorrect_username'] : sprintf( __( '%1$sError:%2$s Invalid Username.', 'loginpress' ), '<strong>', '</strong>' );

				if ( ! $user ) {
					return new WP_Error( 'invalid_username', $invalid_usrname );
				}

				if ( ! empty( $username ) || ! empty( $password ) ) {

					$username = str_replace( '&', '&amp;', stripslashes( $username ) );
					$user = get_user_by( 'login', $username );

					if ( isset( $user, $user->user_login, $user->user_status ) && 0 === intval( $user->user_status ) )
					$username = $user->user_login;
					if ( ! empty( $username ) && is_email( $username ) ) {
						return wp_authenticate_username_password( null, "", "" );
					} else {
						return wp_authenticate_username_password( null, $username, $password );
					}

				}
			} // login order 'username'.

		}

	} // End Of Class.
endif;
