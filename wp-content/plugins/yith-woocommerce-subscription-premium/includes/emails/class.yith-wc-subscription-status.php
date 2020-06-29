<?php
if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Send Email to Administrator to Advice that a customer has paused/resumed/cancelled
 *
 * @class   YITH_WC_Subscription_Status
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */
if ( ! class_exists( 'YITH_WC_Subscription_Status' ) ) {

	/**
	 * YITH_WC_Subscription_Status
	 *
	 * @since 1.0.0
	 */
	class YITH_WC_Subscription_Status extends WC_Email {

		/**
		 * Constructor method, used to return object of the class to WC
		 *
		 * @since 1.0.0
		 */
		public function __construct() {

			$this->id          = 'ywsbs_subscription_admin_mail';
			$this->title       = __( 'Subscription Status', 'yith-woocommerce-subscription' );
			$this->description = __( 'This email is sent to the administrator to inform about a customer pausing/resuming a subscription', 'yith-woocommerce-subscription' );

			$this->heading  = __( 'A subscription status changed', 'yith-woocommerce-subscription' );
			$this->subject  = __( 'Subscription #{subscription_id} is now {status}', 'yith-woocommerce-subscription' );
			$this->reply_to = '';

			$this->email_type = 'html';

			$this->template_base = YITH_YWSBS_TEMPLATE_PATH . '/';
			$this->template_html = 'emails/email-subscription-status.php';

			// Triggers for this email
			add_action( 'ywsbs_subscription_paused_mail_notification', array( $this, 'trigger' ), 15 );
			add_action( 'ywsbs_subscription_resumed_mail_notification', array( $this, 'trigger' ), 15 );
			add_action( 'ywsbs_subscription_cancelled_mail_notification', array( $this, 'trigger' ), 15 );
			add_action( 'ywsbs_subscription_admin_mail_notification', array( $this, 'trigger' ), 15 );

			parent::__construct();

			if ( ! $this->email_type ) {
				$this->email_type = 'html';
			}

			// Other settings
			$this->recipient = $this->get_option( 'recipient' );

			if ( ! $this->recipient ) {
				$this->recipient = get_option( 'admin_email' );
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

			if ( ! $this->is_enabled() ) {
				return;
			}

			$this->object = $subscription;

			$status = ywsbs_get_status();
			if ( version_compare( WC()->version, '3.2.0', '<' ) ) {
				$this->find['subscription-id']        = '{subscription_id}';
				$this->find['subscription-status']    = '{status}';
				$this->replace['subscription-id']     = $subscription->id;
				$this->replace['subscription-status'] = isset( $status[ $subscription->status ] ) ? $status[ $subscription->status ] : $subscription->status;
			} else {
				$this->placeholders['{subscription_id}'] = $subscription->id;
				$this->placeholders['{status}']          = isset( $status[ $subscription->status ] ) ? $status[ $subscription->status ] : $subscription->status;
			}

			if ( ! is_array( $this->get_option( 'status' ) ) || ! in_array( $subscription->status, $this->get_option( 'status' ) ) ) {
				return;
			}

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
				'enabled'   => array(
					'title'   => __( 'Enable/Disable', 'yith-woocommerce-subscription' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable notification for this type of emails', 'yith-woocommerce-subscription' ),
					'default' => 'yes',
				),

				'recipient' => array(
					'title'       => __( 'Recipient(s)', 'yith-woocommerce-subscription' ),
					'type'        => 'text',
					'description' => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', 'yith-woocommerce-subscription' ), esc_attr( get_option( 'admin_email' ) ) ),
					'placeholder' => '',
					'default'     => '',
					'desc_tip'    => true,
				),

				'heading'   => array(
					'title'       => __( 'Email heading', 'yith-woocommerce-subscription' ),
					'type'        => 'text',
					'description' => sprintf( __( 'Defaults to <code>%s</code>', 'yith-woocommerce-subscription' ), $this->heading ),
					'placeholder' => '',
					'default'     => '',
				),

				'status'    => array(
					'title'       => __( 'Send email for these status', 'yith-woocommerce-subscription' ),
					'type'        => 'multiselect',
					'description' => __( 'Choose which status of subscription to send.', 'yith-woocommerce-subscription' ),
					'default'     => array( 'expired', 'cancelled' ),
					'class'       => 'wc-enhanced-select',
					'options'     => ywsbs_get_status(),
					'desc_tip'    => true,
				),
			);
		}


	}
}


// returns instance of the mail on file include
return new YITH_WC_Subscription_Status();
