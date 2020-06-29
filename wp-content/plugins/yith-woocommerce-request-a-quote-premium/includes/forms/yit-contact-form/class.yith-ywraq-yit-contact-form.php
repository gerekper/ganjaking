<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( !defined( 'ABSPATH' ) || !defined( 'YITH_YWRAQ_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements the YITH_YWRAQ_YIT_Contact_Form class.
 *
 * @class   YITH_YWRAQ_YIT_Contact_Form
 * @package YITH
 * @since   2.0.0
 * @author  YITH
 */
if ( !class_exists( 'YITH_YWRAQ_YIT_Contact_Form' ) ) {

	/**
	 * Class YITH_YWRAQ_YIT_Contact_Form
	 */
	class YITH_YWRAQ_YIT_Contact_Form {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_YWRAQ_YIT_Contact_Form
		 */
		protected static $instance;

		/**
		 * @var array
		 */
		protected $yit_contact_form_post = array();

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_YWRAQ_YIT_Contact_Form
		 * @since 2.0.0
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * Initialize form and registers actions and filters to be used
		 *
		 * @since  2.0
		 * @author Emanuela Castorina
		 */
		public function __construct() {

			if ( is_admin() ) {
				add_filter( 'ywraq_form_type_list', array( $this, 'add_to_option_list' ) );
				add_filter( 'ywraq_additional_form_options', array( $this, 'add_option' ) );
			}

			if ( get_option( 'ywraq_inquiry_form_type' ) == 'yit-contact-form' ) {
				add_filter( 'yit_contact_form_shortcodes', array( $this, 'yith_ywraq_quote_list_shortcode' ) );
				add_action( 'init', array( $this, 'yit_contact_form_before_sending_email' ), 9 );
				add_action( 'yit-sendmail-success', array( $this, 'yit_contact_form_after_sent_email' ) );
				add_filter( 'ywraq_order_meta_list', array( $this, 'add_order_metas' ), 10, 3 );
			}

		}

		/**
		 * @param $list
		 *
		 * @return mixed
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_to_option_list( $list ) {
			$list['yit-contact-form'] = __( 'YIT Contact Form', 'yith-woocommerce-request-a-quote' );
			return $list;
		}

		/**
		 * @param $shortcodes
		 *
		 * @return mixed
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function yith_ywraq_quote_list_shortcode( $shortcodes ) {
			$shortcodes['%yith-request-a-quote-list%'] = yith_ywraq_get_email_template( true );
			return $shortcodes;
		}

		/**
		 * @return array
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function get_contact_forms() {
			if ( ! function_exists( 'YIT_Contact_Form' ) ) {
				return array( '' => __( 'Plugin not activated or not installed', 'yith-woocommerce-request-a-quote' ) );
			}

			$array = array();

			$posts = get_posts( array(
				'post_type' => YIT_Contact_Form()->contact_form_post_type
			) );


			foreach ( $posts as $post ) {
				$array[ $post->post_name ] = $post->post_title;
			}

			if ( $array == array() ) {
				return array( '' => __( 'No contact form found', 'yith-woocommerce-request-a-quote' ) );
			}

			return $array;
		}

		/**
		 * @param $options
		 *
		 * @return mixed
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_option( $options ) {
			$options['yit_contact_form'] = array(
				'name'      => '',
				'type'      => 'yith-field',
				'yith-type' => 'select',
				'desc'      => __( 'Choose the form to display', 'yith-woocommerce-request-a-quote' ),
				'options'   => $this->get_contact_forms(),
				'id'        => 'ywraq_inquiry_yit_contact_form_id',
				'deps'      => array(
					'id'    => 'ywraq_inquiry_form_type',
					'value' => 'yit-contact-form',
					'type'  => 'hide'
				),
				'class'     => 'yit-contact-form'
			);

			return $options;
		}

		/**
		 * Function called if yit-contact-form in used to send the request
		 * grab the post array ad save it in $yit_contact_form_post
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 * @return  void
		 */
		public function yit_contact_form_before_sending_email() {

			if ( isset( $_POST['id_form'] ) &&  $this->get_post_for_id() == $_POST['id_form'] && isset( $_POST['yit_contact'] ) ) {
				$this->yit_contact_form_post = $_POST['yit_contact'];
			}
		}

		/**
		 * Return the url of request quote page
		 *
		 * @return string
		 * @since 1.0.0
		 */
		public function get_post_for_id() {
			$option_value = get_option( 'ywraq_inquiry_yit_contact_form_id' );

			if ( function_exists( 'wpml_object_id_filter' ) ) {
				global $sitepress;

				if ( !is_null( $sitepress ) && is_callable( array( $sitepress, 'get_current_language' ) ) ) {
					$option_value = wpml_object_id_filter( $option_value, 'post', true, $sitepress->get_current_language() );
				}
			}

			return  $option_value;

		}

		/**
		 * Function called if yit-contact-form in used to send the request
		 * after the email is sent to administrator
		 *
		 * @since   1.0.0
		 * @author  Emanuela Castorina
		 * @return  void
		 */
		public function yit_contact_form_after_sent_email() {

			if ( ! empty( $this->yit_contact_form_post ) && isset( $this->yit_contact_form_post['name'] ) && isset( $this->yit_contact_form_post['email'] ) ) {

				$args = array(
					'user_name'    => $this->yit_contact_form_post['name'],
					'user_email'   => $this->yit_contact_form_post['email'],
					'user_message' => isset( $this->yit_contact_form_post['message'] ) ? $this->yit_contact_form_post['message'] : '',
					'raq_content'  => YITH_Request_Quote()->get_raq_return()
				);

				$new_order = YITH_YWRAQ_Order_Request()->create_order( $args );

				apply_filters( 'ywraq_clear_list_after_send_quote', true ) && YITH_Request_Quote()->clear_raq_list();

				yith_ywraq_add_notice( ywraq_get_message_after_request_quote_sending( $new_order ), 'success' );

				wp_redirect( YITH_Request_Quote()->get_redirect_page_url(), 301 );

				exit;
			} else {
				yith_ywraq_add_notice( __( 'An error has occurred. Please, contact site administrator.', 'yith-woocommerce-request-a-quote' ), 'error' );
			}

		}

		/**
		 * Add order meta from request.
		 *
		 * @param $attr
		 * @param $order_id
		 * @param $raq
		 *
		 * @return mixed
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function add_order_metas( $attr, $order_id, $raq ) {

			//default fields
			$attr['ywraq_customer_name']    = $raq['user_name'];
			$attr['ywraq_customer_message'] = $raq['user_message'];
			$attr['ywraq_customer_email']   = $raq['user_email'];
			$attr['_raq_request']           = $raq;

			$ov_field = apply_filters( 'ywraq_override_order_billing_fields', true );
			if ( $ov_field ) {
				$attr['_billing_email'] = $raq['user_email'];
			}

			return $attr;
		}

	}

	/**
	 * Unique access to instance of YITH_YWRAQ_YIT_Contact_Form class
	 *
	 * @return \YITH_YWRAQ_YIT_Contact_Form
	 */
	function YITH_YWRAQ_YIT_Contact_Form() {
		return YITH_YWRAQ_YIT_Contact_Form::get_instance();
	}
}