<?php
/**
 * Main class
 *
 * @author  YITH
 * @package YITH WooCommerce Social Login
 * @version 1.0.0
 */

if ( ! defined( 'YITH_YWSL_INIT' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WC_Social_Login' ) ) {
	/**
	 * YITH WooCommerce Social Login main class
	 *
	 * @since 1.0.0
	 */
	class YITH_WC_Social_Login {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WC_Social_Login
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Array with accessible variables
		 */
		protected $_data = array();

		/**
		 * Array with config parameters
		 */
		protected $config = array();

		/**
		 * HybridAuth Object
		 */
		protected $hybridauth;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WC_Social_Login
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self;
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			YITH_WC_Social_Login_Session();


			/* plugin */
			add_action( 'plugins_loaded', array( $this, 'plugin_fw_loader' ), 15 );
			require_once( YITH_YWSL_INC . 'hybridauth/Hybrid/Auth.php' );
			require_once( YITH_YWSL_INC . 'hybridauth/vendor/autoload.php' );

			$this->_set_config();
			$this->_set_social_list();
			$this->_set_social_list_enabled();

			add_shortcode( 'yith_wc_social_login', array( $this, 'yith_wc_social_login_shortcode' ) );

			add_action( 'init', array( $this, 'hybrid_auth' ) );
			add_action( 'init', array( $this, 'get_login_request' ) );
			add_action( 'wp_logout', array( $this, 'logout' ), 11 );
		}

		public function hybrid_auth() {
			if ( isset( $_GET['hauth_start'] ) || isset( $_GET['hauth_done'] ) ) {
				require_once( YITH_YWSL_INC . "hybridauth/Hybrid/Auth.php" );
				require_once( YITH_YWSL_INC . "hybridauth/Hybrid/Endpoint.php" );

				Hybrid_Endpoint::process();

			}
		}

		/**
		 * Return a $property defined in this class
		 *
		 * @since   1.0.0
		 * @author  Emanuela Castorina <emanuela.castorina@yithemes.com>
		 * @return  mix
		 */
		public function __get( $property ) {
			if ( isset( $this->_data[ $property ] ) ) {
				return $this->_data[ $property ];
			}
		}

		/**
		 * Load YIT Plugin Framework
		 *
		 * @since  1.0.0
		 * @return void
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if ( ! empty( $plugin_fw_data ) ) {
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}
		}

		/**
		 * Set the configuration array for Hybrid Class
		 *
		 * @since  1.0.0
		 * @return void
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		private function _set_config() {
			$this->config = include( YITH_YWSL_DIR . '/plugin-options/config.php' );

			//print_r($this->config );
		}

		/**
		 * Set an array with the social list
		 *
		 * @since  1.0.0
		 * @return void
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		private function _set_social_list() {
			$social_list = include( YITH_YWSL_DIR . '/plugin-options/socials.php' );

			if ( get_option( 'ywsl_social_networks' ) ) {
				$social_list = array_merge( array_flip( get_option( 'ywsl_social_networks' ) ), $social_list );
			}

			$this->_data['social_list'] = $social_list;
		}

		/**
		 * Main function to login with social providers
		 *
		 * @since  1.0.0
		 * @return void
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function get_login_request() {

			if ( isset( $_REQUEST['ywsl_social'] ) && isset( $this->_data['social_list'] [ $_REQUEST['ywsl_social'] ] ) ) {

				$social      = $_REQUEST['ywsl_social'];
				$social_name = $this->_data['social_list'] [ $_REQUEST['ywsl_social'] ]['label'];

				// HOOKABLE:
				do_action( "ywsl_process_login_start" );

				if ( ! isset( $this->config['providers'][ $social_name ] ) || get_option( 'ywsl_' . $social . '_enable' ) != 'yes' ) {
					return;
				}

				if ( $_REQUEST['ywsl_social'] == 'facebook' && ! ywsl_check_wpengine() ) {
					$this->config['base_url'] = YITH_YWSL_URL . 'includes/hybridauth/';
				}

				if ( $_REQUEST['ywsl_social'] == 'twitter' ) {
					$this->config['base_url'] = YITH_YWSL_URL . 'includes/hybridauth/';
				}

				$this->config = apply_filters( 'ywsl_alter_config', $this->config, $_REQUEST['ywsl_social'] );

				try {
					$this->hybridauth = new Hybrid_Auth( $this->config );
				} catch ( Exception $e ) {
					wp_redirect( $this->get_redirect_to() );
					exit;
				}

				if ( is_null( $this->hybridauth ) ) {
					wp_redirect( $this->get_redirect_to() );
					exit;
				}

				try {
					$adapter      = $this->hybridauth->authenticate( $social_name );
					$user_profile = $adapter->getUserProfile();
				} catch ( Exception $e ) {
					$this->hybridauth->logoutAllProviders();
					exit;
				}

				$registration_check = $this->verify_user( $social, $user_profile->identifier );
				$hyb_email          = sanitize_email( $user_profile->email );
				$hyb_user_login     = sanitize_user( $user_profile->displayName, true );
				$hyb_user_avatar    = $user_profile->photoURL;

				if ( is_user_logged_in() ) {
					$current_user        = wp_get_current_user();
					$current_customer_id = $current_user->ID;
					//link account
					add_user_meta( $current_customer_id, $social . '_login_id', $user_profile->identifier, true );
					add_user_meta( $current_customer_id, $social . '_login_data', (array) $user_profile, true );

					wp_redirect( $this->get_redirect_to() );
					exit;
				}


				if ( $registration_check ) {
					//registration with this provider exists
					wp_set_auth_cookie( $registration_check, true );
					wp_redirect( $this->get_redirect_to() );
					exit;
				} elseif ( $current_customer_id = $this->verify_email_exists( $hyb_email ) ) {

					//link account
					add_user_meta( $current_customer_id, $social . '_login_id', $user_profile->identifier, true );
					add_user_meta( $current_customer_id, $social . '_login_data', (array) $user_profile, true );
					$this->add_user_meta( $current_customer_id, $user_profile, $hyb_email );
					wp_set_auth_cookie( $current_customer_id, true );
					wp_redirect( $this->get_redirect_to() );
					exit;

				} else {

					$yith_user_login = $this->get_username( $hyb_user_login, $hyb_email );
					$yith_user_email = $this->get_email( $hyb_email );


					$yith_user_login_validate = validate_username( $yith_user_login );
					$yith_user_email_validate = filter_var( $yith_user_email, FILTER_VALIDATE_EMAIL );


					if ( empty( $yith_user_login ) ) {
						$yith_user_login_validate = false;
					}
					if ( empty( $yith_user_email ) ) {
						$yith_user_email_validate = false;
					}


					$show_form        = false;
					$show_email       = false;
					$show_username    = false;
					$show_form_errors = array();

					if ( ! $yith_user_email && ! is_user_logged_in() ) {
						$show_form          = true;
						$show_email         = true;
						$show_form_errors[] = __( 'Add your email address', 'yith-woocommerce-social-login' );
					}

					if ( $yith_user_email && ! $yith_user_email_validate ) {
						$show_form          = true;
						$show_email         = true;
						$show_form_errors[] = __( 'Your email address is not valid!', 'yith-woocommerce-social-login' );
					}

					if ( $yith_user_email_validate && $this->verify_email_exists( $yith_user_email ) && ! is_user_logged_in() ) {
						$show_form          = true;
						$show_email         = true;
						$show_form_errors[] = __( 'This email already exists', 'yith-woocommerce-social-login' );
					}

					if ( ! $yith_user_login || ! $yith_user_login_validate ) {
						$show_form          = true;
						$show_username      = true;
						$show_form_errors[] = __( 'Username is not valid!', 'yith-woocommerce-social-login' );
					}


					if ( $show_form ) {
						$args = array(
							'errors'     => $show_form_errors,
							'avatar'     => $hyb_user_avatar,
							'show_user'  => $show_username,
							'show_email' => $show_email,
							'provider'   => $social,
							'redirect'   => $this->get_redirect_to()
						);


						yit_plugin_get_template( YITH_YWSL_DIR, 'request-info.php', $args );
						exit;

					} else {

						if ( apply_filters( 'ywpop_enable_the_registration', true ) ) {
							if ( ! is_user_logged_in() ) {
								if ( username_exists( $yith_user_login ) ) {
									$yith_user_login = $this->get_username( $yith_user_login, $yith_user_email );
								}

								if ( apply_filters( 'ywsl_enable_new_users', true ) ) {
									$current_customer_id = $this->add_user( $yith_user_login, $yith_user_email, $user_profile );
								}

							}

							//link account
							add_user_meta( $current_customer_id, $social . '_login_id', $user_profile->identifier, true );
							add_user_meta( $current_customer_id, $social . '_login_data', (array) $user_profile, true );

							wp_set_auth_cookie( $current_customer_id, true );
						}

						wp_redirect( $this->get_redirect_to() );
						exit;

					}

				}

			}
		}

		/**
		 * Return the username of user
		 *
		 * @since  1.0.0
		 * @return string
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		function get_username( $hyb_user_login, $hyb_user_email ) {

			$yith_user_login = isset( $_REQUEST["yith_user_login"] ) ? $_REQUEST["yith_user_login"] : $hyb_user_login;

			if ( ! empty( $yith_user_login ) ) {
				if ( get_option( 'woocommerce_registration_generate_username' ) == 'yes' && ! empty( $hyb_user_email ) ) {
					$yith_user_login = sanitize_user( current( explode( '@', $hyb_user_email ) ) );
					if ( username_exists( $yith_user_login ) ) {
						$append     = 1;
						$o_username = $yith_user_login;

						while ( username_exists( $yith_user_login ) ) {
							$yith_user_login = $o_username . $append;
							$append ++;
						}
					}
				}
			} else {
				$yith_user_login = sanitize_user( $hyb_user_login, true );
				$yith_user_login = trim( str_replace( array( ' ', '.' ), '_', $yith_user_login ) );
				$yith_user_login = trim( str_replace( '__', '_', $yith_user_login ) );
			}

			return apply_filters( 'yith_social_login_get_username', $yith_user_login, $hyb_user_login, $hyb_user_email );

		}

		/**
		 * Return the email of user
		 *
		 * @since  1.0.0
		 * @return string
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		function get_email( $hyb_user_email ) {

			$yith_user_email = isset( $_REQUEST["yith_user_email"] ) ? $_REQUEST["yith_user_email"] : '';

			if ( empty( $yith_user_email ) ) {
				$yith_user_email = $hyb_user_email;
			} else {
				$yith_user_email = sanitize_email( $yith_user_email );
			}

			return $yith_user_email;

		}

		/**
		 * Check if the customer has a connection with the provider
		 *
		 * @since  1.0.0
		 * @return string
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function verify_user( $social, $identifier ) {
			global $wpdb;

			$query = $wpdb->prepare( 'SELECT user_id FROM ' . $wpdb->usermeta . ' WHERE meta_key = "%s" AND  meta_value= "%s"', $social . '_login_id', $identifier );

			$user_id = $wpdb->get_var( $query );
			if ( $user_id ) {
				return $user_id;
			} else {
				return false;
			}

		}

		/**
		 * Check if exists an user with an email like $user_email
		 *
		 * @since  1.0.0
		 * @return string
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function verify_email_exists( $user_email ) {
			global $wpdb;
			$query   = $wpdb->prepare( 'SELECT ID FROM ' . $wpdb->users . ' WHERE user_email = "%s"', $user_email );
			$user_id = $wpdb->get_var( $query );
			if ( $user_id ) {
				return $user_id;
			} else {
				return false;
			}
		}

		/**
		 * Add a new user
		 *
		 * @since  1.0.0
		 * @return string
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_user( $username, $user_email, $user_info ) {

			$password    = wp_generate_password();
			$args        = array(
				'user_login' => $username,
				'user_pass'  => $password,
				'user_email' => $user_email,
				'role'       => apply_filters( 'ywsl_new_user_role', 'customer' )
			);
			$customer_id = wp_insert_user( $args );

			$this->add_user_meta( $customer_id, $user_info, $user_email );

			if ( apply_filters( 'ywsl_send_admin_notification', false ) ) {
				wp_new_user_notification( $customer_id, null, 'admin' );
			}

			//do_action( 'user_register', $customer_id );
			do_action( 'woocommerce_created_customer', $customer_id, $args, $password );

			return $customer_id;
		}

		/**
		 * Add meta to user from provider's user info
		 *
		 * @since  1.0.0
		 * @return string
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_user_meta( $user_id, $user_info, $user_email ) {

			if ( get_user_meta( $user_id, 'billing_email', true ) == '' ) {
				update_user_meta( $user_id, 'billing_email', $user_email );
			}

			if ( isset( $user_info->description ) && $user_info->description == '' ) {
				update_user_meta( $user_id, 'description', $user_info->description );
			}

			if ( isset( $user_info->firstName ) ) {
				if ( get_user_meta( $user_id, 'first_name', true ) == '' ) {
					update_user_meta( $user_id, 'first_name', $user_info->firstName );
				}

				if ( get_user_meta( $user_id, 'billing_first_name', true ) == '' ) {
					update_user_meta( $user_id, 'billing_first_name', $user_info->firstName );
				}

				if ( get_user_meta( $user_id, 'shipping_first_name', true ) == '' ) {
					update_user_meta( $user_id, 'shipping_first_name', $user_info->firstName );
				}

			}
			if ( isset( $user_info->lastName ) ) {
				if ( get_user_meta( $user_id, 'last_name', true ) == '' ) {
					update_user_meta( $user_id, 'last_name', $user_info->lastName );
				}

				if ( get_user_meta( $user_id, 'billing_last_name', true ) == '' ) {
					update_user_meta( $user_id, 'billing_last_name', $user_info->lastName );
				}

				if ( get_user_meta( $user_id, 'shipping_last_name', true ) == '' ) {
					update_user_meta( $user_id, 'shipping_last_name', $user_info->lastName );
				}

			}
			if ( isset( $user_info->phone ) && get_user_meta( $user_id, 'billing_phone', true ) == '' ) {
				update_user_meta( $user_id, 'billing_phone', $user_info->phone );
			}
			if ( isset( $user_info->address ) ) {

				if ( get_user_meta( $user_id, 'billing_address_1', true ) == '' ) {
					update_user_meta( $user_id, 'billing_address_1', $user_info->address );
				}

				if ( get_user_meta( $user_id, 'shipping_address_1', true ) == '' ) {
					update_user_meta( $user_id, 'shipping_address_1', $user_info->address );
				}
			}
			if ( isset( $user_info->country ) ) {

				if ( get_user_meta( $user_id, 'billing_country', true ) == '' ) {
					update_user_meta( $user_id, 'billing_country', $user_info->country );
				}

				if ( get_user_meta( $user_id, 'shipping_country', true ) == '' ) {
					update_user_meta( $user_id, 'shipping_country', $user_info->country );
				}

			}
			if ( isset( $user_info->region ) ) {

				if ( get_user_meta( $user_id, 'billing_state', true ) == '' ) {
					update_user_meta( $user_id, 'billing_state', $user_info->region );
				}

				if ( get_user_meta( $user_id, 'shipping_state', true ) == '' ) {
					update_user_meta( $user_id, 'shipping_state', $user_info->region );
				}

			}
			if ( isset( $user_info->city ) ) {

				if ( get_user_meta( $user_id, 'billing_city', true ) == '' ) {
					update_user_meta( $user_id, 'billing_city', $user_info->city );
				}

				if ( get_user_meta( $user_id, 'shipping_city', true ) == '' ) {
					update_user_meta( $user_id, 'shipping_city', $user_info->city );
				}

			}
			if ( isset( $user_info->zip ) ) {

				if ( get_user_meta( $user_id, 'billing_postcode', true ) == '' ) {
					update_user_meta( $user_id, 'billing_postcode', $user_info->zip );
				}

				if ( get_user_meta( $user_id, 'shipping_postcode', true ) == '' ) {
					update_user_meta( $user_id, 'shipping_postcode', $user_info->zip );
				}

			}

			do_action( 'ywsl_add_additional_user_info', $user_id, $user_info, $user_email );

		}

		/**
		 * Return the string of error
		 *
		 * @since  1.0.0
		 * @return string
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function get_error( $e_code ) {
			$error = '';
			switch ( $e_code ) {
				case 0 :
					$error = __( 'Unspecified error.', 'yith-woocommerce-social-login' );
					break;
				case 1 :
					$error = __( 'Hybriauth configuration error.', 'yith-woocommerce-social-login' );
					break;
				case 2 :
					$error = __( 'Provider not properly configured.', 'yith-woocommerce-social-login' );
					break;
				case 3 :
					$error = __( 'Unknown or disabled provider.', 'yith-woocommerce-social-login' );
					break;
				case 4 :
					$error = __( 'Missing provider application credentials.', 'yith-woocommerce-social-login' );
					break;
				case 5 :
					$error = __( 'Authentification failed. The user has canceled the authentication or the provider refused the connection.', 'yith-woocommerce-social-login' );
					break;
				case 6 :
					$error = __( 'Request of user profile failed. Probably the user is not connected to the provider and a new authentication is necessary.', 'yith-woocommerce-social-login' );
					break;
				case 7 :
					$error = __( 'User not connected to the provider.', 'yith-woocommerce-social-login' );
					break;
				case 8 :
					$error = __( 'Provider does not support this feature.', 'yith-woocommerce-social-login' );
					break;
			}

			return $error;
		}

		/**
		 * Return the page to redirect the user
		 *
		 * @since  1.0.0
		 * @return string
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function get_redirect_to() {

			$redirect_to = site_url();

			// get a valid $redirect_to
			if ( isset( $_REQUEST['redirect'] ) && $_REQUEST['redirect'] != '' ) {
				$redirect_to_url = $_REQUEST['redirect'];

				if ( ! ( strpos( $redirect_to_url, 'wp-admin' ) || strpos( $redirect_to_url, 'wp-login.php' ) ) ) {
					$redirect_to = $redirect_to_url;
					// Redirect to https if user wants ssl
					if ( is_ssl() && false !== strpos( $redirect_to, 'wp-admin' ) ) {
						$redirect_to = preg_replace( '|^http://|', 'https://', $redirect_to );
					}
				}

			} else {
				$redirect_to = ywsl_curPageURL();

			}

			$redirect_to = str_replace( '#_=_', '', $redirect_to );


			return apply_filters( 'ywsl_redirect_to_after_login', $redirect_to );
		}

		/**
		 * Set the social providers enabled
		 *
		 * @since  1.0.0
		 * @return void
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		private function _set_social_list_enabled() {
			$enabled_social = array();
			foreach ( $this->social_list as $key => $value ) {
				$enabled = get_option( 'ywsl_' . $key . '_enable' );
				if ( $enabled == 'yes' ) {
					$enabled_social[ $key ] = $value;
				}
			}

			$this->_data['enabled_social'] = $enabled_social;
		}

		/**
		 * Print the Social Login Buttons
		 *
		 * @since  1.0.0
		 * @return string
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function yith_wc_social_login_shortcode( $atts ) {
			return YITH_WC_Social_Login_Frontend()->social_buttons( '', true );
		}

		/**
		 * Return if a provider is enabled
		 *
		 * @since  1.0.0
		 * @return string
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function is_enabled( $provider ) {
			$enabled_list = $this->enabled_social;

			return isset( $enabled_list[ $provider ] );
		}

		/**
		 * Return the base url for the library hybrid
		 *
		 * @since  1.0.8
		 * @return string
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function get_base_url() {

			if ( get_option( 'ywsl_callback_url' ) == 'root' ) {
				$url = site_url();
				$url = substr( $url, - 1 ) === '/' ? $url : $url . '/';

			} else {
				$url = YITH_YWSL_URL . 'includes/hybridauth/';
			}

			return $url;
		}


		/**
		 * Clear the session at logout
		 *
		 * @since  1.0.9
		 * @return void
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function logout() {
			if ( isset( $_SESSION ) ) {
				@session_destroy();
			}
			clearstatcache();
			unset( $this->hybridauth );

		}

	}

	/**
	 * Unique access to instance of YITH_WC_Social_Login class
	 *
	 * @return \YITH_WC_Social_Login
	 */
	function YITH_WC_Social_Login() {
		return YITH_WC_Social_Login::get_instance();
	}

}

