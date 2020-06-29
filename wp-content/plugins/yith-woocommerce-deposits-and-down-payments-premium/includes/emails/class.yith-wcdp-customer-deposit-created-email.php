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

if ( ! class_exists( 'YITH_WCDP_Customer_Deposit_Created_Email' ) ) {
	/**
	 * New deposit created email email
	 *
	 * @since 1.0.0
	 */
	class YITH_WCDP_Customer_Deposit_Created_Email extends YITH_WCDP_Emails {

		/**
		 * Constructor method, used to return object of the class to WC
		 *
		 * @return \YITH_WCDP_Customer_Deposit_Created_Email
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->id          = 'new_deposit';
			$this->title       = __( 'New deposit created', 'yith-woocommerce-deposits-and-down-payments' );
			$this->description = __( 'This email is sent to customers when they create an order with one or more deposit', 'yith-woocommerce-deposits-and-down-payments' );

			$this->heading = __( 'Balance for one or more orders have to be paid', 'yith-woocommerce-deposits-and-down-payments' );
			$this->subject = __( 'Balance for one or more orders have to be paid', 'yith-woocommerce-deposits-and-down-payments' );

			$this->content_html = $this->get_option( 'content_html', __( "<p>Hi there. Your order #{order_number} is being currently processed, and its actual status is {order_status}.</p>
<p>The following down payments can already be paid:</p>
{deposit_list}", 'yith-woocommerce-deposits-and-down-payments' ) );
			$this->content_text = $this->get_option( 'content_text', __( "Hi there. Your order #{order_number} is being currently processed, and its actual status is {order_status}.
The following down payments can already be paid:\n
{deposit_list}" ) );

			$this->template_html  = 'emails/customer-deposit-created-email.php';
			$this->template_plain = 'emails/plain/customer-deposit-created-email.php';

			// Triggers for this email
			add_action( 'woocommerce_order_status_completed_notification', array( $this, 'trigger' ), 10, 1 );
			add_action( 'woocommerce_order_status_processing_notification', array( $this, 'trigger' ), 10, 1 );

			add_action( 'yith_wcdp_deposits_created_notification', array( $this, 'trigger' ), 10, 1 );

			// Call parent constructor
			parent::__construct();
		}

		/**
		 * Method triggered to send email
		 *
		 * @param $order_id int Order id
		 *
		 * @return void
		 */
		public function trigger( $order_id ) {
			$this->object    = wc_get_order( $order_id );
			$this->recipient = yit_get_prop( $this->object, 'billing_email' );
			$this->customer  = $this->object->get_user();
			$this->suborders = YITH_WCDP_Suborders()->get_suborder( yit_get_prop( $this->object, 'id' ) );

			if ( ! $this->is_enabled() || ! $this->get_recipient() || ! $this->suborders ) {
				return;
			}

			$this->set_replaces();

			if ( version_compare( wc()->version, '3.2.0', '>=' ) ) {
				$this->placeholders = array_merge(
					$this->placeholders,
					array(
						'{content_html}' => $this->format_string( $this->content_html ),
						'{content_text}' => $this->format_string( $this->content_text )
					)
				);
			} else {
				$this->find['content-html'] = '{content_html}';
				$this->find['content-text'] = '{content_text}';

				$this->replace['content-html'] = $this->format_string( $this->content_html );
				$this->replace['content-text'] = $this->format_string( $this->content_text );
			}

			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}

		/**
		 * Check if mail is enabled
		 *
		 * @return bool Whether email notification is enabled or not
		 * @since 1.0.0
		 */
		public function is_enabled() {
			$notify_admin = get_option( 'yith_wcdp_notify_customer_deposit_created' );

			return apply_filters( 'yith_wcdp_customer_deposit_created_email_enabled', $notify_admin == 'yes' );
		}

		/**
		 * Init form fields to display in WC admin pages
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function init_form_fields() {
			$this->form_fields = array(
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
				),

				'content_html' => array(
					'title'       => __( 'Email HTML Content', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'        => 'textarea',
					'description' => __( 'This field lets you modify the main content of the HTML email. You can use the following placeholders: <code>{order_id}</code> <code>{order_date}</code> <code>{order_state}</code> <code>{customer_name}</code> <code>{customer_login}</code> <code>{customer_email}</code> <code>{suborder_list}</code> <code>{suborder_table}</code>', 'yith-woocommerce-deposits-and-down-payments' ),
					'placeholder' => '',
					'css'         => 'min-height: 250px;',
					'default'     => __( "<p>Hi there. Your order #{order_number} is currently is being currently processed, and its actual status is {order_status}.</p>
<p>The following down payments can already be paid:</p>
{deposit_list}", 'yith-woocommerce-deposits-and-down-payments' )
				),

				'content_text' => array(
					'title'       => __( 'Email Text Content', 'yith-woocommerce-deposits-and-down-payments' ),
					'type'        => 'textarea',
					'description' => __( 'This field lets you modify the main content of the text email. You can use the following placeholders: <code>{order_id}</code> <code>{order_date}</code> <code>{order_state}</code> <code>{customer_name}</code> <code>{customer_login}</code> <code>{customer_email}</code> <code>{suborder_list}</code> <code>{suborder_table}</code>', 'yith-woocommerce-deposits-and-down-payments' ),
					'placeholder' => '',
					'css'         => 'min-height: 250px;',
					'default'     => __( "Hi there. Your order #{order_number} is currently is being currently processed, and its actual status is {order_status}.
The following down payments can already be paid:\n
{deposit_list}", 'yith-woocommerce-deposits-and-down-payments' )
				)
			);
		}
	}
}

return new YITH_WCDP_Customer_Deposit_Created_Email();