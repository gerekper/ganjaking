<?php
/**
 * Newsletter Mailchimp Integration class
 *
 * @author YITH
 * @package YITH WooCommerce Popup
 * @version 1.0.0
 */


if ( ! defined( 'YITH_YPOP_INIT' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_Popup_Newsletter_Mailchimp' ) ) {
	/**
	 * YITH_Popup_Newsletter_Mailchimp class
	 *
	 * @since 1.0.0
	 */
	class YITH_Popup_Newsletter_Mailchimp {
		/**
		 * Single instance of the class
		 *
		 * @var \YITH_Popup_Newsletter_Mailchimp
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Array with accessible variables
		 */
		protected $_data = array();


		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_Popup_Newsletter_Mailchimp
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}


		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_filter( 'yith-popup-newsletter-integration-type', array( $this, 'add_integration' ) );
			add_filter( 'yith-popup-newsletter-metabox', array( $this, 'add_metabox_field' ) );

			// CUSTOM INTEGRATION TYPE HANDLING
			$this->add_form_handling();
			$this->add_admin_form_handling();
		}

		/**
		 * Add Form Handling
		 *
		 * Add the frontend form handling, if needed
		 *
		 * @return void
		 * @since 1.0.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.it>
		 */
		public function add_form_handling() {
			// add mailchimp subscription
			add_action( 'wp_ajax_ypop_subscribe_mailchimp_user', array( $this, 'subscribe_mailchimp_user' ) );
			add_action( 'wp_ajax_nopriv_ypop_subscribe_mailchimp_user', array( $this, 'subscribe_mailchimp_user' ) );
		}

		/**
		 * Add Admin form handling
		 *
		 * Add the backend form handling formetabox, if needed
		 *
		 * @return void
		 * @since 1.0.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.it>
		 */
		public function add_admin_form_handling() {
			// add mailchimp lists refresh
			add_action( 'wp_ajax_ypop_refresh_mailchimp_list', array( $this, 'refresh_mailchimp_list' ) );

			// add admin-side scripts
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}

		/**
		 * Enqueue admin script
		 *
		 * Enqueue backend scripts; constructor add it to admin_enqueue_scripts hook
		 *
		 * @return void
		 * @since  1.0.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.it>
		 */
		public function admin_enqueue_scripts() {
			global $pagenow;
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			if ( get_post_type() == YITH_Popup()->post_type_name && ( strcmp( $pagenow, 'post.php' ) == 0 || strcmp( $pagenow, 'post-new.php' ) == 0 ) ) {
				wp_enqueue_script( 'ypop-refresh-mailchimp-list', YITH_YPOP_ASSETS_URL . '/js/refresh-mailchimp-list' . $suffix . '.js' );
				wp_localize_script(
					'ypop-refresh-mailchimp-list',
					'mailchimp_localization',
					array(
						'url'           => admin_url( 'admin-ajax.php' ),
						'nonce_field'   => wp_create_nonce( 'yit_mailchimp_refresh_list_nonce' ),
						'refresh_label' => __(
							'Refreshing...',
							'yith-woocommerce-popup'
						),
					)
				);
			}
		}

		/**
		 * Add Metabox Field
		 *
		 * Add mailchimp specific fields to newsletter cpt metabox
		 *
		 * @param $args
		 *
		 * @return mixed
		 * @since 1.0.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.it>
		 */
		public function add_metabox_field( $args ) {
			global $pagenow;
			// generate option array
			$options = array( '-1' => __( 'Select a list', 'yith-woocommerce-popup' ) );

			if ( strcmp( $pagenow, 'post.php' ) == 0 && isset( $_REQUEST['post'] ) ) {
				$post_id = intval( $_REQUEST['post'] );

				$lists = $this->get_mailchimp_lists( false, $post_id );
				if ( $lists !== false ) {
					$options = array_merge( $options, $lists );
				}
			}

			$args['fields'] = array_merge(
				$args['fields'],
				array(
					'mailchimp-apikey'               => array(
						'label' => __( 'Mailchimp API Key', 'yith-woocommerce-popup' ),
						'desc'  => __( 'The Mailchimp API Key, used to connect WordPress to the Mailchimp service. If you need help to create a valid API Key, refer to this <a href="http://kb.mailchimp.com/article/where-can-i-find-my-api-key">tutorial</a>', 'yith-woocommerce-popup' ),
						'type'  => 'text',
						'std'   => '',
						'deps'  => array(
							'ids'    => '_newsletter-integration',
							'values' => 'mailchimp',
						),
					),
					'mailchimp-list'                 => array(
						'label'       => __( 'Mailchimp List', 'yith-woocommerce-popup' ),
						'desc'        => __( 'A valid Mailchimp list name. You may need to save your configuration before displaying the correct contents. If the list is not up to date, click the Refresh button', 'yith-woocommerce-popup' ),
						'type'        => 'select-mailchimp',
						'std'         => '-1',
						'class'       => 'mailchimp-list-refresh',
						'button_name' => __( 'Refresh', 'yith-woocommerce-popup' ),
						'options'     => $options,
						'deps'        => array(
							'ids'    => '_newsletter-integration',
							'values' => 'mailchimp',
						),
					),
					'mailchimp-double_opt_in'        => array(
						'label' => __( 'Double Opt-in', 'yith-woocommerce-popup' ),
						'desc'  => __( 'When you check this option, MailChimp will send a confirmation email before adding the user to the list', 'yith-woocommerce-popup' ),
						'type'  => 'onoff',
						'std'   => 'yes',
						'deps'  => array(
							'ids'    => '_newsletter-integration',
							'values' => 'mailchimp',
						),
					),
					'mailchimp-email-label'          => array(
						'label' => __( 'Email field label', 'yith-woocommerce-popup' ),
						'desc'  => __( 'The label for the Email field', 'yith-woocommerce-popup' ),
						'type'  => 'text',
						'std'   => 'Email',
						'deps'  => array(
							'ids'    => '_newsletter-integration',
							'values' => 'mailchimp',
						),
					),
					'mailchimp-add-privacy-checkbox' => array(
						'label' => __( 'Add Privacy Policy', 'yith-woocommerce-popup' ),
						'desc'  => '',
						'type'  => 'onoff',
						'std'   => 'no',
						'deps'  => array(
							'ids'    => '_newsletter-integration',
							'values' => 'mailchimp',
						),
					),

					'mailchimp-privacy-label'        => array(
						'label' => __( 'Privacy Policy Label', 'yith-woocommerce-popup' ),
						'desc'  => '',
						'type'  => 'text',
						'std'   => __( 'I have read and agree to the website terms and conditions.', 'yith-woocommerce-popup' ),
						'deps'  => array(
							'ids'    => '_newsletter-integration',
							'values' => 'mailchimp',
						),
					),

					'mailchimp-privacy-description'  => array(
						'label' => __( 'Privacy Policy Description', 'yith-woocommerce-popup' ),
						'desc'  => __( 'You can use the shortcode [privacy_policy] (from WordPress 4.9.6) to add the link to privacy policy page', 'yith-woocommerce-popup' ),
						'type'  => 'textarea',
						'std'   => __( 'Your personal data will be used to process your request, support your experience throughout this website, and for other purposes described in our [privacy_policy].', 'yith-woocommerce-popup' ),
						'deps'  => array(
							'ids'    => '_newsletter-integration',
							'values' => 'mailchimp',
						),
					),
					'mailchimp-submit-label'         => array(
						'label' => __( 'Submit button label', 'yith-woocommerce-popup' ),
						'desc'  => __( 'This field is not always used. It depends on the style of the form.', 'yith-woocommerce-popup' ),
						'type'  => 'text',
						'std'   => __( 'Add Me', 'yith-woocommerce-popup' ),
						'deps'  => array(
							'ids'    => '_newsletter-integration',
							'values' => 'mailchimp',
						),
					),
				)
			);

			return $args;
		}

		/**
		 * Add Integration Type
		 *
		 * Add mailchimp integration to integration mode select in newsletter plugin
		 *
		 * @param $integration
		 *
		 * @return mixed
		 * @internal param $types
		 *
		 * @since    1.0.0
		 * @author   Antonio La Rocca <antonio.larocca@yithemes.it>
		 */
		public function add_integration( $integration ) {
			$integration['mailchimp'] = __( 'Mailchimp', 'yith-woocommerce-popup' );
			return $integration;
		}

		/**
		 * Get Mailchimp list for the apikey set in db
		 *
		 * Get mailchimp lists; if no apikey is set, return false. If lists are stored in a transient, return the transient.
		 * If no transient is set for lists, get the list from mailchimp server, store the transient and return the list.
		 * If update is set, force the update of the list and of the transient
		 *
		 * @param boolean $update Whether to update list or no. Default false
		 * @param int     $post_id Post id
		 *
		 * @return boolean|mixed array()
		 * @since 1.0.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.it>
		 */
		public function get_mailchimp_lists( $update = false, $post_id ) {

			if ( isset( $_REQUEST['apikey'] ) ) {
				$apikey = $_REQUEST['apikey'];
			} else {
				$apikey = YITH_Popup()->get_meta( '_mailchimp-apikey', $post_id );
			}

			if ( isset( $apikey ) && strcmp( $apikey, '' ) != 0 ) {
				if ( ! $update ) {
					$transient = get_transient( 'yith-popup-mailchimp-newsletter-list' );
					if ( $transient !== false ) {
						return $transient;
					} else {
						return $this->set_mailchimp_lists( $apikey, $post_id );
					}
				} else {
					return $this->set_mailchimp_lists( $apikey, $post_id );
				}
			} else {
				return false;
			}
		}

		/**
		 * Set Mailchimp list transient and return the list
		 *
		 * @param $apikey string Mailchimp apikey
		 * @param $post_id int Post id
		 *
		 * @return boolean|mixed array()
		 * @since 1.0.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.it>
		 */
		public function set_mailchimp_lists( $apikey, $post_id ) {
			if ( isset( $apikey ) && strcmp( $apikey, '' ) != 0 ) {

				// include libraries
				if ( ! class_exists( 'Mailchimp' ) ) {
					include_once YITH_YPOP_INC . 'vendor/mailchimp/Mailchimp.php';
				}

				// initialize mailchimp wrapper
				$mailchimp_wrapper = new Mailchimp(
					$apikey,
					array(
						'ssl_verifypeer' => false,
					)
				);

				// fetch list
				$result = $mailchimp_wrapper->call(
					'lists/list',
					array(
						'apikey' => $apikey,
					)
				);

				// generate result array
				$lists = array();
				if ( ! empty( $result ) && isset( $result['total'] ) && $result['total'] > 0 ) {
					foreach ( $result['data'] as $list ) {
						$lists[ $list['id'] ] = $list['name'];
					}
				}

				// memorize result array in a transient
				set_transient( 'yith-popup-mailchimp-newsletter-' . $post_id . '-list', $lists, WEEK_IN_SECONDS );

				return $lists;
			} else {
				return false;
			}
		}


		/**
		 * Refresh Mailchimp List
		 *
		 * Refresh Mailchimp list in db and return for ajax callback
		 *
		 * @return boolean|mixed array()
		 * @since 1.0.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.it>
		 */
		public function refresh_mailchimp_list() {
			$post_id = $_REQUEST['post_id'];

			if ( check_ajax_referer( 'yit_mailchimp_refresh_list_nonce', 'yit_mailchimp_refresh_list_nonce', false ) && isset( $post_id ) && strcmp( $post_id, '' ) != 0 ) {
				echo json_encode( $this->get_mailchimp_lists( true, $post_id ) );
				die();
			} else {
				echo json_encode( false );
				die();
			}
		}

		/**
		 * Subscribe Mailchimp user
		 *
		 * Add user to a mailchinmp list posted via AJAX-Request to wp_ajax_subscribe_mailchimp_user action
		 *
		 * @return void
		 * @since 1.0.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.it>
		 */
		public function subscribe_mailchimp_user() {

			$post_id = $_REQUEST['yit_mailchimp_newsletter_form_id'];

			$mail   = $_REQUEST['yit_mailchimp_newsletter_form_email'];
			$apikey = '';
			$list   = '';

			if ( isset( $post_id ) && strcmp( $post_id, '' ) != 0 ) {
				$apikey       = YITH_Popup()->get_meta( '_mailchimp-apikey', $post_id );
				$list         = YITH_Popup()->get_meta( '_mailchimp-list', $post_id );
				$double_optin = YITH_Popup()->get_meta( '_mailchimp-double_opt_in', $post_id );
				$double_optin = yith_plugin_fw_is_true( $double_optin ) ? true : false;
			}

			if ( isset( $mail ) && is_email( $mail ) ) {
				if ( isset( $list ) && strcmp( $list, '-1' ) != 0 && isset( $apikey ) && strcmp( $apikey, '' ) != 0 && check_ajax_referer( 'yit_mailchimp_newsletter_form_nonce', 'yit_mailchimp_newsletter_form_nonce', false ) ) {
					if ( ! class_exists( 'Mailchimp' ) ) {
						include_once YITH_YPOP_INC . 'vendor/mailchimp/Mailchimp.php';
					}

					$mailchimp_wrapper = new Mailchimp(
						$apikey,
						array(
							'ssl_verifypeer' => false,
						)
					);

					$result = $mailchimp_wrapper->call(
						'lists/batch-subscribe',
						array(
							'apikey'       => $apikey,
							'id'           => $list,
							'batch'        => array(
								array(
									'email' => array(
										'email'      => $mail,
										'email_type' => 'html',
									),
								),
							),
							'double_optin' => $double_optin,
						)
					);

					if ( $result['error_count'] != 0 ) {
						$message  = '<span class="error">' . apply_filters( 'ywpop_start_message_error', __( 'Something went wrong:', 'yith-woocommerce-popup' ) );
						$message .= '<ul>';

						foreach ( $result['errors'] as $error ) {
							$code = $error['code'];

							if ( $code <= 0 ) {
								$message_in = __( 'Mailchimp server error', 'yith-woocommerce-popup' );
							} elseif ( $code >= 100 && $code < 120 ) {
								$message_in = __( 'Mailchimp user related error', 'yith-woocommerce-popup' );
							} elseif ( $code >= 120 && $code < 200 ) {
								$message_in = __( 'Mailchimp user related error (action)', 'yith-woocommerce-popup' );
							} elseif ( $code >= 200 && $code < 210 ) {
								$message_in = __( 'Mailchimp list related error', 'yith-woocommerce-popup' );
							} elseif ( $code >= 210 && $code < 220 ) {
								$message_in = __( 'Mailchimp list related error (basic action)', 'yith-woocommerce-popup' );
							} elseif ( $code >= 220 && $code < 230 ) {
								$message_in = __( 'Mailchimp list related error (import)', 'yith-woocommerce-popup' );
							} elseif ( $code >= 230 && $code < 250 ) {
								$message_in = __( 'Mailchimp list related error (email)', 'yith-woocommerce-popup' );
							} elseif ( $code >= 250 && $code < 270 ) {
								$message_in = __( 'Mailchimp list related error (merge)', 'yith-woocommerce-popup' );
							} elseif ( $code >= 270 && $code < 300 ) {
								$message_in = __( 'Mailchimp list related error (interest group)', 'yith-woocommerce-popup' );
							} else {
								$message_in = __( 'Mailchimp general error', 'yith-woocommerce-popup' );
							}

							$message_in = apply_filters( 'ywpop_message_error_filter', $message_in, $code );
							$message   .= '<li>' . $message_in . '</li>';
						}

						$message .= '</ul></span>';

						echo wp_kses_post( $message );
					} else {
						echo wp_kses_post( apply_filters( 'ywpop_message_success_filter', __( '<span class="success">Email successfully registered</span>', 'yith-woocommerce-popup' ) ) );
					}
					die();
				} else {
					echo wp_kses_post( apply_filters( 'ywpop_message_wrong_filter', __( '<span class="error">Ops! Something went wrong</span>', 'yith-woocommerce-popup' ) ) );
					die();
				}
			} else {
				echo wp_kses_post( apply_filters( 'ywpop_message_notice_filter', __( '<span class="notice">Ops! You have to use a valid email address</span>', 'yith-woocommerce-popup' ) ) );

				die();
			}
		}



	}

	/**
	 * Unique access to instance of YITH_Popup class
	 *
	 * @return \YITH_Popup_Newsletter_Mailchimp
	 */
	function YITH_Popup_Newsletter_Mailchimp() {
		return YITH_Popup_Newsletter_Mailchimp::get_instance();
	}

	YITH_Popup_Newsletter_Mailchimp();
}

