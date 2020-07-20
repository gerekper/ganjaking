<?php
/**
 * Social class
 *
 * @author  YITH
 * @package YITH Easy Login & Register Popup For WooCommerce
 * @version 1.0.0
 */

defined( 'YITH_WELRP' ) || exit; // Exit if accessed directly

if ( ! class_exists( 'YITH_Easy_Login_Register_Social' ) ) {
	/**
	 * YITH Easy Login & Register Popup For WooCommerce
	 * Social Class
	 *
	 * @since 1.0.0
	 */
	class YITH_Easy_Login_Register_Social {

		/**
		 * The App ID
		 *
		 * @since 1.0.0
		 * @var string
		 */
		protected $app_id = '';

		/**
		 * The API request url
		 *
		 * @since 1.0.0
		 * @var string
		 */
		protected $api_url = '';

		/**
		 * The social ID
		 *
		 * @since 1.0.0
		 * @var string
		 */
		protected $social = '';

		/**
		 * Social options
		 *
		 * @since 1.0.0
		 * @var array
		 */
		protected $options = [];

		/**
		 * Ajax popup action
		 *
		 * @since 1.0.0
		 * @var string
		 */
		protected $social_action = 'yith_welrp_social_action';

		/**
		 * Constructor
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return void
		 */
		public function __construct() {

			add_filter( 'yith_welrp_script_data', [ $this, 'maybe_add_social_data' ], 10, 1 );
			add_filter( 'yith_welrp_email_section_template_args', [ $this, 'add_button' ], 10, 1 );
			// handle ajax
			add_action( 'wc_ajax_' . $this->social_action, [ $this, 'handle_social_action' ], 10 );
			add_action( 'wp_ajax_nopriv_' . $this->social_action, [ $this, 'handle_social_action' ], 10 );
			// add custom style
			add_filter( 'yith_welrp_custom_css', [ $this, 'custom_css' ], 10, 1 );
		}

		/**
		 * Maybe add social data to plugin scripts array
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param array $data
		 * @return array
		 */
		public function maybe_add_social_data( $data ) {
			$data['socialAction']            = $data['socialAction'] ?? $this->social_action;
			$data[ $this->social . 'AppID' ] = $this->app_id;
			return $data;
		}

		/**
		 * Add social button in popup
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param array $args
		 * @return $args
		 */
		public function add_button( $args ) {

			$logo = YITH_WELRP_ASSETS_URL . 'images/' . ( defined( 'YITH_PROTEO_VERSION' ) ? "{$this->social}-logo-proteo.svg" : "{$this->social}-logo.svg" );

			$args['social'][ $this->social ] = [
				'icon'  => ! empty( $this->options['icon'] ) ? $this->options['icon'] : $logo,
				'label' => $this->options['button_label'],
			];
			return $args;
		}

		/**
		 * Handle social login ajax request
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return void
		 * @throws Exception
		 */
		public function handle_social_action() {

			try {

				if ( empty( $_REQUEST['token'] ) || empty( $_REQUEST['social'] ) || ! in_array( $_REQUEST['social'], YITH_Easy_Login_Register()->get_available_social() ) ) {
					throw new Exception();
				}

				$social = $_REQUEST['social'];
				if ( $social != $this->social ) { // check the requested social and return if is not the current one
					return;
				}

				$response = [];
				$token    = wc_clean( $_REQUEST['token'] );
				$fn       = "validate_token_{$social}";
				// validate token and get user data on success
				$user_data = $this->$fn( $token );

				extract( $user_data );

				$user = $user_id_social ? get_users(
					[ 'fields' => 'ID', 'meta_query' => [ [ 'key' => 'user_id_' . $social, 'value' => $user_id_social ] ] ]
				) : false;
				$user = ! empty( $user ) ? array_shift( $user ) : false;

				if ( ! $user ) {

					if ( empty( $email ) || ! is_email( $email ) ) {
						throw new Exception( _x( 'An error has occurred! A valid email address is required.', 'Form error message', 'yith-easy-login-register-popup-for-woocommerce' ) );
					}

					// first check if user email still exists
					$user = get_user_by( 'email', $email );
					$user = ( $user && $user->exists() ) ? $user->ID : false;

					if ( ! $user ) {
						$username = wc_create_new_customer_username( $email );
						$password = wp_generate_password();
						$args     = [
							'first_name' => wc_clean( $first_name ),
							'last_name'  => wc_clean( $last_name ),
						];

						// remove standard email
						remove_action( 'woocommerce_created_customer_notification', array( WC_Emails::instance(), 'customer_new_account' ), 10 );

						do_action( 'yith_welrp_before_social_register_action', $email );

						$user = wc_create_new_customer( $email, $username, $password, $args );
						if ( is_wp_error( $user ) ) {
							throw new Exception( $user->get_error_message() );
						}

						do_action( 'yith_welrp_after_social_register_action', $email, $user );
					}

					// save token for future login
					update_user_meta( $user, 'user_id_' . $social, $user_id_social );
				}

				// finally login
				wc_set_customer_auth_cookie( $user );

				wc_add_notice( apply_filters( 'yith_welrp_login_success_notice', __( 'You have successfully logged in.', 'yith-easy-login-register-popup-for-woocommerce' ) ) );
				$response['redirectTo'] = yith_welrp_get_redirect_url_from_posted();

				wp_send_json_success( apply_filters( 'yith_welrp_ajax_form_social_response', $response, $social ) );

			} catch ( Exception $e ) {
				$message = $e->getMessage();
				// if empty get the standard message
				! $message && $message = yith_welrp_get_std_error_message();

				wp_send_json_error( [
					'errorMsg' => $e->getMessage(),
				] );
			}
		}

		/**
		 * Check if given response is valid
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param mixed $response
		 * @return boolean
		 */
		protected function is_valid_response( $response ) {
			return ! is_wp_error( $response ) && ! empty( $response['response']['code'] ) && $response['response']['code'] == '200' && ! empty( $response['body'] );
		}

		/**
		 * Add custom css
		 *
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param string $css
		 * @return string
		 */
		public function custom_css( $css ) {
			// background color
			$css .= "#yith-welrp-{$this->social}-button{background:{$this->options['background_color']['normal']};border-color:{$this->options['border_color']['normal']};color:{$this->options['text_color']['normal']};}
            #yith-welrp-{$this->social}-button:hover{background:{$this->options['background_color']['hover']};border-color:{$this->options['border_color']['hover']};color:{$this->options['text_color']['hover']};}";
			return $css;
		}
	}
}
