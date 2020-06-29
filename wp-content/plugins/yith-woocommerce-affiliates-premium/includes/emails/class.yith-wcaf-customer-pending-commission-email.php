<?php
/**
 * Pending Commission class
 *
 * @author  Your Inspiration Themes
 * @package YITH WooCommerce Affiliates
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

if ( ! defined( 'YITH_WCAF' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCAF_Customer_Pending_Commission_Email' ) ) {
	/**
	 * New affiliate email
	 *
	 * @since 1.0.0
	 */
	class YITH_WCAF_Customer_Pending_Commission_Email extends WC_Email {

		/**
		 * Constructor method, used to return object of the class to WC
		 *
		 * @return \YITH_WCAF_Customer_Pending_Commission_Email
		 * @since 1.0.0
		 */
		public function __construct() {
			$this->id             = 'customer_pending_commission';
			$this->title          = __( 'Affiliate\' Pending Commission', 'yith-woocommerce-affiliates' );
			$this->description    = __( 'This email is sent to customers each time a commission status switches to "pending"', 'yith-woocommerce-affiliates' );
			$this->customer_email = true;

			$this->heading = __( 'Your commission is awaiting payment', 'yith-woocommerce-affiliates' );
			$this->subject = __( 'Your {site_title} commission from {commission_date} is awaiting payment', 'yith-woocommerce-affiliates' );

			$this->content_html = $this->get_option( 'content_html' );
			$this->content_text = $this->get_option( 'content_text' );

			$this->template_html  = 'emails/customer-pending-commission-email.php';
			$this->template_plain = 'emails/plain/customer-pending-commission-email.php';

			// Triggers for this email
			add_action( 'yith_wcaf_commission_status_pending_notification', array( $this, 'trigger' ), 10, 1 );

			// Call parent constructor
			parent::__construct();
		}

		/**
		 * Method triggered to send email
		 *
		 * @param $affiliate_id int New affiliate id
		 *
		 * @return void
		 */
		public function trigger( $commission_id ) {
			$this->object = YITH_WCAF_Commission_Handler()->get_commission( $commission_id );

			if ( version_compare( wc()->version, '3.2.0', '>=' ) ) {
				$this->placeholders = array_merge(
					$this->placeholders,
					array(
						'{commission_date}'   => date_i18n( wc_date_format(), strtotime( $this->object['created_at'] ) ),
						'{commission-number}' => $this->object['ID']
					)
				);
			} else {
				$this->find['commission-date']   = '{commission_date}';
				$this->find['commission-number'] = '{commission-number}';

				$this->replace['commission-date']   = date_i18n( wc_date_format(), strtotime( $this->object['created_at'] ) );
				$this->replace['commission-number'] = $this->object['ID'];
			}

			$this->recipient = $this->object['user_email'];

			if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
				return;
			}

			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}

		/**
		 * Return formatted email heading
		 *
		 * @return string Formatted heading
		 * @since 1.0.0
		 */
		function get_heading() {
			return $this->format_string( $this->heading );
		}

		/**
		 * Check if mail is enabled
		 *
		 * @return bool Whether email notification is enabled or not
		 * @since 1.0.0
		 */
		public function is_enabled() {
			if ( ! $this->object['user_id'] ) {
				return false;
			}

			$notify_user = get_user_meta( $this->object['user_id'], '_yith_wcaf_notify_pending_commission', true );

			return apply_filters( 'yith_wcaf_notify_user_pending_commission', $notify_user == 'yes', $this->object['user_id'] );
		}

		/**
		 * Get HTML content for the mail
		 *
		 * @return string HTML content of the mail
		 * @since 1.0.0
		 */
		public function get_content_html() {
			$order = wc_get_order( $this->object['order_id'] );
			ob_start();
			yith_wcaf_get_template( $this->template_html, array(
				'commission'    => $this->object,
				'currency'      => apply_filters( 'yith_wcaf_email_currency', $order ? $order->get_currency() : get_woocommerce_currency(), $this ),
				'affiliate'     => YITH_WCAF_Affiliate_Handler()->get_affiliate_by_id( $this->object['affiliate_id'] ),
				'email_heading' => $this->get_heading(),
				'email'         => $this,
				'sent_to_admin' => true,
				'plain_text'    => false
			) );

			return ob_get_clean();
		}

		/**
		 * Get plain text content of the mail
		 *
		 * @return string Plain text content of the mail
		 * @since 1.0.0
		 */
		public function get_content_plain() {
			$order = wc_get_order( $this->object['order_id'] );

			ob_start();
			yith_wcaf_get_template( $this->template_plain, array(
				'commission'    => $this->object,
				'currency'      => apply_filters( 'yith_wcaf_email_currency', $order ? $order->get_currency() : get_woocommerce_currency(), $this ),
				'affiliate'     => YITH_WCAF_Affiliate_Handler()->get_affiliate_by_id( $this->object['affiliate_id'] ),
				'email_heading' => $this->get_heading(),
				'email'         => $this,
				'sent_to_admin' => true,
				'plain_text'    => true
			) );

			return ob_get_clean();
		}

		/**
		 * Init form fields to display in WC admin pages
		 *
		 * @return void
		 * @since 1.0.0
		 */
		public function init_form_fields() {
			$this->form_fields = array(
				'subject'      => array(
					'title'       => __( 'Subject', 'woocommerce' ),
					'type'        => 'text',
					'description' => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce' ), $this->subject ),
					'placeholder' => '',
					'default'     => ''
				),
				'heading'      => array(
					'title'       => __( 'Email Heading', 'woocommerce' ),
					'type'        => 'text',
					'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave blank to use the default heading: <code>%s</code>.', 'woocommerce' ), $this->heading ),
					'placeholder' => '',
					'default'     => ''
				),
				'email_type'   => array(
					'title'       => __( 'Email type', 'woocommerce' ),
					'type'        => 'select',
					'description' => __( 'Choose which format of email to send.', 'woocommerce' ),
					'default'     => 'html',
					'class'       => 'email_type wc-enhanced-select',
					'options'     => $this->get_email_type_options()
				),
				'content_html' => array(
					'title'       => __( 'Content HTML', 'woocommerce' ),
					'type'        => 'textarea',
					'description' => __( 'Enter the text that you want to send within your email (HTML version). You can use the following placeholders: <code>{commission_id}, {commission_amount}, {commission_amount_html}, {commission_rate}, {payment_threshold}, {payment_date}</code>.', 'yith-woocommerce-affiliates' ),
					'placeholder' => '',
					'default'     => $this->content_html
				),
				'content_text' => array(
					'title'       => __( 'Content text', 'woocommerce' ),
					'type'        => 'textarea',
					'description' => __( 'Enter the text that you want to send within your email (plain text version). You can use the following placeholders: <code>{commission_id}, {commission_amount}, {commission_rate}, {payment_threshold}, {payment_date}</code>.', 'yith-woocommerce-affiliates' ),
					'placeholder' => '',
					'default'     => $this->content_text
				),
			);
		}
	}
}

return new YITH_WCAF_Customer_Pending_Commission_Email();