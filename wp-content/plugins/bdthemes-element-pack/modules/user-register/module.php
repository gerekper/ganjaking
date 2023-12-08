<?php
	
	namespace ElementPack\Modules\UserRegister;
	
	use ElementPack\Base\Element_Pack_Module_Base;
	
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	} // Exit if accessed directly
	
	class Module extends Element_Pack_Module_Base {
		
		public function get_name() {
			return 'user-register';
		}
		
		public function get_widgets() {
			
			$widgets = [
				'User_Register',
			];
			
			return $widgets;
		}
		
		/**
		 * Validates and then completes the new user signup process if all went well.
		 *
		 * @param string $email The new user's email address
		 * @param string $first_name The new user's first name
		 * @param string $last_name The new user's last name
		 *
		 * @return \WP_Error         The id of the user that was created, or error if failed.
		 */
		protected function element_pack_register_user( $email, $password, $is_password_required, $first_name, $last_name ) {
			$errors = new \WP_Error();
			
			// Email address is used as both username and email. It is also the only
			// parameter we need to validate
			if ( ! is_email( $email ) ) {
				$errors->add( 'email', __( 'The email address you entered is not valid.', 'bdthemes-element-pack' ) );
				
				return $errors;
			}
			
			if ( username_exists( $email ) || email_exists( $email ) ) {
				$errors->add( 'email_exists', __( 'An account exists with this email address.', 'bdthemes-element-pack' ) );
				
				return $errors;
			}
			
			if ( ! empty( $is_password_required ) && empty( $password ) ) {
				$errors->add( 'empty_password', __( 'Please enter password.', 'bdthemes-element-pack' ) );
				
				return $errors;
			}
			
			/** Recaptcha*/
			$post_id   = (int) $_REQUEST['page_id'];
			$widget_id = (int) $_REQUEST['widget_id'];
			
			$result = $this->get_widget_settings( $post_id, $widget_id );
			if ( isset( $result['show_recaptcha_checker'] ) && $result['show_recaptcha_checker'] == 'yes' ) {
				$gRecaptcha = esc_textarea( $_REQUEST['g-recaptcha-response'] );
				if ( ! apply_filters( 'element_pack_google_recaptcha_validation', $gRecaptcha ) ) {
					$errors->add( 'recaptcha_invalid', __( 'reCAPTCHA is invalid!.', 'bdthemes-element-pack' ) );
					
					return $errors;
				}
				
			}
			
			if ( empty( $is_password_required ) ) {
				$password = wp_generate_password( 12, false );
			}
			
			// Generate the password so that the subscriber will have to check email...
			
			$user_data = array(
				'user_login' => $email,
				'user_email' => $email,
				'user_pass'  => $password,
				'first_name' => $first_name,
				'last_name'  => $last_name,
				'nickname'   => $first_name,
			);
			
			$user_data = apply_filters( 'elementor_pack_user_register_insert_data', $user_data );
			
			
			$result = wp_insert_user( $user_data );
			$notify = 'both';
			
			do_action( 'edit_user_created_user', $result, $notify );
			
			return $result;
		}
		
		/**
		 * Handles the registration of a new user.
		 * @return [type] [description]
		 */
		public function element_pack_do_register_user() {
			
			check_ajax_referer( 'ajax-login-nonce', 'security' );
			
			if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
				if ( ! get_option( 'users_can_register' ) ) {
					// Registration closed, display error
					echo wp_json_encode(
						[
							'registered' => false,
							'message'    => __( 'Registering new users is currently not allowed.', 'bdthemes-element-pack' )
						] );
				} else {
					
					$post_id   = $_REQUEST['page_id'];
					$widget_id = $_REQUEST['widget_id'];
					
					$settings = $this->get_widget_settings( $post_id, $widget_id );
					
					$email                = wp_unslash( $_POST['email'] );
					$password             =  isset($_POST['password']) ? sanitize_text_field( $_POST['password'] ) : NULL  ;
					$is_password_required = sanitize_text_field( $_POST['is_password_required'] );
					$first_name           = sanitize_text_field( $_POST['first_name'] );
					$last_name            = sanitize_text_field( $_POST['last_name'] );
					
					$result = $this->element_pack_register_user( $email, $password, $is_password_required, $first_name, $last_name );
					
					if ( is_wp_error( $result ) ) {
						// Parse errors into a string and append as parameter to redirect
						$errors = $result->get_error_message();
						echo wp_json_encode( [ 'registered' => false, 'message' => $errors ] );
					} else {
						// Success
						$message = sprintf( __( 'You have successfully registered to <strong>%s</strong>.', 'bdthemes-element-pack' ), get_bloginfo( 'name' ) );
						
						
						if ( isset( $settings['auto_login_after_register'] ) && $settings['auto_login_after_register'] == 'yes' ) {
							$user = get_user_by( 'email', $email );
							wp_set_current_user( $user->ID );
							wp_set_auth_cookie( $user->ID );
							
							do_action( 'wp_login', $user->user_login, $user );
						}

						echo wp_json_encode( [ 'registered' => true, 'message' => $message ] );
						die();
						
					}
				}
				exit;
			}
		}
		
		public function __construct() {
			parent::__construct();
			
			add_action( 'wp_ajax_nopriv_element_pack_ajax_register', [ $this, 'element_pack_do_register_user' ] );
		}
	}
