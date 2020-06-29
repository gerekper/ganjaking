<?php
/**
 * Email subscribe optin check class
 *
 * @author  YITH
 * @package YITH WooCommerce Waiting List
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCWTL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWTL_Mail_Subscribe_Optin' ) ) {
	/**
	 * Email Class
	 * Extend WC_Email to send mail to waitlist users
	 *
	 * @class    YITH_WCWTL_Mail_Subscribe
	 * @extends  WC_Email
	 */
	class YITH_WCWTL_Mail_Subscribe_Optin extends YITH_WCWTL_Mail {

		/**
		 * Constructor
		 *
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function __construct() {

			$this->id          = 'yith_waitlist_mail_subscribe_optin';
			$this->title       = __( 'YITH Waiting List Subscription Opt In Email', 'yith-woocommerce-waiting-list' );
			$this->description = __( 'When a user subscribes to a waiting list, this email is sent for request confirmation.', 'yith-woocommerce-waiting-list' );

			$this->heading      = __( 'Subscription to waiting list for {product_title}', 'yith-woocommerce-waiting-list' );
			$this->subject      = __( 'Subscription confirmation request for waiting list', 'yith-woocommerce-waiting-list' );
			$this->mail_content = __( 'Hi, thanks for your request to join the waiting list for {product_title}. Click this link to confirm your request to join the list: {confirm_link}
			Clicking the link above will confirm your email address and allow you to receive the information you requested. If you did not request to be on this list, please ignore this message. Thank you.', 'yith-woocommerce-waiting-list' );

			$this->template_base  = YITH_WCWTL_TEMPLATE_PATH . '/email/';
			$this->template_html  = 'yith-wcwtl-mail-subscribe-optin.php';
			$this->template_plain = 'plain/yith-wcwtl-mail-subscribe-optin.php';

			$this->customer_email = true;

			// Triggers for this email
			add_action( 'send_yith_waitlist_mail_subscribe_optin_notification', array( $this, 'trigger' ), 10, 2 );

			add_filter( 'yith_wcwtl_email_custom_placeholders', array( $this, 'email_add_placeholders' ), 10, 3 );
			// Call parent constructor
			parent::__construct();
		}

		/**
		 * Add custom email placeholder to default array
		 *
		 * @since  1.5.0
		 * @author Francesco Licandro
		 * @param array  $placeholders
		 * @param object $product
		 * @param string $user
		 * @return array
		 */
		public function email_add_placeholders( $placeholders, $product, $user ) {
			// set remove url
			$url = add_query_arg( array(
				'_yith_wcwtl_users_list'        => $product->get_id(),
				'_yith_wcwtl_users_list-action' => 'register',
				'is-double-optin'               => '1',
				'yith-wcwtl-email'              => urlencode( $user ),
			), $product->get_permalink() );

			$confirmation_link              = ( $this->get_email_type() != 'plain' ) ? '<a href="' . esc_url( $url ) . '">' . apply_filters( 'yith_wcwtl_label_confirmation_link_email', __( 'Confirm your subscription!', 'yith-woocommerce-waiting-list' ) ) . '</a>' : $url;
			$placeholders['{confirm_link}'] = $confirmation_link;

			return $placeholders;
		}
	}
}

return new YITH_WCWTL_Mail_Subscribe_Optin();
