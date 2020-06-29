<?php
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Send Email to Customer
 *
 * @class   YITH_WC_Customer_Subscription_Renew_Reminder
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YITH_WC_Customer_Subscription_Renew_Reminder' ) ) {

	/**
	 * YITH_WC_Customer_Subscription_Renew_Reminder
	 *
	 * @since 1.0.0
	 */
	class YITH_WC_Customer_Subscription_Renew_Reminder extends YITH_WC_Customer_Subscription {

		/**
		 * Constructor method, used to return object of the class to WC
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			$this->id          = 'ywsbs_customer_subscription_renew_reminder';
			$this->title       = __( 'Subscription Renew Reminder', 'yith-woocommerce-subscription' );
			$this->description = __( 'This email is sent to the customer as a reminder for the next payment', 'yith-woocommerce-subscription' );
			$this->email_type  = 'html';
			$this->heading     = __( 'Subscription Renew Reminder', 'yith-woocommerce-subscription' );
			$this->subject     = __( 'Reminder for the order renewal {order_number}', 'yith-woocommerce-subscription' );

			// Call parent constructor
			parent::__construct();

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
			if ( ! $this->is_enabled() || ! $this->get_recipient() || $subscription->order_id == 0 ) {
				return;
			}

			$order = wc_get_order( $subscription->order_id );

			if ( ! $order ) {
				return;
			}

			$this->object = $subscription;
			$this->order  = $order;

			if ( version_compare( WC()->version, '3.2.0', '<' ) ) {
				// Replace macros
				$this->find['order-number']    = '{order_number}';
				$this->replace['order-number'] = $order->get_order_number();
			} else {
				$this->placeholders['{order_number}'] = $order->get_order_number();
			}

			$next_activity_date = $subscription->payment_due_date;

			$this->template_variables = array(
				'subscription'       => $this->object,
				'order'              => $this->order,
				'email_heading'      => $this->get_heading(),
				'sent_to_admin'      => false,
				'next_activity'      => __( 'Renew', 'yith-woocommerce-subscription' ),
				'next_activity_date' => $next_activity_date,
				'email'              => $this,
			);

			$return = $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content_html(), $this->get_headers(), $this->get_attachments() );
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
			wc_get_template( $this->template_html, $this->template_variables, '', $this->template_base );
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

				'delay'         => array(
					'title'       => __( 'Number of days before next subscription payment.', 'yith-woocommerce-subscription' ),
					'type'        => 'number',
					'css'         => 'width:50px;',
					'description' => __( 'Specify the number of days before next subscription payment to send this email.', 'yith-woocommerce-subscription' ),
					'placeholder' => '',
					'default'     => '15',
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


// returns instance of the mail on file include
return new YITH_WC_Customer_Subscription_Renew_Reminder();
