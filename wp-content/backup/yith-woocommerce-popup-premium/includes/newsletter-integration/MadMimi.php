<?php
/**
 * Newsletter MadMimi Integration class
 *
 * @author YITH
 * @package YITH WooCommerce Popup
 * @version 1.0.0
 */


if ( ! defined( 'YITH_YPOP_INIT' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_Popup_Newsletter_MadMimi' ) ) {
	/**
	 * YITH_Popup_Newsletter_MadMimi class
	 *
	 * @since 1.0.0
	 */
	class YITH_Popup_Newsletter_MadMimi {
		/**
		 * Single instance of the class
		 *
		 * @var \YITH_Popup_Newsletter_MadMimi
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
		 * @return \YITH_Popup_Newsletter_MadMimi
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


		public function add_integration( $integration ) {
			$integration['madmimi'] = __( 'Mad Mimi', 'yith-woocommerce-popup' );
			return $integration;
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
			add_action( 'wp_ajax_ypop_subscribe_madmimi_user', array( $this, 'subscribe_madmimi_user' ) );
			add_action( 'wp_ajax_nopriv_ypop_subscribe_madmimi_user', array( $this, 'subscribe_madmimi_user' ) );
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
			add_action( 'wp_ajax_ypop_refresh_madmimi_list', array( $this, 'refresh_madmimi_list' ) );

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
				wp_enqueue_script( 'ypop-refresh-madmimi-list', YITH_YPOP_ASSETS_URL . '/js/refresh-madmimi-list' . $suffix . '.js' );
				wp_localize_script(
					'ypop-refresh-madmimi-list',
					'madmimi_localization',
					array(
						'url'           => admin_url( 'admin-ajax.php' ),
						'nonce_field'   => wp_create_nonce( 'yit_madmimi_refresh_list_nonce' ),
						'refresh_label' => __(
							'Refreshing...',
							'yith-woocommerce-popup'
						),
					)
				);
			}
		}

		/**
		 * Refresh Madmimi List
		 *
		 * Refresh Madmimi list in db and return for ajax callback
		 *
		 * @return boolean|mixed array()
		 * @since 1.0.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.it>
		 */
		public function refresh_madmimi_list() {
			$post_id = $_REQUEST['post_id'];

			if ( check_ajax_referer( 'yit_madmimi_refresh_list_nonce', 'yit_madmimi_refresh_list_nonce', false ) && isset( $post_id ) && strcmp( $post_id, '' ) != 0 ) {
				echo json_encode( $this->get_madmimi_lists( true, $post_id ) );
				die();
			} else {
				echo json_encode( false );
				die();
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

			$options = array( '-1' => __( 'Select a list', 'yith-woocommerce-popup' ) );

			// generate option array
			if ( strcmp( $pagenow, 'post.php' ) == 0 && isset( $_REQUEST['post'] ) ) {
				$post_id = intval( $_REQUEST['post'] );

				$lists = $this->get_madmimi_lists( false, $post_id );
				if ( $lists !== false ) {
					$options = $options + $lists;
				}
			}

			$args['fields'] = array_merge(
				$args['fields'],
				array(
					'madmimi-usr'                  => array(
						'label' => __( 'Mad Mimi Username', 'yith-woocommerce-popup' ),
						'desc'  => __( 'The Mad Mimi username you use to log in', 'yith-woocommerce-popup' ),
						'type'  => 'text',
						'std'   => '',
						'deps'  => array(
							'ids'    => '_newsletter-integration',
							'values' => 'madmimi',
						),
					),
					'madmimi-apikey'               => array(
						'label' => __( 'Mad Mimi API Key', 'yith-woocommerce-popup' ),
						'desc'  => __( 'The Mad Mimi API Key, used to connect WordPress to Mailchimp service. If you need help to create a valid API Key, refer to this <a href="http://help.madmimi.com/where-can-i-find-my-api-key/">tutorial</a>', 'yith-woocommerce-popup' ),
						'type'  => 'text',
						'std'   => '',
						'deps'  => array(
							'ids'    => '_newsletter-integration',
							'values' => 'madmimi',
						),
					),
					'madmimi-list'                 => array(
						'label'       => __( 'Mad Mimi List', 'yith-woocommerce-popup' ),
						'desc'        => __( 'A valid Mad Mimi list name. You may need to save your configuration before displaying the correct contents. If the list is not up to date, click the Refresh button', 'yith-woocommerce-popup' ),
						'type'        => 'select-mailchimp',
						'std'         => '-1',
						'class'       => 'madmimi-list-refresh',
						'button_name' => __( 'Refresh', 'yith-woocommerce-popup' ),
						'options'     => $options,
						'deps'        => array(
							'ids'    => '_newsletter-integration',
							'values' => 'madmimi',
						),
					),
					'madmimi-email-label'          => array(
						'label' => __( 'Email field label', 'yith-woocommerce-popup' ),
						'desc'  => __( 'The label for the Email field', 'yith-woocommerce-popup' ),
						'type'  => 'text',
						'std'   => 'Email',
						'deps'  => array(
							'ids'    => '_newsletter-integration',
							'values' => 'madmimi',
						),
					),
					'madmimi-add-privacy-checkbox' => array(
						'label' => __( 'Add Privacy Policy', 'yith-woocommerce-popup' ),
						'desc'  => '',
						'type'  => 'onoff',
						'std'   => 'no',
						'deps'  => array(
							'ids'    => '_newsletter-integration',
							'values' => 'madmimi',
						),
					),

					'madmimi-privacy-label'        => array(
						'label' => __( 'Privacy Policy Label', 'yith-woocommerce-popup' ),
						'desc'  => '',
						'type'  => 'text',
						'std'   => __( 'I have read and agree to the website terms and conditions.', 'yith-woocommerce-popup' ),
						'deps'  => array(
							'ids'    => '_newsletter-integration',
							'values' => 'madmimi',
						),
					),

					'madmimi-privacy-description'  => array(
						'label' => __( 'Privacy Policy Description', 'yith-woocommerce-popup' ),
						'desc'  => __( 'You can use the shortcode [privacy_policy] (from WordPress 4.9.6) to add the link to privacy policy page', 'yith-woocommerce-popup' ),
						'type'  => 'textarea',
						'std'   => __( 'Your personal data will be used to process your request, support your experience throughout this website, and for other purposes described in our [privacy_policy].', 'yith-woocommerce-popup' ),
						'deps'  => array(
							'ids'    => '_newsletter-integration',
							'values' => 'madmimi',
						),
					),
					'madmimi-submit-label'         => array(
						'label' => __( 'Submit button label', 'yith-woocommerce-popup' ),
						'desc'  => __( 'This field is not always used. It depends on the style of the form.', 'yith-woocommerce-popup' ),
						'type'  => 'text',
						'std'   => 'Add Me',
						'deps'  => array(
							'ids'    => '_newsletter-integration',
							'values' => 'madmimi',
						),
					),
				)
			);

			return $args;
		}

		/**
		 * Get Madmimi list for the apikey set in db
		 *
		 * Get madmimi lists; if no apikey is set, return false. If lists are stored in a transient, return the transient.
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
		public function get_madmimi_lists( $update = false, $post_id ) {
			if ( isset( $_REQUEST['apikey'] ) && isset( $_REQUEST['username'] ) ) {
				$apikey   = $_REQUEST['apikey'];
				$username = $_REQUEST['username'];
			} else {
				$apikey   = YITH_Popup()->get_meta( '_madmimi-apikey', $post_id );
				$username = YITH_Popup()->get_meta( '_madmimi-usr', $post_id );
			}

			if ( isset( $apikey ) && strcmp( $apikey, '' ) != 0 && isset( $username ) && strcmp( $username, '' ) != 0 ) {
				if ( ! $update ) {
					$transient = get_transient( 'yith-popup-madmimi-newsletter-list' );
					$transient = false;
					if ( $transient !== false ) {
						return $transient;
					} else {
						return $this->set_madmimi_lists( $username, $apikey, $post_id );
					}
				} else {
					return $this->set_madmimi_lists( $username, $apikey, $post_id );
				}
			} else {
				return false;
			}
		}

		/**
		 * Set Madmimi list transient and return the list
		 *
		 * @param $username string Madmimi user
		 * @param $apikey  string Madmimi apikey
		 * @param $post_id int Post id
		 *
		 * @return boolean|mixed array()
		 * @since  1.0.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.it>
		 */
		public function set_madmimi_lists( $username, $apikey, $post_id ) {
			if ( isset( $apikey ) && strcmp( $apikey, '' ) != 0 ) {
				// include libraries
				if ( ! class_exists( 'MadMimi' ) ) {
					include_once YITH_YPOP_INC . 'vendor/madmimi/MadMimi.class.php';
				}

				// initialize mailchimp wrapper
				$madmimi_wrapper = new MadMimi(
					$username,
					$apikey
				);

				// fetch list
				$xml = $madmimi_wrapper->Lists();

				try {
					$result = new SimpleXMLElement( $xml );
					// generate result array
					$lists = array();
					foreach ( $result->list as $list ) {
						$attrs        = $list->attributes();
						$id           = (string) $attrs['id'];
						$name         = (string) $attrs['name'];
						$lists[ $id ] = $name;
					}

					// memorize result array in a transient
					set_transient( 'yith-popup-madmimi-newsletter-' . $post_id . '-list', $lists, WEEK_IN_SECONDS );
					return $lists;
				} catch ( Exception $e ) {
					if ( WP_DEBUG ) {
						echo $e->getMessage(); //phpcs:ignore
					}
					return false;
				}
			} else {
				return false;
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
		public function subscribe_madmimi_user() {

			$post_id = $_REQUEST['yit_madmimi_newsletter_form_id'];

			$mail     = $_REQUEST['yit_madmimi_newsletter_form_email'];
			$apikey   = '';
			$username = '';
			$list     = '';

			if ( isset( $post_id ) && strcmp( $post_id, '' ) != 0 ) {
				$apikey   = YITH_Popup()->get_meta( '_madmimi-apikey', $post_id );
				$username = YITH_Popup()->get_meta( '_madmimi-usr', $post_id );
				$list     = YITH_Popup()->get_meta( '_madmimi-list', $post_id );
			}
			if ( isset( $mail ) && is_email( $mail ) ) {
				if ( isset( $list ) && strcmp( $list, '-1' ) != 0 && isset( $apikey ) && strcmp( $apikey, '' ) != 0 && isset( $username ) && strcmp( $username, '' ) != 0 && check_ajax_referer( 'yit_madmimi_newsletter_form_nonce', 'yit_madmimi_newsletter_form_nonce', false ) ) {

					if ( ! class_exists( 'MadMimi' ) ) {
						include_once YITH_YPOP_INC . 'vendor/madmimi/MadMimi.class.php';
					}

					$madmimi_wrapper = new Madmimi(
						$username,
						$apikey
					);

					$lists     = $this->get_madmimi_lists( false, $post_id );
					$list_name = $lists[ $list ];

					$result = $madmimi_wrapper->addMembership( $list_name, $mail );

					echo $result; //phpcs:ignore

					esc_html_e( '<span class="success">Email successfully registered</span>', 'yith-woocommerce-popup' );
					die();
				} else {
					esc_html_e( '<span class="error">Ops! Something went wrong</span>', 'yith-woocommerce-popup' );
					die();
				}
			} else {
				esc_html_e( '<span class="notice">Ops! You have to use a valid email address</span>', 'yith-woocommerce-popup' );
				die();
			}
		}

	}

	/**
	 * Unique access to instance of YITH_Popup class
	 *
	 * @return \YITH_Popup_Newsletter_MadMimi
	 */
	function YITH_Popup_Newsletter_MadMimi() {
		return YITH_Popup_Newsletter_MadMimi::get_instance();
	}

	YITH_Popup_Newsletter_MadMimi();
}

