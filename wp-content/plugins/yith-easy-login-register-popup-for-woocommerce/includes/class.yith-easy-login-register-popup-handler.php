<?php
/**
 * Popup handler class
 *
 * @author  YITH
 * @package YITH Easy Login & Register Popup For WooCommerce
 * @version 1.0.0
 */

defined( 'YITH_WELRP' ) || exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_Easy_Login_Register_Popup_Handler' ) ) {
	/**
	 * YITH Easy Login & Register Popup For WooCommerce
	 *
	 * @since 1.0.0
	 */
	class YITH_Easy_Login_Register_Popup_Handler {

		/**
		 * Ajax popup action
		 *
		 * @since 1.0.0
		 * @var string
		 */
		protected $form_action = 'yith_welrp_form_action';

		/**
		 * Check login ajax action
		 *
		 * @since 1.0.0
		 * @var string
		 */
		protected $check_login = 'yith_welrp_check_login';

		/**
		 * Current processed user
		 *
		 * @since 1.0.0
		 * @var WP_User|boolean
		 */
		protected $user = false;

		/**
		 * reCaptcha public key
		 *
		 * @since 1.0.0
		 * @var string
		 */
		private $_recaptcha_public = '';

		/**
		 * reCaptcha private key
		 *
		 * @since 1.0.0
		 * @var string
		 */
		private $_recaptcha_private = '';

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function __construct() {
			// Handle Ajax
			add_action( 'wc_ajax_' . $this->form_action, [ $this, 'handle_form_action' ] );
			add_action( 'wp_ajax_nopriv_' . $this->form_action, [ $this, 'handle_form_action' ] );
			add_action( 'wc_ajax_' . $this->check_login, [ $this, 'check_login' ] );
			add_action( 'wp_ajax_nopriv_' . $this->check_login, [ $this, 'check_login' ] );
			add_filter( 'yith_welrp_script_data', [ $this, 'add_ajax_handler_data' ], 10, 1 );
			// Handle reCaptcha
			if ( $this->enabled_recaptcha() ) {
				add_filter( 'yith_welrp_script_data', [ $this, 'add_recaptcha_key' ], 10, 1 );
				add_action( 'yith_welrp_before_register_action', [ $this, 'verify_captcha' ], 10, 1 );
			}
		}

		/**
		 * Add AJAX data to script data array
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @param array $data
		 * @return array
		 */
		public function add_ajax_handler_data( $data ) {
			$data = array_merge( $data, [
				'formAction' => $this->form_action,
				'formNonce'  => wp_create_nonce( $this->form_action ),
				'checkLogin' => $this->check_login,
				'checkNonce' => wp_create_nonce( $this->check_login ),
			] );

			return $data;
		}

		/**
		 * Check for login
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @return void <francesco.licandro@yithemes.com>
		 */
		public function check_login() {
			check_ajax_referer( $this->check_login );
			wp_send_json_success( [
				'logged' => is_user_logged_in(),
			] );
		}

		/**
		 * Handle form action
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return void
		 * @throws Exception
		 */
		public function handle_form_action() {

			check_ajax_referer( $this->form_action );

			try {

				$response = [ 'message' => '' ];
				$action   = ! empty( $_POST['action'] ) ? preg_replace( '/[^a-z_-]/', '', $_POST['action'] ) : 'first-step';
				$handler  = 'handle_' . str_replace( '-', '_', $action );

				if ( empty( $_POST['user_login'] ) || ! method_exists( $this, $handler ) ) {
					wp_die( -1, 400 ); // malformed request
				}

				$user_login = wc_clean( $_POST['user_login'] );
				// try to set user from posted data
				$this->try_set_user_from_posted( $user_login, $action );
				// then validate current user for action
				$this->validate_user_action( $action, $user_login );
				// at the end handle action
				// redefining user_login ensures we return the right case .
				$user_login = $this->user ? $this->user->user_login : $user_login;
				$response   = array_merge( $response, $this->$handler( $user_login ) );

				wp_send_json_success( apply_filters( 'yith_welrp_ajax_form_action_response', $response, $action, $user_login ) );

			} catch ( Exception $e ) {
				$message = $e->getMessage();
				// if empty get the standard message
				! $message && $message = yith_welrp_get_std_error_message();

				wp_send_json_error( [
					'message' => $message,
				] );
			}
		}

		/**
		 * Get an user login from posted data
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param string $user_login
		 * @param string $action
		 * @return void
		 */
		protected function try_set_user_from_posted( $user_login, $action ) {
			if ( is_email( $user_login ) ) {
				$this->user = get_user_by( 'email', $user_login );
			} elseif ( get_option( 'yith_welrp_allow_username', 'no' ) == 'yes' || $action != 'first-step' ) {
				$this->user = get_user_by( 'login', sanitize_user( $user_login ) );
			}
		}

		/**
		 * Validate an user action based on current user
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param string $action
		 * @param string $user_login
		 * @return void
		 * @throws Exception
		 */
		protected function validate_user_action( $action, $user_login ) {

			$username_enabled = get_option( 'yith_welrp_allow_username', 'no' ) == 'yes';

			if ( ( $action == 'register' && $this->user )
				|| ( ! $this->user && ( $action == 'authenticate-lost-password' || $action == 'set-new-password' ) ) ) {
				// malformed request, send 400. This case should never happen
				wp_die( -1, 400 );
			} elseif ( ( $action == 'first-step' && ! $username_enabled && ! is_email( $user_login ) ) ||
				( ( $action == 'login' || $action == 'lost-password' ) && ! $this->user ) ) {
				// in this case, because the user doesn't exists, user must add a valid email/username
				$message = $username_enabled ? __( 'Please enter a valid username or email address.', 'yith-easy-login-register-popup-for-woocommerce' )
					: __( 'Please enter a valid email address.', 'yith-easy-login-register-popup-for-woocommerce' );
				throw new Exception( $message );
			}
		}

		/**
		 * Handle request action. User add his email/username and ask to register/login
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param string $user_login
		 * @return array
		 * @throws Exception
		 */
		protected function handle_first_step( $user_login ) {

			$action = $this->user ? 'login' : 'register';
			// start build a response
			$response = [
				'popup' => [
					'user_login' => $user_login,
				],
			];

			// filter avatar url
			add_filter( 'get_avatar_url', [ $this, 'default_avatar_url' ], 10, 3 );

			$default_wp_avatar = get_option( 'avatar_default' );

			if ( $action == 'login' ) {
				$response['action'] = [ 'nextSection' => 'login-section' ];
				$response['popup']  = array_merge( $response['popup'], [
					'title'      => yith_welrp_replace_placeholder( get_option( "yith_welrp_popup_login_title", _x( 'Welcome back, [username]!', '[username] is a placeholder for username.', 'yith-easy-login-register-popup-for-woocommerce' ) ), $this->user ),
					'message'    => yith_welrp_replace_placeholder( get_option( 'yith_welrp_popup_login_message', __( 'Great to see you again! Enter your password to continue.', 'yith-easy-login-register-popup-for-woocommerce' ) ), $this->user ),
					'avatar'     => get_avatar( $this->user->user_email, 55, $default_wp_avatar, '' ),
					'user_email' => $this->user->user_email,
				] );
			} else {

				$button_label = empty( $_POST['additional'] )
					? get_option( 'yith_welrp_popup_register_button_label', __( 'Register and proceed to checkout', 'yith-easy-login-register-popup-for-woocommerce' ) )
					: get_option( 'yith_welrp_additional_popup_register_button', __( 'Register', 'yith-easy-login-register-popup-for-woocommerce' ) );

				$response['action'] = [ 'nextSection' => 'register-section' ];
				$response['popup']  = array_merge( $response['popup'], [
					'title'        => yith_welrp_replace_placeholder( get_option( "yith_welrp_popup_register_title", __( 'You are new here. Create your account!', 'yith-easy-login-register-popup-for-woocommerce' ) ), $this->user ),
					'message'      => yith_welrp_replace_placeholder( get_option( 'yith_welrp_popup_register_message', __( 'It seems you don\'t have an account on [blogname] yet. But don\'t worry, you can create one and then complete your order.', 'yith-easy-login-register-popup-for-woocommerce' ) ), $this->user ),
					'button_label' => $button_label,
					'avatar'       => get_avatar( 0, 55, 'mystery', '', [ 'force_default' => true ] ),
					'email_field'  => ! is_email( $user_login ),
				] );
			}

			return $response;
		}

		/**
		 * Handle login action. User is already register and want to login
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param string $user_login
		 * @return array
		 * @throws Exception
		 */
		protected function handle_login( $user_login ) {

			if ( empty( $_POST['user_password'] ) ) {
				throw new Exception( __( 'Password field cannot be empty.', 'yith-easy-login-register-popup-for-woocommerce' ) );
			}

			do_action( 'yith_welrp_before_login_action', $this->user );

			// avoid Advanced noCaptcha & invisible Captcha check
			add_filter( 'anr_verify_captcha_pre', '__return_true' );

			// Perform the login.
			$user = wp_signon( apply_filters( 'yith_welrp_login_params', [
				'user_login'    => $user_login,
				'user_password' => $_POST['user_password'],
				'remember'      => ! empty( $_POST['remeberme'] ),
			] ), is_ssl() );

			if ( is_wp_error( $user ) ) {
				$message = $user->get_error_message();
				if ( $user->get_error_code() == 'incorrect_password' ) {
					$message = preg_replace( '/(<a).*(\/a>)/', '', $message );
				}

				throw new Exception( $message );
			}

			do_action( 'yith_welrp_after_login_action', $user );

			// because you are going to redirect user, use wc notice to send a message
			wc_add_notice( __( 'You have successfully logged in, great to see you again!', 'yith-easy-login-register-popup-for-woocommerce' ) );

			return [ 'action' => [ 'redirectTo' => yith_welrp_get_redirect_url_from_posted() ] ];
		}

		/**
		 * Handle register action. User is new and want to register
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param string $user_login
		 * @return array
		 * @throws Exception
		 */
		protected function handle_register( $user_login ) {

			// in this case user_login must be an email
			$username = ! is_email( $user_login ) ? $user_login : wc_create_new_customer_username( $user_login );
			$email    = isset( $_POST['reg_email'] ) ? wc_clean( $_POST['reg_email'] ) : $user_login;
			$password = isset( $_POST['reg_password'] ) ? $_POST['reg_password'] : '';

			if ( ! is_email( $email ) ) {
				throw new Exception( __( 'Please enter a valid email address.', 'yith-easy-login-register-popup-for-woocommerce' ) );
			}
			if ( $user = get_user_by( 'email', $email ) ) {
				throw new Exception( __( 'An account is already registered with this email.', 'yith-easy-login-register-popup-for-woocommerce' ) );
			}
			if ( get_option( 'yith_welrp_popup_register_repeat_password', 'no' ) == 'yes' && ( empty( $_POST['reg_password_2'] ) || $password != $_POST['reg_password_2'] ) ) {
				throw new Exception( __( 'Passwords do not match! Please try again.', 'yith-easy-login-register-popup-for-woocommerce' ) );
			}
			if ( get_option( 'yith_welrp_popup_register_policy_enabled', 'no' ) == 'yes' && empty( $_POST['terms_policy'] ) ) {
				throw new Exception( __( 'You must accept our terms and privacy policy!', 'yith-easy-login-register-popup-for-woocommerce' ) );
			}

			do_action( 'yith_welrp_before_register_action', $user_login );

			// avoid Advanced noCaptcha & invisible Captcha check
			add_filter( 'anr_verify_captcha_pre', '__return_true' );

			$new_customer = wc_create_new_customer( $email, wc_clean( $username ), $password );
			if ( is_wp_error( $new_customer ) ) {
				throw new Exception( $new_customer->get_error_message() );
			}
			// finally login
			wc_set_customer_auth_cookie( $new_customer );

			do_action( 'yith_welrp_after_register_action', $user_login, $new_customer );

			// because you are going to redirect user, use wc notice to send a message
			wc_add_notice( __( 'Your account has been successfully created. Your login details have been sent to your email address.', 'yith-easy-login-register-popup-for-woocommerce' ) );

			return [ 'action' => [ 'redirectTo' => yith_welrp_get_redirect_url_from_posted() ] ];
		}

		/**
		 * Handle lost password action. User is asking to reset his password
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param string $user_login
		 * @return array
		 * @throws Exception
		 */
		protected function handle_lost_password( $user_login ) {

			do_action( 'yith_welrp_before_reset_password_action', $user_login );

			if ( ! $this->user || ( is_multisite() && ! is_user_member_of_blog( $this->user->ID, get_current_blog_id() ) ) ) {
				throw new Exception( __( 'Please enter a valid email address.', 'yith-easy-login-register-popup-for-woocommerce' ) );
			}

			do_action( 'retrieve_password', $user_login );

			$allow = apply_filters( 'allow_password_reset', true, $this->user->ID );
			if ( ! $allow ) {
				throw new Exception( __( 'Password reset is not allowed for this user', 'yith-easy-login-register-popup-for-woocommerce' ) );
			} elseif ( is_wp_error( $allow ) ) {
				throw new Exception( $allow->get_error_message() );
			}

			$type = get_option( 'yith_welrp_popup_lost_password_recover_type', 'classic' );

			// Send email notification.
			WC()->mailer(); // Load email classes.

			$response = [
				'action' => [ 'nextSection' => 'lost-password-section' ],
			];

			if ( $type == 'with-code' ) {

				$authentication_code = apply_filters( 'yith_welrp_authentication_code', wp_generate_password( 10, false, false ) );
				// save as user meta with expired date
				update_user_meta( $this->user->ID, '_yith_welrp_auth_code', $authentication_code );
				// send mail
				do_action( 'send_yith_welrp_customer_authentication_code_notification', $this->user, $authentication_code );

				$response['message'] = isset( $_POST['resend'] ) ? __( 'A new authentication code was sent successfully to your email', 'yith-easy-login-register-popup-for-woocommerce' ) : '';
				$response['popup']   = [
					'action'       => 'authenticate-lost-password',
					'user_login'   => $user_login,
					'title'        => yith_welrp_replace_placeholder( get_option( 'yith_welrp_popup_lost_password_authentication_title', __( 'Authentication required', 'yith-easy-login-register-popup-for-woocommerce' ) ), $this->user ),
					'button_label' => get_option( 'yith_welrp_popup_lost_password_authentication_button_label', __( 'Continue', 'yith-easy-login-register-popup-for-woocommerce' ) ),
					'message'      => yith_welrp_replace_placeholder( get_option( 'yith_welrp_popup_lost_password_authentication_message', __( 'For your security we need to authenticate your request. We\'ve sent a code to your email. Please enter it below.', 'yith-easy-login-register-popup-for-woocommerce' ) ), $this->user ),
				];
			} else {
				// Get password reset key (function introduced in WordPress 4.4).
				$key = get_password_reset_key( $this->user );
				do_action( 'woocommerce_reset_password_notification', $user_login, $key );

				$response['message'] = isset( $_POST['resend'] ) ? __( 'A new password reset email has been sent to the email address of your account.', 'yith-easy-login-register-popup-for-woocommerce' ) : '';
				$response['popup']   = [
					'action'     => 'lost-password-confirm',
					'user_login' => $user_login,
					'title'      => yith_welrp_replace_placeholder( get_option( 'yith_welrp_popup_lost_password_confirm_title', __( 'Check your email', 'yith-easy-login-register-popup-for-woocommerce' ) ), $this->user ),
					'message'    => yith_welrp_replace_placeholder( get_option( 'yith_welrp_popup_lost_password_confirm_message', __( 'A password reset email has been sent to the email address of your account. If you did not receive it, check your spam folder or click to receive another email.', 'yith-easy-login-register-popup-for-woocommerce' ) ), $this->user ),
				];
			}

			do_action( 'yith_welrp_after_reset_password_action', $user_login );

			return $response;
		}

		/**
		 * Handle authentication code for lost password
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param string $user_login
		 * @return array
		 * @throws Exception
		 */
		protected function handle_authenticate_lost_password( $user_login ) {

			// get code
			$code         = ! empty( $_POST['authentication_code'] ) ? wc_clean( $_POST['authentication_code'] ) : '';
			$founded_user = $code ? get_users( [
				'meta_key'     => '_yith_welrp_auth_code',
				'meta_value'   => $code,
				'meta_compare' => '=',
			] ) : [];

			$founded_user = ! empty( $founded_user ) ? array_shift( $founded_user ) : '';

			if ( ! $code || ! $founded_user || $founded_user->user_login != $user_login ) {
				throw new Exception( __( 'Invalid code.', 'yith-easy-login-register-popup-for-woocommerce' ) );
			}

			$button_label = empty( $_POST['additional'] )
				? get_option( 'yith_welrp_popup_lost_password_set_button_label', __( 'Save password and proceed to checkout', 'yith-easy-login-register-popup-for-woocommerce' ) )
				: get_option( 'yith_welrp_additional_popup_set_password_button', __( 'Save password and access', 'yith-easy-login-register-popup-for-woocommerce' ) );

			$response = [
				'action' => [ 'nextSection' => 'lost-password-section' ],
				'popup'  => [
					'action'       => 'set-new-password',
					'user_login'   => $user_login,
					'title'        => yith_welrp_replace_placeholder( get_option( 'yith_welrp_popup_lost_password_set_title', __( 'Set a new password and login', 'yith-easy-login-register-popup-for-woocommerce' ) ), $this->user ),
					'button_label' => $button_label,
					'message'      => yith_welrp_replace_placeholder( get_option( 'yith_welrp_popup_lost_password_set_message', __( 'Now you can set a new password for your account.', 'yith-easy-login-register-popup-for-woocommerce' ) ), $this->user ),
				],
			];

			return $response;
		}

		/**
		 * Handle set customer new password
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param string $user_login
		 * @return array
		 * @throws Exception
		 */
		protected function handle_set_new_password( $user_login ) {

			// get password
			$password = isset( $_POST['new_password'] ) ? $_POST['new_password'] : '';
			if ( get_option( 'yith_welrp_popup_lost_password_set_repeat_password', 'no' ) == 'yes' && ( empty( $_POST['new_password_2'] ) || $password != $_POST['new_password_2'] ) ) {
				throw new Exception( __( 'Passwords do not match! Please try again.', 'yith-easy-login-register-popup-for-woocommerce' ) );
			}

			wp_set_password( $password, $this->user->ID );
			// delete user meta
			delete_user_meta( $this->user->ID, '_yith_welrp_auth_code' );
			// finally login
			wc_set_customer_auth_cookie( $this->user->ID );

			// because you are going to redirect user, use wc notice to send a message
			wc_add_notice( __( 'The password of your account has been changed successfully', 'yith-easy-login-register-popup-for-woocommerce' ) );
			$response = [ 'action' => [ 'redirectTo' => yith_welrp_get_redirect_url_from_posted() ] ];

			return $response;
		}

		/**
		 * Filter default avatar url to get the custom one
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param string $url         The URL of the avatar.
		 * @param mixed  $id_or_email The Gravatar to retrieve. Accepts a user_id, gravatar md5 hash,
		 *                            user email, WP_User object, WP_Post object, or WP_Comment object.
		 * @param array  $args        Arguments passed to get_avatar_data(), after processing.
		 * @return string
		 */
		public function default_avatar_url( $url, $id_or_email, $args ) {
			if ( $args['found_avatar'] || apply_filters( 'yith_welrp_use_wp_default_avatar', false ) ) {
				return $url;
			}
			return YITH_WELRP_ASSETS_URL . 'images/user.svg';
		}

		/**
		 * Check if reCaptcha is enabled
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @return boolean
		 */
		public function enabled_recaptcha() {
			if ( get_option( 'yith_welrp_popup_register_enable_recaptcha', 'no' ) != 'yes' ) {
				return false;
			}

			$this->_recaptcha_public  = get_option( 'yith_welrp_popup_register_recaptcha_public_key', '' );
			$this->_recaptcha_private = get_option( 'yith_welrp_popup_register_recaptcha_private_key', '' );

			if ( ! $this->_recaptcha_public || ! $this->_recaptcha_private ) {
				return false;
			}

			return true;
		}

		/**
		 * Add reCaptcha public key to script data
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @param array $data
		 * @return array
		 */
		public function add_recaptcha_key( $data ) {
			$data['googleReCaptcha'] = $this->_recaptcha_public;
			return $data;
		}

		/**
		 * Verify posted reCaptcha
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @param string $user_login
		 * @return boolean
		 * @throws Exception
		 */
		public function verify_captcha( $user_login ) {
			if ( empty( $_REQUEST['g-recaptcha-response'] ) || ! $this->is_recaptcha_valid( $_REQUEST['g-recaptcha-response'] ) ) {
				throw new Exception( __( 'ReCaptcha validation error. Please try again.', 'yith-easy-login-register-popup-for-woocommerce' ) );
			}

			return true;
		}

		/**
		 * Validate reCaptcha
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro
		 * @param string $response
		 * @return boolean
		 */
		protected function is_recaptcha_valid( $response ) {
			if ( ! $response || ! $this->_recaptcha_private ) {
				return false;
			}

			$ch = curl_init();
			curl_setopt( $ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify" );
			curl_setopt( $ch, CURLOPT_POST, 1 );
			curl_setopt( $ch, CURLOPT_POSTFIELDS, array(
				'secret'   => $this->_recaptcha_private,
				'response' => $response,
			) );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
			$output = curl_exec( $ch );

			curl_close( $ch );

			$output = json_decode( $output );

			return ! empty( $output->success ) ? $output->success : false;
		}
	}
}
