<?php

/**
 *   Log in process
 *
 *   When the admin is accessed with the wp_auto_login query arg,
 *   check to see if the current user is logged in.
 *   If not, set the current user to the defined account (username and password)
 */
if ( ! class_exists( 'LoginPress_Set_User' ) ) :

	class LoginPress_Set_User {

		public function __construct() {

			add_action( 'init', array( $this, 'login_user_with_autologin_code' ) );
		}

		/**
		 *   Set current user session
		 *   @since 1.0.0
		 *   @version 1.0.1
		 */
		public function login_user_with_autologin_code() {

			global $wpdb;

			// Check if loginpress code is specified - if there is one the work begins
			if ( isset( $_GET['loginpress_code'] ) ) {

				$loginpress_code = preg_replace( '/[^a-zA-Z0-9]+/', '', $_GET['loginpress_code'] );

				if ( $loginpress_code ) { // Check if not empty
					// Get part left of ? of the request URI for resassembling the target url later
					$subURIs = array();
					if ( preg_match( '/^([^\?]+)\?/', $_SERVER['REQUEST_URI'], $subURIs ) === 1 ) {
						$pageRedirect = $subURIs[1];

					 // $loginpress_code has been heavily cleaned before
						$userIds = array();

						// WP_User_Query arguments
						$args = array(
							'blog_id'				 => $GLOBALS['blog_id'],
							'order'          => 'ASC',
							'orderby'        => 'display_name',
							'meta_query'     => array(
								array(
									'key'     => 'loginpress_autologin_code',
									'value'   => $loginpress_code,
									'compare' => '=',
								),
							),
						);
						$user_query = new WP_User_Query( $args );
						// User Loop
						if ( ! empty( $user_query->get_results() ) ) {
							foreach ( $user_query->get_results() as $user ) {
								$userIds[] = $user->ID;
							}
						} else {
							echo 'No users found.';
						}

						// Double login codes? should never autologin.
						if ( count( $userIds ) > 1 ) {
							wp_die( 'Please login normally - this is a statistic bug and prevents you from using login links securely!' ); // TODO !!!
						}

						// Only login if there is only ONE possible user
						if ( 1 == count( $userIds ) ) {
							$userToLogin = get_user_by( 'id', (int) $userIds[0] );

							// Check if user exists
							if ( $userToLogin ) {

								wp_set_auth_cookie( $userToLogin->ID, false );
								do_action( 'wp_login', $userToLogin->name, $userToLogin );

								// Create redirect URL without LoginPress code
								$GETQuery = $this->loginpress_get_variable();
								if ( class_exists( 'LoginPress_Set_Login_Redirect' ) && do_action( 'loginpress_redirect_autologin', $userToLogin ) ) {
									do_action( 'loginpress_redirect_autologin', $userToLogin );
								} else {
									if ( 'on' == $_SERVER['HTTPS'] ) {
										$ssl = 'https://';
									} else {
										$ssl = 'http://';
									}

									wp_redirect( $ssl . $_SERVER['HTTP_HOST'] . $pageRedirect . $GETQuery );
								}
								exit;
							}
						}
					}
				}

				// If something went wrong send the user to login-page (and log the old user out if there was any)
				wp_logout();
				wp_redirect( home_url( 'wp-login.php?loginpress_error=invalid_login_code' ) );
				exit;
			}
		}

		/**
		 * [loginpress_get_variable Generates string of the GET variable including '?' separator from the URL.]
		 *
		 * @return [string]
		 */
		function loginpress_get_variable() {

			$request = $_GET;
			unset( $request['loginpress_code'] );
			$GETString = $this->loginpress_join_get_variable( $request );
			if ( strlen( $GETString ) > 0 ) {
				$GETString = '?' . $GETString;
			}
			return $GETString;
		}

		/**
		 * [loginpress_join_get_variable Convert a GET variable array into GET-request parameter list]
		 *
		 * @param  [array] $request [description]
		 * @return [string]         [ return a string of GET variable ]
		 */
		function loginpress_join_get_variable( $request ) {

			$keys                 = array_keys( $request );
			$assignments  = array();
			foreach ( $keys as $key ) {
				$assignments[] = "$key=$request[$key]";
			}
			return implode( '&', $assignments );
		}
	}

	endif;
	new LoginPress_Set_User();
