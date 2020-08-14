<?php
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWRAC_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Implements features of YITH WooCommerce Recover Abandoned Cart
 *
 * @class   YITH_YWRAC_Send_Email_Recovered_Cart
 * @package YITH WooCommerce Recover Abandoned Cart
 * @since   1.0.0
 * @author YITH
 */
if ( ! class_exists( 'YITH_YWRAC_Send_Email_Recovered_Cart' ) ) {

	/**
	 * YITH_YWRAC_Send_Email_Recovered_Cart
	 *
	 * @since 1.0.0
	 */
	class YITH_YWRAC_Send_Email_Recovered_Cart extends WC_Email {

		/**
		 * Constructor method, used to return object of the class to WC
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->id          = 'ywrac_email_recovered_cart';
			$this->title       = __( 'Recovered Abandoned Cart Administrator', 'yith-woocommerce-recover-abandoned-cart' );
			$this->description = __( 'This is the email sent to the administrator when an order is submitted from a recover cart', 'yith-woocommerce-recover-abandoned-cart' );

			$this->heading  = get_option( 'ywrac_admin_sender_name' );
			$this->subject  = get_option( 'ywrac_admin_email_subject' );
			$this->reply_to = '';

			$this->template_html = 'email/email-recover-cart.php';

			if ( ! $this->recipient ) {
				$this->recipient = get_option( 'ywrac_admin_email_recipient' );
			}

			// Triggers for this email
			add_action( 'send_recovered_cart_mail_notification', array( $this, 'trigger' ), 15 );

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
			$this->order = wc_get_order( $args['order_id'] );
			$headers     = implode( ',', $this->get_headers() );
			$return      = $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content_html(), $headers, $this->get_attachments() );
		}

		/**
		 * get_headers function.
		 *
		 * @access public
		 * @return string
		 */
		public function get_headers() {

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
					'order'         => $this->order,
					'email_heading' => $this->heading,
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
return new YITH_YWRAC_Send_Email_Recovered_Cart();
