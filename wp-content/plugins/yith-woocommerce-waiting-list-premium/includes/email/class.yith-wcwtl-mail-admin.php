<?php
/**
 * Email Admin class
 *
 * @author  YITH
 * @package YITH WooCommerce Waiting List
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCWTL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWTL_Mail_Admin' ) ) {
	/**
	 * Email Class
	 * Extend WC_Email to send mail to admin when an user subscribe a waiting list
	 *
	 * @class    YITH_WCWTL_Mail_Admin
	 * @extends  WC_Email
	 */
	class YITH_WCWTL_Mail_Admin extends YITH_WCWTL_Mail {

		/**
		 * Constructor
		 *
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function __construct() {

			$this->id          = 'yith_waitlist_mail_admin';
			$this->title       = __( 'YITH Waiting list - Admin Email', 'yith-woocommerce-waiting-list' );
			$this->description = __( 'This email is sent to the store admin whenever a user subscribes to a waiting list.', 'yith-woocommerce-waiting-list' );

			$this->template_base  = YITH_WCWTL_TEMPLATE_PATH . '/email/';
			$this->template_html  = 'yith-wcwtl-mail-admin.php';
			$this->template_plain = 'plain/yith-wcwtl-mail-admin.php';
			$this->customer_email = false;
			$this->recipient      = apply_filters( 'yith_wcwtl_recipient_admin_email', $this->get_option( 'recipient', get_option( 'admin_email' ) ) );

			$this->init_email_attributes();

			// Triggers for this email
			add_action( 'send_yith_waitlist_mail_admin_notification', array( $this, 'trigger' ), 10, 2 );

			// Call parent constructor
			parent::__construct();
		}

		/**
		 * Load email attributes
		 *
		 * @since  1.5.5
		 * @author Francesco Licandro
		 */
		public function init_email_attributes() {
			$this->heading      = __( 'New subscription to a product waiting list', 'yith-woocommerce-waiting-list' );
			$this->subject      = __( 'A new user has just subscribed to a product waiting list ', 'yith-woocommerce-waiting-list' );
			$this->mail_content = __( 'Hi, a new user {user_email} has just subscribed to the waiting list of product {product_title}', 'yith-woocommerce-waiting-list' );
		}

		/**
		 * @return string|void
		 */
		public function init_form_fields() {
			parent::init_form_fields();
			$default = get_option( 'admin_email' );
			// Add recipient options after enable.
			$head      = array_slice( $this->form_fields, 0, 1, true );
			$tail      = array_slice( $this->form_fields, 1, null, true );
			$recipient = array(
				'recipient' => array(
					'title'       => __( 'Recipient(s)', 'yith-woocommerce-waiting-list' ),
					'type'        => 'text',
					/* translators: %s: WP admin email */
					'description' => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'woocommerce' ), '<code>' . esc_attr( $default ) . '</code>' ),
					'placeholder' => esc_attr( $default ),
					'default'     => '',
					'desc_tip'    => true,
				),
			);

			$this->form_fields = $head + $recipient + $tail;
		}

		/**
		 * Email Trigger
		 *
		 * @since 1.0.0
		 * @param string  $user_email
		 * @param integer $product_id
		 */
		public function trigger( $user_email, $product_id ) {

			$this->init_email_attributes();
			$this->init_form_fields();
			$this->init_settings();

			$this->object = wc_get_product( $product_id );

			if ( ! $this->is_enabled() || ! $this->object || ( $user_email == $this->get_recipient() ) ) {
				return;
			}

			$placeholders = apply_filters( 'yith_wcwtl_email_custom_placeholders', array(
				'{user_email}'    => $user_email,
				'{product_title}' => $this->object->get_name(),
				'{blogname}'      => $this->get_blogname(),
			), $this->object, $user_email );

			$this->set_placeholders( $placeholders );

			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
		}
	}
}

return new YITH_WCWTL_Mail_Admin();