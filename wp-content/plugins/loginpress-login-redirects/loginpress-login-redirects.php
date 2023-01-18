<?php

/**
 * Plugin Name: LoginPress - Redirect Login
 * Plugin URI: http://www.WPBrigade.com/wordpress/plugins/login-redirects/
 * Description: LoginPress is the best <code>wp-login</code> Login Redirects plugin by <a href="https://wpbrigade.com/">WPBrigade</a> which allows you to redirect user after login.
 * Version: 2.0.0
 * Author: WPBrigade
 * Author URI: http://www.WPBrigade.com/
 * Text Domain: loginpress-login-redirects
 * Domain Path: /languages
 *
 * @package loginpress
 * @category Core
 * @author WPBrigade
 */

 define( 'LOGINPRESS_REDIRECT_ROOT_PATH', dirname( __FILE__ ) );
 define( 'LOGINPRESS_REDIRECT_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
 define( 'LOGINPRESS_REDIRECT_DIR_PATH', plugin_dir_path( __FILE__ ) );
 define( 'LOGINPRESS_REDIRECT_DIR_URL', plugin_dir_url( __FILE__ ) );
 define( 'LOGINPRESS_REDIRECT_ROOT_FILE', __FILE__ );
 define( 'LOGINPRESS_REDIRECT_PLUGIN_ROOT', dirname( plugin_basename( __FILE__ ) ) );
 define( 'LOGINPRESS_REDIRECT_STORE_URL', 'https://WPBrigade.com' );
 define( 'LOGINPRESS_REDIRECT_VERSION', '2.0.0' );

 add_action( 'plugins_loaded', 'loginpress_redirect_login_instance', 25 );

function loginpress_redirect_login_instance() {

	if ( ! file_exists( WP_PLUGIN_DIR . '/loginpress-pro/loginpress-pro.php' ) ) {
		add_action( 'admin_notices', 'loginpress_redirect_login_install_pro' );
		return;
	}

	if ( ! class_exists( 'LoginPress_Pro' ) ) {
		add_action( 'admin_notices', 'loginpress_redirect_login_activate_pro' );
		return;
	}

	// if ( defined( 'LOGINPRESS_PRO_VERSION' ) ) {
	// 	$addons = get_option( 'loginpress_pro_addons' );
  //
	// 	if ( LOGINPRESS_PRO_VERSION < '3.0' ) {
	// 		// If PRO version is still old
	// 		add_action( 'admin_notices' , 'lp_login_redirects_depricated' );
	// 	} else if ( ( LOGINPRESS_PRO_VERSION >= '3.0.0' ) && ( ! empty( $addons ) ) && ( $addons['login-redirects']['is_active'] ) ) {
	// 		// If PRO addon and the same plugin both active
	// 		add_action( 'admin_notices' , 'lp_login_redirects_depricated_remove' );
	// 		return;
	// 	}
	// }

	 // Call the function
	 loginPress_redirect_login_loader();
}


 /**
  * Returns the main instance of WP to prevent the need to use globals.
  *
  * @since  1.0.0
  * @return object LoginPress_Login_Redirect_Main
  */
function loginPress_redirect_login_loader() {
	include_once LOGINPRESS_REDIRECT_ROOT_PATH . '/classes/class-loginpress-login-redirects.php';
	return LoginPress_Login_Redirect_Main::instance();
}

 /**
  * Notice if LoginPress Pro is not install.
  *
  * @since 1.0.0
  */
function loginpress_redirect_login_install_pro() {
	$class   = 'notice notice-error is-dismissible';
	$message = __( 'Please Install LoginPress Pro to use "LoginPress Redirect Login" add-on.', 'loginpress-login-redirects' );

	printf( '<div class="%1$s"><p>%2$s</p></div>', $class, $message );
}

 /**
  * Notice if LoginPress Pro is not activate.
  *
  * @since 1.0.0
  */
function loginpress_redirect_login_activate_pro() {

	$action = 'activate';
	$slug   = 'loginpress-pro/loginpress-pro.php';
	$link   = wp_nonce_url(
		add_query_arg(
			array(
				'action' => $action,
				'plugin' => $slug,
			),
			admin_url( 'plugins.php' )
		),
		$action . '-plugin_' . $slug
	);

	printf(
		'<div class="notice notice-error is-dismissible">
   <p>%1$s<a href="%2$s" style="text-decoration:none">%3$s</a></p></div>',
		esc_html__( 'LoginPress Redirect Login required LoginPress Pro activation &mdash; ', 'loginpress-login-redirects' ),
		$link,
		esc_html__( 'Click here to activate LoginPress Pro', 'loginpress-login-redirects' )
	);
}

// /**
// * Notice plugin is depricated.
// *
// * @since 1.1.3
// */
// function lp_login_redirects_depricated() {
//   $link   = '';
//
//   printf('<div class="notice notice-error is-dismissible">
//   <p>%1$s<a href="%2$s" style="text-decoration:none">%3$s</a></p></div>' , esc_html__( 'LoginPress Login Redirects Plugin is depricated, please upgrade to LoginPress Pro 3.0 &mdash; Find out more ', 'loginpress-auto-login' ), $link, esc_html__( 'here', 'loginpress-auto-login' ) );
// }

// /**
// * Notice plugin is depricated and remove.
// *
// * @since 1.1.3
// */
// function lp_login_redirects_depricated_remove() {
//   $link   = '';
//
//   printf('<div class="notice notice-error is-dismissible">
//   <p>%1$s<a href="%2$s" style="text-decoration:none">%3$s</a></p></div>' , esc_html__( 'LoginPress Login Redirects Plugin is depricated, you can remove it. &mdash; Find out more ', 'loginpress-auto-login' ), $link, esc_html__( 'here', 'loginpress-auto-login' ) );
// }
