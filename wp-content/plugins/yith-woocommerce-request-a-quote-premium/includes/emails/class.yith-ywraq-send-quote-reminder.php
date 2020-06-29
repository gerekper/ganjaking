<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAQ_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements the YITH_YWRAQ_Send_Quote_Reminder class.
 *
 * @class   YITH_YWRAQ_Send_Quote_Reminder
 * @package YITH
 * @since   2.1.0
 * @author  YITH
 */
if ( ! class_exists( 'YITH_YWRAQ_Send_Quote_Reminder' ) ) {

	/**
	 * YITH_YWRAQ_Send_Quote_Reminder
	 *
	 * @since   2.1.0
	 */
	class YITH_YWRAQ_Send_Quote_Reminder extends WC_Email {

		/**
		 * Constructor method, used to return object of the class to WC
		 * @return mixed
		 * @since   2.1.0
		 */
		public function __construct() {
			$this->id          = 'ywraq_send_quote_reminder';
			$this->title       = __( '[YITH Request a Quote] Reminder: expiring quote', 'yith-woocommerce-request-a-quote' );
			$this->description = __( 'This email is sent when to the customer when the quote is going to expire.', 'yith-woocommerce-request-a-quote' );

			$this->heading = __( 'Reminder: expiring quote', 'yith-woocommerce-request-a-quote' );
			$this->subject = __( 'Reminder: expiring quote', 'yith-woocommerce-request-a-quote' );

			$this->template_base  = YITH_YWRAQ_TEMPLATE_PATH . '/';
			$this->template_html  = 'emails/quote-reminder.php';
			$this->template_plain = 'emails/plain/quote-reminder.php';
			if ( $this->enabled == 'no' ) {
				return;
			}

			global $woocommerce_wpml;

			$is_wpml_configured = apply_filters( 'wpml_setting', false, 'setup_complete' );
			if ( $is_wpml_configured && defined( 'WCML_VERSION' ) && $woocommerce_wpml ) {
				add_action( 'send_quote_mail_notification', array( $this, 'refresh_email_lang' ), 10, 1 );
			}

			// Triggers for this email
			add_action( 'send_reminder_quote_mail_notification', array( $this, 'trigger' ), 15, 2 );

			$this->customer_email = true;
			// Call parent constructor
			parent::__construct();

			//$this->recipient = ( isset($this->settings['recipient']) && $this->settings['recipient'] != '' ) ? $this->settings['recipient'] : get_option( 'admin_email' );

			$this->enable_bcc = $this->get_option( 'enable_bcc' );
			$this->enable_bcc = $this->enable_bcc == 'yes';
		}

		/**
		 * @param $order_id
		 */
		function refresh_email_lang( $order_id ) {
			global $sitepress;
			if ( is_array( $order_id ) ) {
				if ( isset( $order_id['order_id'] ) ) {
					$order_id = $order_id['order_id'];
				} else {
					return;
				}

			}

			$order = wc_get_order( $order_id );
			$lang  = yit_get_prop( $order, 'wpml_language', true );
			if ( ! empty( $lang ) ) {
				$sitepress->switch_lang( $lang, true );
			}

		}

		/**
		 * Method triggered to send email
		 *
		 * @param $order_id
		 *
		 * @internal param int $args
		 *
		 * @since    2.1.0
		 * @author   Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function trigger( $order_id ) {

			$order = wc_get_order( $order_id );
			if ( $order ) {
				$exdata              = yit_get_prop( $order, '_ywcm_request_expire', true );
				$expired_data        = strtotime( $exdata );
				$day_before_expiring = (int) $this->get_option( 'days_before_expire' );
				$time_to_check       = $expired_data - ( $day_before_expiring * DAY_IN_SECONDS );

				//the cron is scheduled daily so the email is send around a day
				if ( ( $time_to_check ) <= time() && time() < ( $time_to_check + DAY_IN_SECONDS ) && 'ywraq-pending' == $order->get_status() ) {

					$order_date                    = yit_get_prop( $order, '_date_created', true );
					$on                            = $order->get_order_number();
					$this->order                   = $order;
					$this->raq['customer_message'] = yit_get_prop( $order, 'ywraq_customer_message', true );
					$this->raq['admin_message']    = nl2br( yit_get_prop( $order, '_ywcm_request_response', true ) );
					$this->raq['user_email']       = yit_get_prop( $order, 'ywraq_customer_email', true );
					$this->raq['user_name']        = yit_get_prop( $order, 'ywraq_customer_name', true );
					$this->raq['expiration_data']  = ( $exdata != '' ) ? date_i18n( wc_date_format(), strtotime( $exdata ) ) : '';
					$this->raq['order-date']       = date_i18n( wc_date_format(), strtotime( $order_date ) );
					$this->raq['order-id']         = $order_id;
					$this->raq['order-number']     = ! empty( $on ) ? $on : $order_id;
					$this->raq['lang']             = yit_get_prop( $order, 'wpml_language', true );

					$this->object                         = $order;
					$this->recipient                      = $this->raq['user_email'];
					$this->placeholders['{quote_number}'] = apply_filters( 'ywraq_quote_number', $this->raq['order-id'] );

					$this->heading = apply_filters( 'wpml_translate_single_string', $this->heading, 'admin_texts_woocommerce_ywraq_send_quote_reminder_settings', '[woocommerce_ywraq_send_quote_reminder_settings]heading', $this->raq['lang'] );
					$this->subject = apply_filters( 'wpml_translate_single_string', $this->subject, 'admin_texts_woocommerce_ywraq_send_quote_reminder_settings', '[woocommerce_ywraq_send_quote_reminder_settings]subject', $this->raq['lang'] );

					$this->send( $this->recipient, $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
				}
			}

		}

		/**
		 * @return array
		 */
		public function get_attachments() {
			$order_id    = $this->order->get_id();
			$attachments = array();
			if ( get_option( 'ywraq_pdf_attachment' ) == 'yes' ) {
				$attachments[] = YITH_Request_Quote_Premium()->get_pdf_file_path( $order_id );
			}

			if ( '' != $optional_upload = yit_get_prop( $this->order, '_ywraq_optional_attachment', true ) ) {
				$attachment_id = ywraq_get_attachment_id_by_url( $optional_upload );
				$path          = ( $attachment_id ) ? get_attached_file( $attachment_id ) : null;
				if ( file_exists( $path ) ) {
					$attachments[] = $path;
				}
			}

			return apply_filters( 'woocommerce_email_attachments', $attachments, $this->id, $this->object );
		}

		/**
		 * get_headers function.
		 *
		 * @access public
		 * @return string
		 */
		function get_headers() {

			$cc = ( isset( $this->settings['recipient'] ) && $this->settings['recipient'] != '' ) ? $this->settings['recipient'] : get_option( 'admin_email' );

			$headers = array();

			if ( get_option( 'woocommerce_email_from_address' ) != '' ) {
				$headers[] = "Reply-To: " . $this->get_from_address();
			}

			if ( $this->enable_bcc ) {
				$headers[] = "Bcc: " . $cc . "\r\n";
			}

			$headers[] = "Content-Type: " . $this->get_content_type();

			return apply_filters( 'woocommerce_email_headers', $headers, $this->id, $this->object );
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
				'order'             => $this->order,
				'email_heading'     => $this->get_heading(),
				'raq_data'          => $this->raq,
				'email_title'       => $this->get_option( 'email-title' ),
				'email_description' => $this->get_option( 'email-description' ),
				'sent_to_admin'     => true,
				'plain_text'        => false,
				'email'             => $this
			), false, $this->template_base );

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
				'order'             => $this->order,
				'raq_data'          => $this->raq,
				'email_title'       => $this->get_option( 'email-title' ),
				'email_description' => $this->get_option( 'email-description' ),
				'sent_to_admin'     => true,
				'plain_text'        => false,
				'email'             => $this
			), false, $this->template_base );

			return ob_get_clean();
		}

		/**
		 * Get from name for email.
		 *
		 * @return string
		 */
		public function get_from_name( $from_name = '' ) {
			$email_from_name = ( isset( $this->settings['email_from_name'] ) && $this->settings['email_from_name'] != '' ) ? $this->settings['email_from_name'] : $from_name;

			return wp_specialchars_decode( esc_html( $email_from_name ), ENT_QUOTES );
		}

		/**
		 * Get from email address.
		 *
		 * @return string
		 */
		public function get_from_address( $from_email = '' ) {
			$email_from_email = ( isset( $this->settings['email_from_email'] ) && $this->settings['email_from_email'] != '' ) ? $this->settings['email_from_email'] : $from_email;

			return sanitize_email( $email_from_email );
		}

		/**
		 * Init form fields to display in WC admin pages
		 *
		 * @return void
		 * @since   2.1.0
		 * @author  Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'            => array(
					'title'   => __( 'Enable/Disable', 'yith-woocommerce-request-a-quote' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable this email notification', 'yith-woocommerce-request-a-quote' ),
					'default' => 'yes'
				),
				'days_before_expire' => array(
					'title'       => __( 'Days (to pass) before sending the email: ', 'yith-woocommerce-request-a-quote' ),
					'type'        => 'text',
					'description' => __( 'Number of days before quote expiration to send the email', 'yith-woocommerce-request-a-quote' ),
					'placeholder' => '',
					'css'         => 'width:50px;',
					'default'     => 2
				),
				'email_from_name'    => array(
					'title'       => __( '"From" Name', 'yith-woocommerce-request-a-quote' ),
					'type'        => 'text',
					'description' => '',
					'placeholder' => '',
					'default'     => get_option( 'woocommerce_email_from_name' )
				),
				'email_from_email'   => array(
					'title'       => __( '"From" Email Address', 'yith-woocommerce-request-a-quote' ),
					'type'        => 'text',
					'description' => '',
					'placeholder' => '',
					'default'     => get_option( 'woocommerce_email_from_address' )
				),
				'subject'            => array(
					'title'       => __( 'Subject', 'yith-woocommerce-request-a-quote' ),
					'type'        => 'text',
					'description' => sprintf( __( 'This field lets you modify the email subject line. Leave it blank to use the default subject text: <code>%s</code>. You can use {quote_number} as a placeholder that will show the quote number in the quote.', 'yith-woocommerce-request-a-quote' ), $this->subject ),
					'placeholder' => '',
					'default'     => ''
				),
				'recipient'          => array(
					'title'       => __( 'Bcc Recipient(s)', 'yith-woocommerce-request-a-quote' ),
					'type'        => 'text',
					'description' => __( 'Enter futher recipients (separated by commas) for this email. By default email to the customer', 'yith-woocommerce-request-a-quote' ),
					'placeholder' => '',
					'default'     => ''
				),
				'enable_bcc'         => array(
					'title'       => __( 'Send BCC copy', 'yith-woocommerce-request-a-quote' ),
					'type'        => 'checkbox',
					'description' => __( 'Send a blind carbon copy to the administrator', 'yith-woocommerce-request-a-quote' ),
					'default'     => 'no'
				),
				'heading'            => array(
					'title'       => __( 'Email Heading', 'yith-woocommerce-request-a-quote' ),
					'type'        => 'text',
					'description' => sprintf( __( 'This field lets you change the main heading in email notification. Leave it blank to use default heading type: <code>%s</code>.', 'yith-woocommerce-request-a-quote' ), $this->heading ),
					'placeholder' => '',
					'default'     => ''
				),
				'email-title'        => array(
					'title'       => __( 'Email Title', 'yith-woocommerce-request-a-quote' ),
					'type'        => 'text',
					'placeholder' => '',
					'default'     => __( 'Expiring Proposal', 'yith-woocommerce-request-a-quote' )
				),
				'email-description'  => array(
					'title'       => __( 'Email Description', 'yith-woocommerce-request-a-quote' ),
					'type'        => 'textarea',
					'placeholder' => '',
					'default'     => __( 'You have received this email because your quote request on our store is about to expire and will be no longer available. Our best proposal is the following:', 'yith-woocommerce-request-a-quote' )
				),
				'email_type'         => array(
					'title'       => __( 'Email type', 'yith-woocommerce-request-a-quote' ),
					'type'        => 'select',
					'description' => __( 'Choose email format.', 'yith-woocommerce-request-a-quote' ),
					'default'     => 'html',
					'class'       => 'email_type wc-enhanced-select',
					'options'     => $this->get_email_type_options()
				),
			);
		}
	}
}


// returns instance of the mail on file include
return new YITH_YWRAQ_Send_Quote_Reminder();
