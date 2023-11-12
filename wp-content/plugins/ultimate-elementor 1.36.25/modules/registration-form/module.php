<?php
/**
 * UAEL Registration Form Module.
 *
 * @package UAEL
 */

namespace UltimateElementor\Modules\RegistrationForm;

use UltimateElementor\Base\Module_Base;
use UltimateElementor\Classes\UAEL_Helper;
use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Class Module.
 */
class Module extends Module_Base {

	/**
	 * Module should load or not.
	 *
	 * @since 1.18.0
	 * @access public
	 *
	 * @return bool true|false.
	 */
	public static function is_enable() {
		return true;
	}

	/**
	 * Get Module Name.
	 *
	 * @since 1.18.0
	 * @access public
	 *
	 * @return string Module name.
	 */
	public function get_name() {
		return 'uael-registration-form';
	}

	/**
	 * Get Widgets.
	 *
	 * @since 1.18.0
	 * @access public
	 *
	 * @return array Widgets.
	 */
	public function get_widgets() {
		return array(
			'RegistrationForm',
		);
	}

	/**
	 * Member Variable
	 *
	 * @var array mail content
	 */
	private static $email_content = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();

		add_action( 'wp_ajax_uael_register_user', array( $this, 'get_form_data' ) );
		add_action( 'wp_ajax_nopriv_uael_register_user', array( $this, 'get_form_data' ) );
		add_filter( 'wp_new_user_notification_email', array( $this, 'custom_wp_new_user_notification_email' ), 10, 3 );

