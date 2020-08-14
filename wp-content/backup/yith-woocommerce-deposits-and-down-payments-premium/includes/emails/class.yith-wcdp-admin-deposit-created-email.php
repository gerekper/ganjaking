<?php
/**
 * New deposit created email
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Deposits and Down Payments
 * @version 1.0.0
 */

/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'YITH_WCDP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCDP_Admin_Deposit_Created_Email' ) ) {
	/**
	 * New deposit created email
	 *
	 * @since 1.0.0
	 */
	class YITH_WCDP_Admin_Deposit_Created_Email extends YITH_WCDP_Emails {

		/**
		 * Constructor method, used to return object of the class to WC
		 *
		 * @return \YITH_WCDP_Admin_Deposit_Created_Email
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->id          = 'admin_new_deposit';
			$this->title       = __( 'Admin - new deposit', 'yith-woocommerce-deposits-and-down-payments' );
			$this->description = __( 'This email is sent to admins when a customer creates an order in which a deposit for one or more products has been paid', 'yith-woocommerce-deposits-and-down-payments' );

			$this->heading = __( 'New customer deposit', 'yith-woocommerce-deposits-and-down-payments' );
			$this->subject = __( '[{site_title}] New customer deposit ({order_number}) - {order_date}', 'yith-woocommerce-deposits-and-down-payments' );

			$this->template_html  = 'emails/admin-deposit-created-email.php';
			$this->template_plain = 'emails/plain/admin-deposit-created-email.php';

			// Triggers for this email
			add_action( 'woocommerce_order_status_completed_notification', array( $this, 'trigger' ), 10, 1 );
			add_action( 'woocommerce_order_status_processing_notification', array( $this, 'trigger' ), 10, 1 );

			// Call parent constructor
			parent::__construct();

			// Other settings
			$this->recipient = $this->get_option( 'recipient' );

			if ( ! $this->recipient ) {
				$this->recipient = get_option( 'admin_email' );
			}
		}

		/**
		 * Method triggered to send email
		 *
		 * @param $order_id int Original order id
		 *
		 * @return void
		 */
		public function trigger( $order_id ) {
			$this->object    = wc_get_order( $order_id );
			$this->customer  = $this->object->get_user();
			$this->suborders = YITH_WCDP_Suborders()->get_suborder( yit_get_prop( $this->object, 'id' ) );

			if ( ! $this->is_enabled() || ! $this->get_recipient() || ! $this->suborders ) {
				return;
			}

			$this->set_replaces();

			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}

		/**
		 * Check if mail is enabled
		 *
		 * @return bool Whether email notification is enabled or not
		 * @since 1.0.0
		 */
		public function is_enabled() {
			$notify_admin = get_option( 'yith_wcdp_notify_admin_deposit_created' );

			return $notify_admin == 'yes';
		}

		/**
		 * Init form fields to display in WC admin pages
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function init_form_fields() {
			$this->form_fields = array(
				'recipient'  => array(
					'title'       => __( 'Recipient(s)', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'        => 'text',
					'description' => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>.', 'yith-woocommerce-deposits-and-down-payments' ), esc_attr( get_option( 'admin_email' ) ) ),
					'placeholder' => '',
					'default'     => ''
				),
				'subject'    => array(
					'title'       => __( 'Subject', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'        => 'text',
					'description' => sprintf( __( 'This controls the email subject line. Leave it blank to use the default subject: <code>%s</code>.', 'yith-woocommerce-deposits-and-down-payments' ), $this->subject ),
					'placeholder' => '',
					'default'     => ''
				),
				'heading'    => array(
					'title'       => __( 'Email Heading', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'        => 'text',
					'description' => sprintf( __( 'This controls the main heading in the notification email. Leave it blank to use the default heading: <code>%s</code>.', 'yith-woocommerce-deposits-and-down-payments' ), $this->heading ),
					'placeholder' => '',
					'default'     => ''
				),
				'email_type' => array(
					'title'       => __( 'Email type', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'        => 'select',
					'description' => __( 'Choose a format for the email to send.', 'yith-woocommerce-deposits-and-down-payments' ),
					'default'     => 'html',
					'class'       => 'email_type wc-enhanced-select',
					'options'     => $this->get_email_type_options()
				)
			);
		}
	}
}

return new YITH_WCDP_Admin_Deposit_Created_Email();