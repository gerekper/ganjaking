<?php
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Send Email to Administrator to Advice that a customer has paused/resumed/cancelled
 *
 * @class   YITH_WC_Customer_Subscription
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YITH_WC_Customer_Subscription' ) ) {

	/**
	 * YITH_WC_Customer_Subscription_Cancelled
	 *
	 * @since 1.0.0
	 */
	class YITH_WC_Customer_Subscription extends WC_Email {

		/**
		 * Constructor method, used to return object of the class to WC
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			// Cancel sending emails if this is a duplicate website
			if ( ! apply_filters( 'ywsbs_send_email_in_main_site', YITH_WC_Subscription()->is_main_site() ) ) {
				return;
			}

			// Send a copy to admin?
			$this->send_to_admin = $this->get_option( 'send_to_admin' );

			// Triggers for this email
			$this->template_base = YITH_YWSBS_TEMPLATE_PATH . '/';
			$this->email_type    = 'html';
			$this->template_html = 'emails/' . $this->id . '.php';

			add_action( $this->id . '_mail_notification', array( $this, 'trigger' ), 15 );

			// Call parent constructor
			parent::__construct();

			$this->customer_email = true;
			// Other settings
			$this->recipient = $this->get_option( 'recipient' );

			if ( ! $this->recipient ) {
				$this->recipient = get_option( 'admin_email' );
			}

			if ( ! $this->email_type ) {
				$this->email_type = 'html';
			}

		}

		/**
		 * Method triggered to send email
		 *
		 * @param int $subscription
		 *
		 * @return void
		 * @since  1.0
		 * @author Emanuela Castorina <emanuela.castorina@yithemes.com>
		 */
		public function trigger( $subscription ) {

			$this->recipient = $subscription->get_billing_email();

			// Check if this email type is enabled, recipient is set
			if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
				return;
			}

			$this->object = $subscription;
			$return       = $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content_html(), $this->get_headers(), $this->get_attachments() );
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
					'subscription'  => $this->object,
					'email_heading' => $this->heading,
					'sent_to_admin' => true,
					'plain_text'    => false,
					'email'         => $this,
				),
				'',
				$this->template_base
			);

			return ob_get_clean();
		}


		/**
		 * Initialise settings form fields
		 *
		 * @access public
		 * @return void
		 */
		function init_form_fields() {
			$this->form_fields = array(
				'enabled'       => array(
					'title'   => __( 'Enable/Disable', 'yith-woocommerce-subscription' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable notification for this type of emails', 'yith-woocommerce-subscription' ),
					'default' => 'yes',
				),
				'subject'       => array(
					'title'       => __( 'Subject', 'yith-woocommerce-subscription' ),
					'type'        => 'text',
					'description' => sprintf( __( 'Defaults to <code>%s</code>', 'yith-woocommerce-subscription' ), $this->subject ),
					'placeholder' => '',
					'default'     => '',
				),

				'send_to_admin' => array(
					'title'   => __( 'Send to admin?', 'yith-woocommerce-subscription' ),
					'type'    => 'checkbox',
					'label'   => __( 'Send a copy of this email to admin', 'yith-woocommerce-subscription' ),
					'default' => 'no',
				),
				'heading'       => array(
					'title'       => __( 'Email heading', 'yith-woocommerce-subscription' ),
					'type'        => 'text',
					'description' => sprintf( __( 'Defaults to <code>%s</code>', 'yith-woocommerce-subscription' ), $this->heading ),
					'placeholder' => '',
					'default'     => '',
				),

			);
		}
	}
}