		add_action( 'show_user_profile', array( $this, 'show_user_extra_field' ) );
		add_action( 'edit_user_profile', array( $this, 'show_user_extra_field' ) );
		add_action( 'personal_options_update', array( $this, 'update_user_profile' ) );
		add_action( 'edit_user_profile_update', array( $this, 'update_user_profile' ) );
		if ( ! current_user_can( 'manage_options' ) ) {
			add_filter(
				'elementor/document/save/data',
				function ( $data ) {
					if ( isset( $data['elements'] ) ) {
						$data['elements'] = Plugin::$instance->db->iterate_data(
							$data['elements'],
							function ( $element ) {
								if ( isset( $element['widgetType'] ) && 'uael-registration-form' === $element['widgetType'] ) {
									if ( ! empty( $element['settings']['select_role'] ) ) {
										$element['settings']['select_role'] = 'default';
									}
								}

								return $element;
							}
						);
					}

					return $data;
				}
			);
		}

	}

	/**
	 * Show extra phone field on user profile page.
	 *
	 * @since 1.30.0
	 * @param object $user WP_User object.
	 * @access public
	 */
	public static function show_user_extra_field( $user ) {
		$phone = get_user_meta( $user->ID, 'phone', true );
		if ( empty( $phone ) ) {
			return;
		}
		?>
		<h3><?php echo esc_html__( 'Extra profile information', 'uael' ); ?></h3>
		<table class="form-table">
			<tr>
				<th><label for="phone"><?php echo esc_html__( 'Phone Number', 'uael' ); ?></label></th>
				<td>
					<input type="text" name="phone" id="phone" value="<?php echo esc_attr( $phone ); ?>" class="regular-text" placeholder="<?php echo esc_attr( 'Enter your phone number' ); ?>" /><br />
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Update extra phone field on user profile page.
	 *
	 * @since 1.30.0
	 * @param int $user_id WP_User object.
	 * @access public
	 */
	public function update_user_profile( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}

		if ( ! empty( $_POST['phone'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Missing
			update_user_meta( $user_id, 'phone', intval( $_POST['phone'] ) ); //phpcs:ignore WordPress.Security.NonceVerification.Missing
		}
	}

	/**
	 * Modify user notification email.
	 *
	 * @since 1.18.0
	 * @param array  $wp_new_user_notification_email email content.
	 * @param string $user user name.
	 * @param string $blogname email blogname.
	 * @access public
	 */
	public function custom_wp_new_user_notification_email( $wp_new_user_notification_email, $user, $blogname ) {

		if ( array_key_exists( 'template_type', self::$email_content ) && 'custom' === self::$email_content['template_type'] ) {

			$wp_new_user_notification_email['subject'] = sprintf( self::$email_content['subject'], $blogname, $user->user_login );

			$message = self::$email_content['message'];

			$find = array( '/\[field=password\]/', '/\[field=username\]/', '/\[field=email\]/', '/\[field=first_name\]/', '/\[field=last_name\]/' );

			$replacement = array( self::$email_content['pass'], self::$email_content['user_login'], self::$email_content['user_email'], self::$email_content['first_name'], self::$email_content['last_name'] );

			if ( isset( self::$email_content['pass'] ) ) {
				$message = preg_replace( $find, $replacement, $message );
			}

			$wp_new_user_notification_email['message'] = $message;

			$wp_new_user_notification_email['headers'] = self::$email_content['headers'];
		}

		return $wp_new_user_notification_email;

	}

	/**
	 * Get Form Data via AJAX call.
	 *
	 * @since 1.18.0
	 * @access public
	 */
	public function get_form_data() {

		check_ajax_referer( 'uael_register_user', 'nonce' );

		$data             = array();
		$error            = array();
		$response         = array();
		$allow_register   = get_option( 'users_can_register' );
		$is_widget_active = UAEL_Helper::is_widget_active( 'RegistrationForm' );

		if ( isset( $_POST['data'] ) && $allow_register && true === $is_widget_active ) {

			$data = array_map( 'sanitize_text_field', $_POST['data'] );

			if ( isset( $data['is_recaptcha_enabled'] ) ) {
				if ( 'yes' === sanitize_text_field( $data['is_recaptcha_enabled'] ) ) {
					$recaptcha_token = sanitize_text_field( $data['recaptcha_token'] );
					if ( empty( $recaptcha_token ) ) {
						$error['recaptcha'] = __( 'The Captcha field cannot be blank. Please enter a value.', 'uael' );
					}

					$recaptcha_errors = array(
						'missing-input-secret'   => __( 'The secret parameter is missing.', 'uael' ),
						'invalid-input-secret'   => __( 'The secret parameter is invalid or malformed.', 'uael' ),
						'missing-input-response' => __( 'The response parameter is missing.', 'uael' ),
						'invalid-input-response' => __( 'The response parameter is invalid or malformed.', 'uael' ),
					);

					$recaptcha_response = $recaptcha_token;
					$integration_option = UAEL_Helper::get_integrations_options();
					$recaptcha_secret   = $integration_option['recaptcha_v3_secretkey'];
					$client_ip          = UAEL_Helper::get_client_ip();
					$recaptcha_score    = $integration_option['recaptcha_v3_score'];
					if ( 0 > $recaptcha_score || 1 < $recaptcha_score ) {
						$recaptcha_score = 0.5;
					}

					$request = array(
						'body' => array(
							'secret'   => $recaptcha_secret,
							'response' => $recaptcha_response,
							'remoteip' => $client_ip,
						),
					);

					$response = wp_remote_post( 'https://www.google.com/recaptcha/api/siteverify', $request );

					$response_code = wp_remote_retrieve_response_code( $response );

					if ( 200 !== (int) $response_code ) {
						/* translators: %d admin link */
						$error['recaptcha'] = sprintf( __( 'Can not connect to the reCAPTCHA server (%d).', 'uael' ), $response_code );
					} else {
						$body   = wp_remote_retrieve_body( $response );
						$result = json_decode( $body, true );

						$action = ( ( isset( $result['action'] ) && 'Form' === $result['action'] ) && ( $result['score'] > $recaptcha_score ) );

						if ( ! $result['success'] ) {
							if ( ! $action ) {
								$message = __( 'Invalid Form - reCAPTCHA validation failed', 'uael' );

								if ( isset( $result['error-codes'] ) ) {
									$result_errors = array_flip( $result['error-codes'] );

									foreach ( $recaptcha_errors as $error_key => $error_desc ) {
										if ( isset( $result_errors[ $error_key ] ) ) {
											$message = $recaptcha_errors[ $error_key ];
											break;
										}
									}
								}
								$error['recaptcha'] = $message;
							}
						}
					}
				}
			}

			$post_id   = $data['page_id'];
			$widget_id = $data['widget_id'];

			$elementor = \Elementor\Plugin::$instance;
			$meta      = $elementor->documents->get( $post_id )->get_elements_data();

			$widget_data = $this->find_element_recursive( $meta, $widget_id );

			$widget = $elementor->elements_manager->create_element_instance( $widget_data );

			$settings = $widget->get_settings();

			if ( 'both' === $data['send_email'] && 'custom' === $settings['email_template'] ) {
				self::$email_content['subject'] = $settings['email_subject'];
				self::$email_content['message'] = $settings['email_content'];
				self::$email_content['headers'] = 'Content-Type: text/' . $settings['email_content_type'] . '; charset=UTF-8' . "\r\n";
			}

			self::$email_content['template_type'] = $settings['email_template'];

			$user_role = ( 'default' !== $settings['select_role'] && ! empty( $settings['select_role'] ) ) ? $settings['select_role'] : get_option( 'default_role' );

			/* Checking Email address. */
			if ( isset( $data['email'] ) && ! is_email( $data['email'] ) ) {

				$error['email'] = __( 'The email address is incorrect.', 'uael' );

			} elseif ( email_exists( $data['email'] ) ) {

				$error['email'] = __( 'An account is already registered with your email address. Please choose another one.', 'uael' );
			}

			/* Checking User name. */
			if ( isset( $data['user_name'] ) && ! empty( $data['user_name'] ) && ! validate_username( $data['user_name'] ) ) {

				$error['user_name'] = __( 'This username is invalid because it uses illegal characters. Please enter a valid username.', 'uael' );

			} elseif ( isset( $data['user_name'] ) && ( mb_strlen( $data['user_name'] ) > 60 ) && validate_username( $data['user_name'] ) ) {

				$error['user_name'] = __( 'Username may not be longer than 60 characters.', 'uael' );
			} elseif ( isset( $data['user_name'] ) && username_exists( $data['user_name'] ) ) {

				$error['user_name'] = __( 'This username is already registered. Please choose another one.', 'uael' );

			} elseif ( isset( $data['user_name'] ) && ! empty( $data['user_name'] ) ) {

				/** This Filters the list of blacklisted usernames. */
				$illegal_logins = (array) apply_filters( 'uael_illegal_user_logins', array() );

				if ( in_array( strtolower( $data['user_name'] ), array_map( 'strtolower', $illegal_logins ), true ) ) {
					$error['user_login'] = __( 'Sorry, that username is not allowed.', 'uael' );
				}
			}

			/* Get username from e-mail address */
			if ( ! isset( $data['user_name'] ) || empty( $data['user_name'] ) ) {
				$email_username    = $this->uael_create_username( $data['email'], '' );
				$data['user_name'] = sanitize_user( $email_username );
			}

			// Handle password creation.
			$password_generated = false;
			$user_pass          = '';
			if ( ! isset( $data['password'] ) && empty( $data['password'] ) ) {
				$user_pass          = wp_generate_password();
				$password_generated = true;
			} else {
				/* Checking User Password. */
				if ( false !== strpos( wp_unslash( $data['password'] ), '\\' ) ) {
					$error['password'] = __( 'Password may not contain the character "\\"', 'uael' );
				} else {
					$user_pass = $data['password'];
				}
			}

			$user_login = ( isset( $data['user_name'] ) && ! empty( $data['user_name'] ) ) ? sanitize_user( $data['user_name'], true ) : '';
			$user_email = ( isset( $data['email'] ) && ! empty( $data['email'] ) ) ? sanitize_text_field( wp_unslash( $data['email'] ) ) : '';

			$first_name = ( isset( $data['first_name'] ) && ! empty( $data['first_name'] ) ) ? sanitize_text_field( wp_unslash( $data['first_name'] ) ) : '';

			$last_name = ( isset( $data['last_name'] ) && ! empty( $data['last_name'] ) ) ? sanitize_text_field( wp_unslash( $data['last_name'] ) ) : '';

			$phone = ( isset( $data['phone'] ) && ! empty( $data['phone'] ) ) ? sanitize_text_field( wp_unslash( $data['phone'] ) ) : '';

			if ( ! empty( $error ) ) {

				// If there are items in our errors array, return those errors.
				$response['success'] = false;
				$response['error']   = $error;

			} else {

				self::$email_content['user_login'] = $user_login;
				self::$email_content['user_email'] = $user_email;
				self::$email_content['first_name'] = $first_name;
				self::$email_content['last_name']  = $last_name;

				$user_args = apply_filters(
					'uael_register_insert_user_args',
					array(
						'user_login'      => isset( $user_login ) ? $user_login : '',
						'user_pass'       => isset( $user_pass ) ? $user_pass : '',
						'user_email'      => isset( $user_email ) ? $user_email : '',
						'first_name'      => isset( $first_name ) ? $first_name : '',
						'last_name'       => isset( $last_name ) ? $last_name : '',
						'user_registered' => gmdate( 'Y-m-d H:i:s' ),
						'role'            => isset( $user_role ) ? $user_role : '',
						'phone'           => isset( $phone ) ? $phone : '',
					),
					$data
				);

				$phone_val = $user_args['phone'];

				if ( 'administrator' === $user_args['role'] ) {
					$user_args['role'] = get_option( 'default_role' );
				}

				unset( $user_args['phone'] );

				$result = wp_insert_user( $user_args );

				if ( ! is_wp_error( $result ) ) {
					update_user_meta( $result, 'phone', $phone_val );
				}

				if ( ! is_wp_error( $result ) ) {
					// show a message of success and provide a true success variable.
					$response['success'] = true;
					$response['message'] = __( 'successfully registered', 'uael' );

					$notify = $data['send_email'];

					/* Login user after registration and redirect to home page if not currently logged in */
					if ( ! is_user_logged_in() && 'yes' === $data['auto_login'] ) {
						$creds                  = array();
						$creds['user_login']    = $user_login;
						$creds['user_password'] = $user_pass;
						$creds['remember']      = true;
						$login_user             = wp_signon( $creds, false );
					}

					if ( $result ) {

						// Send email to the user even if the send email option is disabled.
						self::$email_content['pass'] = $user_pass;
					}

					/**
					 * Fires after a new user has been created.
					 *
					 * @since 1.18.0
					 *
					 * @param int    $user_id ID of the newly created user.
					 * @param string $notify  Type of notification that should happen. See wp_send_new_user_notifications()
					 *                        for more information on possible values.
					 */
					do_action( 'edit_user_created_user', $result, $notify );

				} else {
					$response['error'] = wp_send_json_error();
				}
			}

			wp_send_json( $response );

		} else {
			die;
		}

	}

	/**
	 * Get Widget Setting data.
	 *
	 * @since 1.18.0
	 * @access public
	 * @param array  $elements Element array.
	 * @param string $form_id Element ID.
	 * @return Boolean True/False.
	 */
	public function find_element_recursive( $elements, $form_id ) {

		foreach ( $elements as $element ) {
			if ( $form_id === $element['id'] ) {
				return $element;
			}

			if ( ! empty( $element['elements'] ) ) {
				$element = $this->find_element_recursive( $element['elements'], $form_id );

				if ( $element ) {
					return $element;
				}
			}
		}

		return false;
	}

	/**
	 * Create a unique username for a new customer.
	 *
	 * @since 1.18.0
	 * @access public
	 * @param string $email New customer email address.
	 * @param string $suffix Append string to username to make it unique.
	 * @return string Generated username.
	 */
	public function uael_create_username( $email, $suffix ) {

		$username_parts = array();

		// If there are no parts, e.g. name had unicode chars, or was not provided, fallback to email.
		if ( empty( $username_parts ) ) {
			$email_parts    = explode( '@', $email );
			$email_username = $email_parts[0];

			// Exclude common prefixes.
			if ( in_array(
				$email_username,
				array(
					'sales',
					'hello',
					'mail',
					'contact',
					'info',
				),
				true
			) ) {
				// Get the domain part.
				$email_username = $email_parts[1];
			}

			$username_parts[] = sanitize_user( $email_username, true );
		}
		$username = strtolower( implode( '', $username_parts ) );

		if ( $suffix ) {
			$username .= $suffix;
		}

		if ( username_exists( $username ) ) {
			// Generate something unique to append to the username in case of a conflict with another user.
			$suffix = '-' . zeroise( wp_rand( 0, 9999 ), 4 );
			return $this->uael_create_username( $email, $suffix );
		}

		return $username;
	}

}
