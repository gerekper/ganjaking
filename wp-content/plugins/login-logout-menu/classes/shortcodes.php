<?php
/**
 * Login Logout Shortcode Class to implement the shortcode functionality.
 *
 * @since 1.3.0
 * @version 1.3.1
 * 
 */

if ( ! class_exists( 'Login_Logout_Menu_Shortcode' ) ):

	/**
	 * Shortcode cLass for Login, Logout, Register and Reset Password Link creation
	 * 
	 * @since 1.3.0
	 * 
	 */
	class Login_Logout_Menu_Shortcode {

		/**
		 * constructor function of class `Login_Logout_Menu_Shortcode`
		 * 
		 * @since 1.3.0
		 *  
		 */
		function __construct() {

			add_shortcode( 'login_logout_menu__login_link', 		array( $this, 'login_logout_menu__login_link_callback' ) );
			add_shortcode( 'login_logout_menu__logout_link', 		array( $this, 'login_logout_menu__logout_link_callback' ) );
			add_shortcode( 'login_logout_menu__profile_link',		array( $this, 'login_logout_menu__profile_link_callback' ) );
			add_shortcode( 'login_logout_menu__register_link',		array( $this, 'login_logout_menu__register_link_callback' ) );
			add_shortcode( 'login_logout_menu__username_link',		array( $this, 'login_logout_menu__username_link_callback' ) );
			add_shortcode( 'login_logout_menu__reset_pass_link',	array( $this, 'login_logout_menu__reset_pass_link_callback' ) );
			add_shortcode( 'login_logout_menu__login_logout_link', 	array( $this, 'login_logout_menu__login_logout_link_callback' ) );

		}

		/**
		 * Callback function of 'login_logout_menu__login_logout_link' Shortcode to show login-logout buttons.
		 * 
		 * @param string  $atts[login_url]    			The Login redirect URL
		 * @param string  $atts[logout_url]      		The Logout redirect URL
		 * @param string  $atts[login_text]	 			Login link Text
		 * @param string  $atts[logout_text]			Logout link Text
		 * @param string  $atts[login_logout_class]   	Custom CSS class for styling purpose.
		 * 
		 * @since 1.3.0
		 * @version 1.3.1
		 * @return html Link to Login or logout
		 */
		function login_logout_menu__login_logout_link_callback( $atts ) {

			//Current Page URL
			$item_redirect	= site_url( $_SERVER['REQUEST_URI'] ) ;
			
			//Default args adding in the shortcode as shortcode attributes
			$args = shortcode_atts( array(
				'login_url'				=> $item_redirect,
				'logout_url'			=> $item_redirect,
				'login_text'			=> __( 'Log in', 'login-logout-menu' ),
				'logout_text'			=> __( 'Log out', 'login-logout-menu' ),
				'login_logout_class'	=> 'login_logout_class',
			), $atts );

			//If user is logged in
			if ( is_user_logged_in() ) {
				return '<a href="' . wp_logout_url( $args['logout_url'] ) . '" class="' . $args['login_logout_class'] . ' "title="' . $args['logout_text'] . '">' . $args['logout_text'] . '</a>';
			} else {
				return '<a href="' . wp_login_url( $args['login_url'] ) . '" class="' . $args['login_logout_class'] . '"title="' . $args['login_text'] . '">' . $args['login_text'] . '</a>';
			}
			
		}

		/**
		 * Callback function of 'login_logout_menu__login_link' Shortcode to show login-logout buttons.
		 * 
		 * @param string  $atts[login_url]    			The Login redirect URL
		 * @param string  $atts[login_text]	 			Login link Text
		 * @param string  $atts[login_logout_class]   	Custom CSS class for styling purpose.
		 * 
		 * @since 1.3.0
		 * @version 1.3.1
		 * @return html Link to Login
		 */
		function login_logout_menu__login_link_callback( $atts ) {

			if ( is_user_logged_in() )
				return;

			//Current Page URL
			$item_redirect	= site_url(  $_SERVER['REQUEST_URI'] ) ;
			
			//Default args adding in the shortcode as shortcode attributes
			$args = shortcode_atts( array(
				'login_url'				=> $item_redirect,
				'login_text'			=> __( 'Log in', 'login-logout-menu' ),
				'login_logout_class'	=> 'login_logout_class',
			), $atts );

			//If user is logged in
			if ( ! is_user_logged_in() ) {
				return '<a href="' . wp_login_url( $args['login_url'] ) . '" class="' . $args['login_logout_class'] . ' " title="' . $args['login_text'] . '">' . $args['login_text'] . '</a>';
			}
		}

		/**
		 * Callback function of 'login_logout_menu__logout_link' Shortcode to show logout buttons.
		 * 
		 * @param string  $atts[logout_url]    			The Login redirect URL
		 * @param string  $atts[logout_text]	 		Login link Text
		 * @param string  $atts[login_logout_class]   	Custom CSS class for styling purpose.
		 * 
		 * @since 1.3.0
		 * @version 1.3.1
		 * @return html Link to logout
		 */
		function login_logout_menu__logout_link_callback( $atts ) {

			if ( ! is_user_logged_in() )
				return;

			//Current Page URL
			$item_redirect	= site_url(  $_SERVER['REQUEST_URI'] ) ;
			
			// Default args adding in the shortcode as shortcode attributes
			$args = shortcode_atts( array(
				'logout_url'			=> $item_redirect,
				'logout_text'			=> __( 'Log out', 'login-logout-menu' ),
				'login_logout_class'	=> 'login_logout_class',
			), $atts );

			//If user is logged in
			return '<a href="' . wp_logout_url( $args['logout_url'] ) . '" class="' . $args['login_logout_class'] . ' " title="' . $args['logout_text'] . '">' . $args['logout_text'] . '</a>';
		}

		/**
		 * Callback of 'login_logout_menu__register_link' Shortcode.
		 * 
		 * @param string  $atts[register_url]    		The Registration page URL
		 * @param string  $atts[register_text]	 		Registration redirect link Text
		 * @param string  $atts[login_logout_class]   	Custom CSS class for styling purpose.
		 * 
		 * @since 1.3.0
		 * @version 1.3.1
		 * @return html the link to Registration page
		 */
		function login_logout_menu__register_link_callback( $atts ) {

			if ( is_user_logged_in() )
				return;

			//Current Page URL
			$item_redirect	= site_url(  $_SERVER['REQUEST_URI'] ) ;

			//Default args adding in the shortcode as shortcode attributes
			$args = shortcode_atts( array(
				'register_url'			=> $item_redirect,
				'register_text'			=> __( 'Register', 'login-logout-menu' ),
				'login_logout_class'	=> 'login_logout_class',
			), $atts );

			//If user is not logged in
			return '<a href="' . wp_registration_url( $args['register_url'] ) . '" class="' . $args['login_logout_class'] . '">' . $args['register_text'] . '</a>';
		}

		/**
		 * Callback of 'login_logout_menu__reset_pass_link' Shortcode.
		 * 
		 * @param string  $atts[lostpassword_url]    		The Lost Password URL
		 * @param string  $atts[lostpassword_text]	 		Lost Password link Text
		 * @param string  $atts[login_logout_class]   		CSS class for styling purpose
		 * 
		 * @since 1.3.0
		 * @version 1.3.1
		 * @return html the link to Lost Password form page
		 */
		function login_logout_menu__reset_pass_link_callback( $atts ) {

			if ( is_user_logged_in() )
				return;

			//Current Page URL
			$item_redirect	= site_url(  $_SERVER['REQUEST_URI'] ) ;

			//Default args adding in the shortcode as shortcode attributes
			$args = shortcode_atts( array(
				'lostpassword_url'		=> $item_redirect,
				'lostpassword_text'		=> __( 'Reset Password', 'login-logout-menu' ),
				'login_logout_class'	=> 'login_logout_class',
			), $atts );

			return '<a href="' . wp_lostpassword_url( $args['lostpassword_url'] ) . '" class="' . $args['login_logout_class'] . '" title="' . $args['lostpassword_text'] . '">' . $args['lostpassword_text'] . '</a>';
		}

		/**
		 * Callback of 'login_logout_menu__username_link' Shortcode. 
		 * 
		 * @param string  $atts[url]    					Account/Profile page link
		 * @param string  $atts[username]	 				Display name of logged in user
		 * @param string  $atts[login_logout_class]   		CSS class for styling purpose
		 * 
		 * @since 1.3.0
		 * @return html the link to account/profile page
		 */
		function login_logout_menu__username_link_callback( $atts ){

			if ( ! is_user_logged_in() )
				return ;

			$current_user = wp_get_current_user();
			$username     = apply_filters( 'login_logout_menu_username', $current_user->display_name );
			//Default args adding in the shortcode as shortcode attributes
			
			$args = shortcode_atts( array(
				'url'					=> esc_url( apply_filters( 'login_logout_menu_username_url', Login_Logout_Menu::login_logout_menu_profile_link() ) ),
				'username'				=> esc_html( $username ),
				'login_logout_class'	=> 'login_logout_class',
			), $atts );

			return '<a href="' . $args['url'] . '" class="'.$args['login_logout_class'] . '" title="' . $args['username'] . '">' . $args['username'] . '</a>';
		}

		
		/**
		 * Callback of 'login_logout_menu__profile_link' Shortcode. 
		 * 
		 * @param string  $atts[url]    					Account/Profile page link
		 * @param string  $atts[edit_text]	 				Text of edit profile link
		 * @param string  $atts[login_logout_class]   		CSS class for styling purpose
		 * 
		 * @since 1.3.0
		 * @return html the link to edit account/profile page
		 */
		function login_logout_menu__profile_link_callback( $atts ){

			if ( ! is_user_logged_in() )
				return ;

			//Default args adding in the shortcode as shortcode attributes
			$args = shortcode_atts( array(
				'url'					=> esc_url( apply_filters( 'login_logout_menu_profile', Login_Logout_Menu::login_logout_menu_profile_link() ) ),
				'edit_text'				=> __( 'Edit Profile', 'login-logout-menu' ),
				'login_logout_class'	=> 'login_logout_class',
			), $atts );

			return '<a href="' . $args['url'] . '" class="' . $args['login_logout_class'] . '" title="' . $args['edit_text'] . '">' . $args['edit_text'] . '</a>';
		}
		
	}
endif;
