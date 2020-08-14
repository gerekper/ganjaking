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
 * Implements the YITH_YWRAQ_Send_Email_Request_Quote class.
 *
 * @class   YITH_YWRAQ_Send_Email_Request_Quote
 * @package YITH
 * @since   1.0.0
 * @author  YITH
 */
if ( !class_exists( 'YITH_YWRAQ_Send_Email_Request_Quote' ) ) {

	/**
	 * YITH_YWRAQ_Send_Email_Request_Quote
	 *
	 * @since 1.0.0
	 */
	class YITH_YWRAQ_Send_Email_Request_Quote extends WC_Email {

		/**
		 * Constructor method, used to return object of the class to WC
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->id          = 'ywraq_email';
			$this->title       = __( '[YITH Request a Quote] Email to request a quote', 'yith-woocommerce-request-a-quote' );
			$this->description = __( 'This email is sent when a user clicks on "Request a quote" button', 'yith-woocommerce-request-a-quote' );

			$this->heading = __( 'Request a quote', 'yith-woocommerce-request-a-quote' );
			$this->subject = __( '[Request a quote]', 'yith-woocommerce-request-a-quote' );

			$this->template_base  = YITH_YWRAQ_TEMPLATE_PATH.'/';
			$this->template_html  = 'emails/request-quote.php';
			$this->template_plain  = 'emails/plain/request-quote.php';

			// Triggers for this email
			add_action( 'send_raq_mail_notification', array( $this, 'trigger' ), 15, 1 );

			// Call parent constructor
			parent::__construct();

			if( $this->enabled == 'no'){
				return;
			}

			// Other settings
			$this->recipient = $this->get_option( 'recipient' );

			if ( !$this->recipient ) {
				$this->recipient = get_option( 'admin_email' );
			}

			$this->enable_cc = $this->get_option( 'enable_cc' );

			$this->enable_cc = $this->enable_cc == 'yes';

		}

		/**
		 * Method triggered to send email
		 *
		 * @param int $args
		 *
		 * @return void
		 * @since  1.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function trigger( $args ) {

			$new_order             = WC()->session->raq_new_order;



			if( $this->enabled == 'no'){

				if( apply_filters('ywraq_clear_list_after_send_quote', true ) ){
					YITH_Request_Quote()->clear_raq_list();
				}

				$message_after_send = ywraq_get_message_after_request_quote_sending( $new_order );
				if( !empty( $message_after_send ) ) {
					yith_ywraq_add_notice( $message_after_send, 'success' );
				}

				return true;
			}

			$this->raq             = $args;
			$this->raq['order_id'] = is_null( $new_order ) ? 0 : $new_order;

			//quote checkout
			$this->raq['content_type'] = isset( $args['from_checkout'] ) ? 'order_items' : 'raq_content';

			$order = wc_get_order( $this->raq['order_id'] );
			if( 'order_items' ==  $this->raq['content_type'] && $order ){
				$this->raq['raq_content'] = $order->get_items();
			}

			$this->placeholders['{quote_number}'] = apply_filters( 'ywraq_quote_number', $this->raq['order_id'] );
			$this->placeholders['{quote_user}']   = $this->raq['user_name'];
			$this->placeholders['{quote_email}']  = $this->raq['user_email'];

			if( ! is_null( $new_order ) ){
				$this->object = wc_get_order( $new_order );
			}

			$recipients = (array) $this->get_recipient();
			if ( $this->enable_cc ) {
				$recipients[] = $this->raq['user_email'];
			}
			$recipients = implode( ',', $recipients );
			// remove spaces for avoiding problems on multi-recipients emails
			$recipients = str_replace( ' ', '', $recipients );

			if( class_exists('YWRAQ_Multivendor') ){
				$return = apply_filters( 'ywraq_multivendor_email', false, $args, $this);
			}else{
				$return = $this->send( $recipients, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			}

			if ( $return || apply_filters('ywraq_check_send_email_request_a_quote', false) || defined('YWTENV_INIT') ) {
				if( apply_filters('ywraq_clear_list_after_send_quote', true ) ){
					YITH_Request_Quote()->clear_raq_list();
				}

				$message_after_send = ywraq_get_message_after_request_quote_sending( $new_order );
				if( !empty( $message_after_send ) ) {
					yith_ywraq_add_notice( $message_after_send, 'success' );
				}
			}
			else {
				yith_ywraq_add_notice( __( 'There was a problem sending your request. Please try again....', 'yith-woocommerce-request-a-quote' ), 'error' );
			}

		}

		/**
		 * get_headers function.
		 *
		 * @access public
		 * @return string
		 */
		public function get_headers() {
			$headers = "Reply-to: " . $this->raq['user_email'] . "\r\n";

			if ( $this->enable_cc ) {
				$headers .= "Cc: " . $this->raq['user_email'] . "\r\n";
			}

			$headers .= "Content-Type: " . $this->get_content_type() . "\r\n";

			$obj = isset(  $this->object ) ?  $this->object : false;

			return apply_filters( 'woocommerce_email_headers', $headers, $this->id, $obj  );
		}

		/**
		 * Get HTML content for the mail
		 *
		 * @return string HTML content of the mail
		 * @since  1.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function get_content_html() {
			ob_start();

			wc_get_template( $this->template_html, array(
				'raq_data'          => $this->raq,
				'email_heading'     => $this->get_heading(),
				'email_description' => $this->format_string( $this->get_option( 'email-description' ) ),
				'sent_to_admin'     => true,
				'plain_text'        => false,
				'email'             => $this
			), '', $this->template_base );



			return ob_get_clean();
		}

		/**
		 * Get Plain content for the mail
		 *
		 * @access public
		 * @return string
		 */
		function get_content_plain() {
			ob_start();
			wc_get_template( $this->template_plain, array(
				'raq_data'          => $this->raq,
				'email_heading'     => $this->get_heading(),
				'email_description' => $this->format_string( $this->get_option( 'email-description' ) ),
				'sent_to_admin'     => true,
				'plain_text'        => false,
				'email'             => $this
			), false, $this->template_base );
			return ob_get_clean();
		}


		/**
		 * Return the array with the attachments' file paths.
		 *
		 * @return array
		 */
		public function get_attachments(){
			$attachments = ywraq_get_default_form_attachment( $this->raq );
			$obj = isset(  $this->object ) ?  $this->object : false;
			return apply_filters( 'woocommerce_email_attachments', $attachments, $this->id, $obj );
		}

		/**
		 * Get from name for email.
		 *
		 * @return string
		 */
		public function get_from_name( $from_name= '' ) {
			$email_from_name = ( isset($this->settings['email_from_name']) && $this->settings['email_from_name'] != '' ) ? $this->settings['email_from_name'] : $from_name;
			$email_from_name  = apply_filters( 'ywraq_request_a_quote_send_email_from_name' , $email_from_name, $this );
			return wp_specialchars_decode( esc_html( $email_from_name ), ENT_QUOTES );
		}

		/**
		 * Get from email address.
		 *
		 * @return string
		 */
		public function get_from_address( $from_email = '') {
			$email_from_email = ( isset($this->settings['email_from_email']) && $this->settings['email_from_email'] != '' ) ? $this->settings['email_from_email'] : $from_email;
			$email_from_email  = apply_filters( 'ywraq_request_a_quote_send_email_from_address' , $email_from_email, $this );
			return sanitize_email( $email_from_email );
		}



		/**
		 * Init form fields to display in WC admin pages
		 *
		 * @return void
		 * @since  1.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function init_form_fields() {
			$this->form_fields = array(
				'enabled' => array(
					'title'         => __( 'Enable/Disable', 'yith-woocommerce-request-a-quote' ),
					'type'          => 'checkbox',
					'label'         => __( 'Enable this email notification', 'yith-woocommerce-request-a-quote' ),
					'default'       => 'yes'
				),
				'email_from_name'    => array(
					'title'       => __( '"From" Name', 'yith-woocommerce-request-a-quote' ),
					'type'        => 'text',
					'description' => '',
					'placeholder' => '',
					'default'     => get_option( 'woocommerce_email_from_name' )
				),
				'email_from_email'    => array(
					'title'       => __( '"From" Email Address', 'yith-woocommerce-request-a-quote' ),
					'type'        => 'text',
					'description' => '',
					'placeholder' => '',
					'default'     => get_option( 'woocommerce_email_from_address' )
				),
				'subject'    => array(
					'title'       => __( 'Subject', 'yith-woocommerce-request-a-quote' ),
					'type'        => 'text',
					'description' => sprintf( __( 'This field lets you edit email subject line. Leave it blank to use default subject text: <code>%s</code>. You can use {quote_number} as a placeholder that will show the quote number in the quote,<br>{quote_user} to show the customer\'s name, {quote_email} to show the customer\'s email', 'yith-woocommerce-request-a-quote' ), $this->subject ),
					'placeholder' => '',
					'default'     => ''
				),
				'recipient'  => array(
					'title'       => __( 'Recipient(s)', 'yith-woocommerce-request-a-quote' ),
					'type'        => 'text',
					'description' => sprintf( __( 'Enter recipients (separated by commas) for this email. Defaults to <code>%s</code>', 'yith-woocommerce-request-a-quote' ), esc_attr( get_option( 'admin_email' ) ) ),
					'placeholder' => '',
					'default'     => ''
				),
				'enable_cc'  => array(
					'title'       => __( 'Send CC copy', 'yith-woocommerce-request-a-quote' ),
					'type'        => 'checkbox',
					'description' => __( 'Send a carbon copy to the user', 'yith-woocommerce-request-a-quote' ),
					'default'     => 'no'
				),
				'heading'    => array(
					'title'       => __( 'Email Heading', 'yith-woocommerce-request-a-quote' ),
					'type'        => 'text',
					'description' => sprintf( __( 'This field lets you change the main heading in email notification. Leave it blank to use default heading type: <code>%s</code>.', 'yith-woocommerce-request-a-quote' ), $this->heading ),
					'placeholder' => '',
					'default'     => ''
				),

				'email-description'    => array(
					'title'       => __( 'Email Description', 'yith-woocommerce-request-a-quote' ),
					'type'        => 'textarea',
					'placeholder' => '',
					'default'     =>  __( 'You have received a request for a quote. The request is the following:', 'yith-woocommerce-request-a-quote')
				),
				'quote_detail_link'               => array(
					'title'    => __( 'Link to quote request details to be shown in "Request a Quote" email', 'yith-woocommerce-request-a-quote' ),
					'description'    => '',
					'id'      => 'ywraq_quote_detail_link',
					'class'			=> 'email_type wc-enhanced-select',
					'type'    => 'select',
					'options' => array(
						'myaccount' => __( 'Quote request details', 'yith-woocommerce-request-a-quote' ),
						'editor'    => __( 'Quote creation page (admin)', 'yith-woocommerce-request-a-quote' ),
					),
					'default' => 'myaccount'
				),
				'email_type' => array(
					'title' 		=> __( 'Email type', 'yith-woocommerce-request-a-quote' ),
					'type' 			=> 'select',
					'description' 	=> __( 'Choose email format.', 'yith-woocommerce-request-a-quote' ),
					'default' 		=> 'html',
					'class'			=> 'email_type wc-enhanced-select',
					'options'		=> $this->get_email_type_options()
				),

			);
		}
	}
}


// returns instance of the mail on file include
return new YITH_YWRAQ_Send_Email_Request_Quote();
