<?php
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAC_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements features of YITH WooCommerce Recover Abandoned Cart
 *
 * @class   YITH_YWRAC_Send_Email
 * @package YITH WooCommerce Recover Abandoned Cart
 * @since   1.0.0
 * @author YITH
 */
if ( ! class_exists( 'YITH_YWRAC_Send_Email' ) ) {

	/**
	 * YITH_YWRAC_Send_Email
	 *
	 * @since 1.0.0
	 */
	class YITH_YWRAC_Send_Email extends WC_Email {

		/**
		 * Constructor method, used to return object of the class to WC
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->id          = 'ywrac_email';
			$this->title       = __( 'Recover Abandoned Cart Email', 'yith-woocommerce-recover-abandoned-cart' );
			$this->description = _x( 'This is the email sent to the customer from the admin with the YITH WooCommerce Recover Abandoned Cart plugin', 'do not translate plugin name', 'yith-woocommerce-recover-abandoned-cart' );

			$this->heading  = get_option( 'ywrac_email_sender_name' );
			$this->reply_to = get_option( 'ywrac_email_sender' );

			$this->customer_email = true;

			// Triggers for this email
			add_action( 'send_rac_mail_notification', array( $this, 'trigger' ), 15 );

			// Call parent constructor
			parent::__construct();
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

			$this->recipient     = $args['user_email'];
			$this->email_content = $args['email_content'];
			$this->subject       = ( isset( $args['email_subject'] ) ? $args['email_subject'] : get_option( 'ywrac_email_subject' ) );
			$this->type          = $args['type'];

			$this->template_html = apply_filters( 'ywrac_email_template', 'email/email-template.php', $this, $args );

			$this->email_title = get_the_title( $args['email_id'] );

			if ( ! isset( $args['email_test'] ) ) {

				$return = $this->send( $this->get_recipient(), $this->subject, $this->get_content_html(), $this->get_headers(), $this->get_attachments() );

				$date = date( 'Y-m-d H:i:s', ywrac_get_timestamp() );
				update_post_meta( $args['cart_id'], '_email_sent', $date );

				// update cart meta '_emails_sent'
				$emails_sent = get_post_meta( $args['cart_id'], '_emails_sent', true );
				$emails_sent = empty( $emails_sent ) ? array() : $emails_sent;

				$email_id = $args['email_id'];
				global $sitepress;
				$has_wpml = ! empty( $sitepress ) ? true : false;
				if ( $has_wpml ) {
					$email_id = yit_wpml_object_id( $args['email_id'], 'ywrac_email', true );
				}

				$emails_sent[ $email_id ] = array(
					'email_id'   => $email_id,
					'email_name' => $args['email_name'],
					'data_sent'  => $date,
					'clicked'    => 0,
				);
				update_post_meta( $args['cart_id'], '_emails_sent', $emails_sent );

				if ( $return ) {

					// update email template meta '_email_sent_counter'
					YITH_WC_Recover_Abandoned_Cart_Helper()->update_counter_meta( $args['email_id'], '_email_sent_counter' );

					// update general email sent counter
					YITH_WC_Recover_Abandoned_Cart_Helper()->update_counter( 'email_sent_counter' );
					YITH_WC_Recover_Abandoned_Cart_Helper()->update_counter( 'email_sent_' . $this->type . '_counter' );

					// update logs
					YITH_WC_Recover_Abandoned_Cart_Helper()->email_log( $args['user_email'], $args['email_id'], $args['cart_id'], $date );

				}
			} else {
				$headers = implode( ',', $this->get_headers() );
				$return  = $this->send( $this->get_recipient(), $this->subject, $this->get_content_html(), $headers, $this->get_attachments() );

				if ( $return ) {
					update_post_meta( $args['email_id'], '_email_test_sent', 1 );
				}
			}

		}

		/**
		 * get_headers function.
		 *
		 * @access public
		 * @return string
		 */
		public function get_headers() {

			$headers = array();

			if ( $this->reply_to != '' ) {
				$headers[] = 'Reply-To: ' . $this->reply_to;
			}

			$headers[] = 'Content-Type: ' . $this->get_content_type();

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
			wc_get_template(
				$this->template_html,
				array(
					'email_content' => $this->email_content,
					'email_heading' => $this->email_title,
					'sent_to_admin' => true,
					'plain_text'    => false,
					'email'         => $this,
				)
			);

			return ob_get_clean();
		}


	}
}


// returns instance of the mail on file include
return new YITH_YWRAC_Send_Email();
