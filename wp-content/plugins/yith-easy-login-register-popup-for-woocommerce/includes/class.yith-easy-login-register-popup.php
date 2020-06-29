<?php
/**
 * Popup class
 *
 * @author  YITH
 * @package YITH Easy Login & Register Popup For WooCommerce
 * @version 1.0.0
 */

defined( 'YITH_WELRP' ) || exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_Easy_Login_Register_Popup' ) ) {
	/**
	 * YITH Easy Login & Register Popup For WooCommerce
	 *
	 * @since 1.0.0
	 */
	class YITH_Easy_Login_Register_Popup {

		/**
		 * Constructor
		 *
		 * @since 1.0.0
		 * @return void
		 */
		public function __construct() {
			// add params to scripts data
			add_filter( 'yith_welrp_script_data', [ $this, 'add_popup_data' ], 10, 1 );
			// add main popup
			add_action( 'wp_footer', [ $this, 'add_popup' ], 10 );
			// add popup template parts
			add_action( 'wp_footer', [ $this, 'email_section_template' ], 20 );
			add_action( 'wp_footer', [ $this, 'login_section_template' ], 20 );
			add_action( 'wp_footer', [ $this, 'register_section_template' ], 20 );
			add_action( 'wp_footer', [ $this, 'lost_password_section_template' ], 20 );
		}

		/**
		 * Add data to script json
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param array $data
		 * @return array
		 */
		public function add_popup_data( $data ) {
			$data = array_merge( $data, [
				'lostPasswordTitle'   => yith_welrp_replace_placeholder( get_option( 'yith_welrp_popup_lost_password_title', __( 'Lost your password?', 'yith-easy-login-register-popup-for-woocommerce' ) ) ),
				'lostPasswordButton'  => get_option( 'yith_welrp_popup_lost_password_button_label', __( 'Reset password', 'yith-easy-login-register-popup-for-woocommerce' ) ),
				'lostPasswordMessage' => yith_welrp_replace_placeholder( get_option( 'yith_welrp_popup_lost_password_message', __( 'Don\'t worry. Enter the email address of your account.', 'yith-easy-login-register-popup-for-woocommerce' ) ) ),
			] );

			return $data;
		}

		/**
		 * Output the popup
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return void
		 */
		public function add_popup() {

			// build class overlay
			$wrapper_class = [];
			get_option( 'yith_welrp_popup_close_overlay', 'yes' ) == 'yes' && $wrapper_class[] = 'close-on-click';

			// get animation
			$animation = get_option( 'yith_welrp_popup_animation', [] );

			wc_get_template( 'popup.php',
				apply_filters( 'yith_welrp_popup_template_args', [
					'wrapper_class' => $wrapper_class,
					'animation_in'  => isset( $animation['entrance'] ) ? $animation['entrance'] : 'fadeIn',
					'animation_out' => isset( $animation['exit'] ) ? $animation['exit'] : 'fadeOut',
					'close_icon'    => get_option( 'yith_welrp_popup_close_icon', '' ),
				] ),
				WC()->template_path() . '/yith-welrp',
				YITH_WELRP_TEMPLATE_PATH
			);
		}

		/**
		 * Ask for email or login through social template part
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return void
		 */
		public function email_section_template() {
			wc_get_template(
				'email-section.php',
				apply_filters( 'yith_welrp_email_section_template_args', [
					'header'                   => get_option( 'yith_welrp_popup_header', __( 'Go to checkout', 'yith-easy-login-register-popup-for-woocommerce' ) ),
					'message'                  => yith_welrp_replace_placeholder( get_option( 'yith_welrp_popup_message', '' ) ),
					'button_label'             => get_option( 'yith_welrp_button_label', __( 'Continue', 'yith-easy-login-register-popup-for-woocommerce' ) ),
					'login_label'              => get_option( 'yith_welrp_input_label', __( 'E-mail address or username:', 'yith-easy-login-register-popup-for-woocommerce' ) ),
					'login_placeholder'        => $this->get_placeholder(),
					'continue_as_guest'        => ! WC()->checkout()->is_registration_required(),
					'continue_as_guest_text'   => __( 'Don\'t want to register now?', 'yith-easy-login-register-popup-for-woocommerce' ),
					'continue_as_guest_button' => __( 'Guest Checkout', 'yith-easy-login-register-popup-for-woocommerce' ),
				] ),
				WC()->template_path() . '/yith-welrp',
				YITH_WELRP_TEMPLATE_PATH
			);
		}

		/**
		 * "Login/Register section template part
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return void
		 */
		public function login_section_template() {
			wc_get_template(
				'login-section.php',
				apply_filters( 'yith_welrp_login_section_template_args', [
					'header'               => get_option( 'yith_welrp_popup_login_header', __( 'Proceed to checkout', 'yith-easy-login-register-popup-for-woocommerce' ) ),
					'button_label'         => get_option( 'yith_welrp_popup_login_button_label', __( 'Sign in', 'yith-easy-login-register-popup-for-woocommerce' ) ),
					'password_label'       => get_option( 'yith_welrp_popup_login_input_label', _x( 'Password:', 'Input password label', 'yith-easy-login-register-popup-for-woocommerce' ) ),
					'password_placeholder' => _x( 'Enter password here', 'Input password placeholder', 'yith-easy-login-register-popup-for-woocommerce' ),
					'remember_label'       => get_option( 'yith_welrp_popup_login_remember_label', __( 'Stay signed in', 'yith-easy-login-register-popup-for-woocommerce' ) ),
					'remember_checked'     => get_option( 'yith_welrp_popup_login_remember_checked', 'no' ) == 'yes',
				] ),
				WC()->template_path() . '/yith-welrp',
				YITH_WELRP_TEMPLATE_PATH
			);
		}


		/**
		 * "Login/Register section template part
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return void
		 */
		public function register_section_template() {
			wc_get_template(
				'register-section.php',
				apply_filters( 'yith_welrp_register_section_template_args', [
					'header'                      => get_option( 'yith_welrp_popup_register_header', __( 'Proceed to checkout', 'yith-easy-login-register-popup-for-woocommerce' ) ),
					'password_label'              => get_option( 'yith_welrp_popup_register_password_label', _x( 'Set a password for this account:', 'Input password label', 'yith-easy-login-register-popup-for-woocommerce' ) ),
					'password_placeholder'        => _x( 'Enter password here', 'Input password placeholder', 'yith-easy-login-register-popup-for-woocommerce' ),
					'repeat_password'             => get_option( 'yith_welrp_popup_register_repeat_password', 'no' ) == 'yes',
					'repeat_password_label'       => get_option( 'yith_welrp_popup_register_repeat_password_label', _x( 'Repeat password:', 'Input repeat password label', 'yith-easy-login-register-popup-for-woocommerce' ) ),
					'repeat_password_placeholder' => _x( 'Enter password again', 'Input repeat password placeholder', 'yith-easy-login-register-popup-for-woocommerce' ),
					'policy_enabled'              => get_option( 'yith_welrp_popup_register_policy_enabled', 'no' ) == 'yes',
					'policy_label'                => wc_replace_policy_page_link_placeholders( get_option( 'yith_welrp_popup_register_policy_label', __( 'I have read and accepted your [terms] and [privacy_policy]', 'yith-easy-login-register-popup-for-woocommerce' ) ) ),
					'policy_checked'              => get_option( 'yith_welrp_popup_register_policy_checked', 'no' ) == 'yes',
					'enabled_reCaptcha'           => get_option( 'yith_welrp_popup_register_enable_recaptcha', 'no' ) == 'yes',
				] ),
				WC()->template_path() . '/yith-welrp',
				YITH_WELRP_TEMPLATE_PATH
			);
		}

		/**
		 * Lost password template part
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return void
		 */
		public function lost_password_section_template() {
			wc_get_template(
				'lost-password-section.php',
				apply_filters( 'yith_welrp_lost_password_section_template_args', [
					'header'                      => get_option( 'yith_welrp_popup_lost_password_header', __( 'Recover your password', 'yith-easy-login-register-popup-for-woocommerce' ) ),
					'title'                       => yith_welrp_replace_placeholder( get_option( 'yith_welrp_popup_lost_password_title', __( 'Lost your password?', 'yith-easy-login-register-popup-for-woocommerce' ) ) ),
					'button_label'                => get_option( 'yith_welrp_popup_lost_password_button_label', __( 'Reset password', 'yith-easy-login-register-popup-for-woocommerce' ) ),
					'message'                     => yith_welrp_replace_placeholder( get_option( 'yith_welrp_popup_lost_password_message', __( 'Don\'t worry. Enter the email address of your account.', 'yith-easy-login-register-popup-for-woocommerce' ) ) ),
					'prefill_input'               => get_option( 'yith_welrp_popup_lost_password_prefill', 'no' ) == 'yes',
					'send_auth'                   => get_option( 'yith_welrp_popup_lost_password_authentication_send_again', 'no' ) == 'yes',
					'send_auth_label'             => get_option( 'yith_welrp_popup_lost_password_authentication_send_again_text', __( 'Not received the code? Resend it', 'yith-easy-login-register-popup-for-woocommerce' ) ),
					'send_email_label'            => get_option( 'yith_welrp_popup_lost_password_resend_email_text', __( 'Resend email', 'yith-easy-login-register-popup-for-woocommerce' ) ),
					'repeat_password'             => get_option( 'yith_welrp_popup_lost_password_set_repeat_password', 'no' ) == 'yes',
					'login_label'                 => get_option( 'yith_welrp_popup_lost_password_input_label', _x( 'E-mail address or username:', 'User login input label', 'yith-easy-login-register-popup-for-woocommerce' ) ),
					'login_placeholder'           => $this->get_placeholder(),
					'code_input_label'            => get_option( 'yith_welrp_popup_lost_password_authentication_input_label', _x( 'Enter code:', 'Autheticate lost password action input label', 'yith-easy-login-register-popup-for-woocommerce' ) ),
					'code_input_placeholder'      => _x( 'Enter code here', 'Autheticate lost password action input placeholder', 'yith-easy-login-register-popup-for-woocommerce' ),
					'password_label'              => get_option( 'yith_welrp_popup_lost_password_set_input_label', _x( 'New password:', 'Set new password action input label', 'yith-easy-login-register-popup-for-woocommerce' ) ),
					'password_placeholder'        => _x( 'Enter password here', 'Input password placeholder', 'yith-easy-login-register-popup-for-woocommerce' ),
					'repeat_password_label'       => get_option( 'yith_welrp_popup_lost_password_set_repeat_input_label', _x( 'Repeat password:', 'Repeat new password action input label', 'yith-easy-login-register-popup-for-woocommerce' ) ),
					'repeat_password_placeholder' => _x( 'Enter password here again', 'Input repeat password placeholder', 'yith-easy-login-register-popup-for-woocommerce' ),
				] ),
				WC()->template_path() . '/yith-welrp',
				YITH_WELRP_TEMPLATE_PATH
			);
		}

		/**
		 * Get input correct placeholder
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return string
		 */
		protected function get_placeholder() {
			return get_option( 'yith_welrp_allow_username', 'no' ) == 'yes'
				? _x( 'Enter your email or username here', 'Input email placeholder', 'yith-easy-login-register-popup-for-woocommerce' )
				: _x( 'Enter email here', 'Input email placeholder', 'yith-easy-login-register-popup-for-woocommerce' );
		}
	}
}
