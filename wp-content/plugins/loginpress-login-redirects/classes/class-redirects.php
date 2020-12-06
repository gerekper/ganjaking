<?php

/**
 *   Redirects in process
 *
 */
if ( ! class_exists( 'LoginPress_Set_Login_Redirect' ) ) :

	class LoginPress_Set_Login_Redirect {

		public function __construct() {
			add_filter( 'login_redirect', array( $this, 'loginpress_redirects_after_login' ), 10, 3 );
      add_action( 'clear_auth_cookie', array( $this, 'loginpress_redirects_after_logout' ), 10 );
			add_action( 'loginpress_redirect_autologin', array( $this, '_autologin_redirects' ), 10, 1 );
    }

    /**
     * Check if inner link provided.
     *
     * @since 1.1.2
     * @return bool
     */
    function is_inner_link( $url ) {
      $current_site = wp_parse_url( get_site_url() );
      $current_site = $current_site['host'];

      if ( strpos( $url, $current_site ) !== false ) {
        return true;
      }

      return false;
    }


    /**
     * This function wraps around the main redirect function to determine whether or not to bypass the WordPress local URL limitation.
     * @param  string $redirect_to
     * @param  string $requested_redirect_to
     * @param  object $user
     * @return string
     * @since 1.0.0
     * @version 1.1.2
     */
    function loginpress_redirects_after_login( $redirect_to, $requested_redirect_to, $user ) {

			if ( apply_filters( 'prevent_loginpress_login_redirect', false ) ) {
        return;
      }

      if ( isset( $user->ID ) ) {
        $user_redirects_url = $this->loginpress_redirect_url( $user->ID, 'loginpress_login_redirects_url' );
        $role_redirects_url = get_option( 'loginpress_redirects_role' );

      	if ( isset( $user->roles ) && is_array( $user->roles ) ) {

      		// if ( in_array( 'administrator', $user->roles ) ) { //check for admins.
      		// 	// redirect them to the default place
      		// 	return $redirect_to;
          //
      		// } else

          if ( ! empty( $user_redirects_url ) ) { // check for specific user.

            if ( $this->is_inner_link( $user_redirects_url ) ) {
              return $user_redirects_url;
            }

            $this->_redirects( $user->ID, $user->name, $user, $user_redirects_url );

      		} elseif ( ! empty( $role_redirects_url ) ) { // check for specific role.

	      		foreach ( $role_redirects_url as $key => $value ) {
	      			if ( in_array( $key, $user->roles ) ) {

                if ( $this->is_inner_link( $value['login'] ) ) {
                  return $value['login'];
                }

								$this->_redirects( $user->ID, $user->name, $user, $value['login'] );

	      			}
	      		}
	      	}
      	} else {
      		return $redirect_to;
      	}
      }
      return $redirect_to;
    }

    /**
     * Callback for clear_auth_cookie.
     * Fire after user is logged out.
     * 
     * @return null
		 * @since 1.0.0
     */
    function loginpress_redirects_after_logout() {
      // Prevent method from executing.
      if ( apply_filters( 'prevent_loginpress_logout_redirect', false ) ) {
        return;
      }

      $user_id = get_current_user_id();

      // Only execute for registered user.
      if ( 0 !== $user_id ) {
        $user_info = get_userdata( $user_id );
        $user_role = $user_info->roles;
        $role_redirects_url = get_option( 'loginpress_redirects_role' );
        $user_redirects_url = $this->loginpress_redirect_url( $user_id, 'loginpress_logout_redirects_url' );

        if ( isset( $user_redirects_url ) && ! empty( $user_redirects_url ) ) {
          wp_redirect( $user_redirects_url );
          exit;
        } elseif ( ! empty( $role_redirects_url ) ) {
        	foreach ( $role_redirects_url as $key => $value ) {
						if ( in_array( $key, $user_role ) ) {
							wp_redirect( $value['logout'] );
							exit;
						}
					}
        }
      }
    }

		private function _redirects( $user_id, $username, $user, $redirect ) {

			wp_set_auth_cookie( $user_id, false );
			do_action( 'wp_login', $username, $user );
			wp_redirect( $redirect );
			exit;
		}

		/**
		 * _autologin_redirects redirect a user to a custom URL
		 * @param  object $user
		 * @return string URL
		 * @since 1.0.1
		 */
		public function _autologin_redirects( $user ){
			// if ( isset( $user->ID ) ) {
        $user_redirects_url = $this->loginpress_redirect_url( $user->ID, 'loginpress_login_redirects_url' );
				$role_redirects_url = get_option( 'loginpress_redirects_role' );

      	if ( isset( $user->roles ) && is_array( $user->roles ) ) {

					if ( ! empty( $user_redirects_url ) ) { // check for specific user.

      			$this->_redirects( $user->ID, $user->name, $user, $user_redirects_url );

      		} elseif ( ! empty( $role_redirects_url ) ) { // check for specific role.

	      		foreach ( $role_redirects_url as $key => $value ) {
	      			if ( in_array( $key, $user->roles ) ) {

								$this->_redirects( $user->ID, $user->name, $user, $value['login'] );

	      			}
	      		}
	      	}
      	// } else {
      	// 	return $redirect_to;
      	// }
      }
		}

		/**
		 * Get user meta.
		 * @param  int $user_id [ID of the use]
		 * @param string $option [user meta key]
		 * @since 1.0.1
		 */
		public function loginpress_redirect_url( $user_id, $option ) {

			if ( ! is_multisite() ) {
				$redirect_url = get_user_meta( $user_id, $option, true );
			} else {
				$redirect_url = get_user_option( $option, $user_id );
			}

			return $redirect_url;
		}

	}

	endif;
  new LoginPress_Set_Login_Redirect();
