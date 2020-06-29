<?php
/**
 * Newsletter Mailpoet Integration class
 *
 * @author YITH
 * @package YITH WooCommerce Popup
 * @version 1.0.0
 */

if ( ! defined( 'YITH_YPOP_INIT' ) ) {
	exit;
} // Exit if accessed directly

define( 'YPOP_NEWSLETTER_MAILPOET', true );

if ( ! class_exists( 'YITH_Popup_Newsletter_Wysija' ) ) {
	/**
	 * YITH_Popup_Newsletter_Wysija class
	 *
	 * @since 1.0.0
	 */
	class YITH_Popup_Newsletter_Wysija {
		/**
		 * Single instance of the class
		 *
		 * @var \YITH_Popup_Newsletter_Wysija
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
		 * @return \YITH_Popup_Newsletter_Wysija
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

			if ( ! class_exists( 'WYSIJA' ) ) {
				return;
			}
			add_filter( 'yith-popup-newsletter-integration-type', array( $this, 'add_integration' ) );
			add_filter( 'yith-popup-newsletter-metabox', array( $this, 'add_metabox_field' ) );

			// CUSTOM INTEGRATION TYPE HANDLING
			$this->add_form_handling();
			// $this->add_admin_form_handling();
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
			$integration['mailpoet'] = __( 'Wysija', 'yith-woocommerce-popup' );
			return $integration;
		}



		/**
		 * Add Metabox Field
		 *
		 * Add mailpoet specific fields to newsletter cpt metabox
		 *
		 * @param $args
		 *
		 * @return mixed
		 * @since 1.0.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.it>
		 */
		public function add_metabox_field( $args ) {

			// generate option array
			$options = array( '-1' => __( 'Select a list', 'yith-woocommerce-popup' ) );

			$mailpoet_lists = $this->get_mailpoet_lists();

			if ( $mailpoet_lists !== false ) {
				$options = $options + $mailpoet_lists;
			}

			$args['fields'] = array_merge(
				$args['fields'],
				array(
					'mailpoet-list'         => array(
						'label'   => __( 'Wysija List', 'yith-woocommerce-popup' ),
						'desc'    => __( 'A valid Wysija list name.', 'yith-woocommerce-popup' ),
						'type'    => 'select',
						'std'     => '-1',
						'options' => $options,
						'deps'    => array(
							'ids'    => '_newsletter-integration',
							'values' => 'mailpoet',
						),
					),
					'mailpoet-email-label'  => array(
						'label' => __( 'Email field label', 'yith-woocommerce-popup' ),
						'desc'  => __( 'The label for the Email field', 'yith-woocommerce-popup' ),
						'type'  => 'text',
						'std'   => 'Email',
						'deps'  => array(
							'ids'    => '_newsletter-integration',
							'values' => 'mailpoet',
						),
					),
					'mailpoet-submit-label' => array(
						'label' => __( 'Submit button label', 'yith-woocommerce-popup' ),
						'desc'  => __( 'This field is not always used. It depends on the style of the form.', 'yith-woocommerce-popup' ),
						'type'  => 'text',
						'std'   => 'Add Me',
						'deps'  => array(
							'ids'    => '_newsletter-integration',
							'values' => 'mailpoet',
						),
					),
				)
			);

			return $args;
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
			add_action( 'wp_ajax_ypop_subscribe_mailpoet_user', array( $this, 'subscribe_mailpoet_user' ) );
			add_action( 'wp_ajax_nopriv_ypop_subscribe_mailpoet_user', array( $this, 'subscribe_mailpoet_user' ) );
		}


		/**
		 * Subscribe Mailpoet user
		 *
		 * Add user to a mailpoet list posted via AJAX-Request to wp_ajax_subscribe_mailpoet_user action
		 *
		 * @return void
		 * @since  1.0.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.it>
		 */
		public function subscribe_mailpoet_user() {
			$post_id = $_REQUEST['ypop_mailpoet_newsletter_form_id'];
			$mail    = $_REQUEST['ypop_mailpoet_newsletter_form_email'];

			$list = '';

			if ( isset( $post_id ) && strcmp( $post_id, '' ) != 0 ) {
				$list = YITH_Popup()->get_meta( '_mailpoet-list', $post_id );
			}

			if ( isset( $mail ) && is_email( $mail ) ) {
				if ( isset( $list ) && strcmp( $list, '-1' ) != 0 && check_ajax_referer( 'ypop_mailpoet_newsletter_form_nonce', 'ypop_mailpoet_newsletter_form_nonce', false ) ) {

					$user_data       = array(
						'email' => $mail,
					);
					$data_subscriber = array(
						'user'      => $user_data,
						'user_list' => array( 'list_ids' => array( $list ) ),
					);

					$helper_user = WYSIJA::get( 'user', 'helper' );
					$helper_user->addSubscriber( $data_subscriber );

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

		/**
		 * Get Mailpoet list registered in db
		 *
		 * Get mailpoet lists
		 *
		 * @return boolean|mixed array()
		 * @since  1.0.0
		 * @author Antonio La Rocca <antonio.larocca@yithemes.it>
		 */
		public function get_mailpoet_lists() {
			$model_list     = WYSIJA::get( 'list', 'model' );
			$mailpoet_lists = $model_list->get( array( 'name', 'list_id' ), array( 'is_enabled' => 1 ) );

			if ( ! empty( $mailpoet_lists ) ) {
				$lists = array();
				foreach ( $mailpoet_lists as $list ) {
					$lists[ $list['list_id'] ] = $list['name'];
				}

				return $lists;
			} else {
				return false;
			}
		}
	}


	/**
	 * Unique access to instance of YITH_Popup class
	 *
	 * @return \YITH_Popup_Newsletter_Wysija
	 */
	function YITH_Popup_Newsletter_Wysija() {
		return YITH_Popup_Newsletter_Wysija::get_instance();
	}

	YITH_Popup_Newsletter_Wysija();
}
