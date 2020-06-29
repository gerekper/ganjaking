<?php
/**
 * Email subscribe class
 *
 * @author  YITH
 * @package YITH WooCommerce Waiting List
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCWTL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWTL_Mail_Subscribe' ) ) {
	/**
	 * Email Class
	 * Extend WC_Email to send mail to waitlist users
	 *
	 * @class    YITH_WCWTL_Mail_Subscribe
	 * @extends  WC_Email
	 */
	class YITH_WCWTL_Mail_Subscribe extends YITH_WCWTL_Mail {

		/**
		 * Constructor
		 *
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function __construct() {

			$this->id          = 'yith_waitlist_mail_subscribe';
			$this->title       = __( 'YITH Waiting list Subscription Email', 'yith-woocommerce-waiting-list' );
			$this->description = __( 'When a user subscribes to a waiting list, this email is sent for confirmation.', 'yith-woocommerce-waiting-list' );

			$this->heading      = __( 'You are now in the waiting list for {product_title}', 'yith-woocommerce-waiting-list' );
			$this->subject      = __( 'Subscription confirmation for waiting list', 'yith-woocommerce-waiting-list' );
			$this->mail_content = __( 'Hi, your email address has been saved and you will be notified when {product_title} is back in stock on {blogname}. If you want to be removed from this list, please click {remove_link}', 'yith-woocommerce-waiting-list' );

			$this->template_base  = YITH_WCWTL_TEMPLATE_PATH . '/email/';
			$this->template_html  = 'yith-wcwtl-mail-subscribe.php';
			$this->template_plain = 'plain/yith-wcwtl-mail-subscribe.php';

			$this->customer_email = true;

			// Triggers for this email
			add_action( 'send_yith_waitlist_mail_subscribe_notification', array( $this, 'trigger' ), 10, 2 );

			add_filter( 'yith_wcwtl_email_custom_placeholders', array( $this, 'email_add_placeholders' ), 10, 3 );

			// Call parent constructor
			parent::__construct();
		}

		/**
		 * Send mail using standard WP Mail or Mandrill Service
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param string $subject
		 * @param string $message
		 * @param string $headers
		 * @param array  $attachments
		 *
		 * @param string $to
		 * @return void
		 */
		public function send( $to, $subject, $message, $headers, $attachments ) {
			$to = apply_filters( 'yith_wcwtl_recipient_mail_subscribe', $to );
			parent::send( $to, $subject, $message, $headers, $attachments );
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
			$this->remove_url = add_query_arg( array(
				'_yith_wcwtl_users_list'        => $product->get_id(),
				'_yith_wcwtl_users_list-action' => 'leave',
				'yith-wcwtl-email'              => urlencode( $user ),
			), $product->get_permalink() );

			$remove_link                   = ( $this->get_email_type() != 'plain' ) ? '<a href="' . esc_url( $this->remove_url ) . '">' . apply_filters( 'yith_wcwtl_label_remove_link_email', __( 'here', 'yith-woocommerce-waiting-list' ) ) . '</a>' : $url;
			$placeholders['{remove_link}'] = $remove_link;

			return $placeholders;
		}
	}
}

return new YITH_WCWTL_Mail_Subscribe();
