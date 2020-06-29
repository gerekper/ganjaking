<?php
/**
 * Email instock class
 *
 * @author  YITH
 * @package YITH WooCommerce Waiting List
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WCWTL' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCWTL_Mail_Instock' ) ) {
	/**
	 * Email Class
	 * Extend WC_Email to send mail to waitlist users
	 *
	 * @class    YITH_WCWTL_Mail_Instock
	 * @extends  WC_Email
	 */
	class YITH_WCWTL_Mail_Instock extends YITH_WCWTL_Mail {

		/**
		 * Constructor
		 *
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function __construct() {

			$this->id          = 'yith_waitlist_mail_instock';
			$this->title       = __( 'YITH Waiting list In Stock Email', 'yith-woocommerce-waiting-list' );
			$this->description = __( 'When a product is back in stock, this email is sent to all the users registered in the waiting list of that product.', 'yith-woocommerce-waiting-list' );

			$this->template_base  = YITH_WCWTL_TEMPLATE_PATH . '/email/';
			$this->template_html  = 'yith-wcwtl-mail-instock.php';
			$this->template_plain = 'plain/yith-wcwtl-mail-instock.php';

			$this->customer_email = true;

			$this->init_email_attributes();

			// Triggers for this email
			add_action( 'send_yith_waitlist_mail_instock_notification', array( $this, 'trigger' ), 10, 2 );

			add_filter( 'yith_wcwtl_email_custom_placeholders', array( $this, 'email_add_placeholders' ), 10, 3 );

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
			$this->heading      = __( '{product_title} is now back in stock on {blogname}', 'yith-woocommerce-waiting-list' );
			$this->subject      = __( 'A product you are waiting for is back in stock', 'yith-woocommerce-waiting-list' );
			$this->mail_content = __( 'Hi, {product_title} is now back in stock on {blogname}. You have been sent this email because your email address was registered in a waiting list for this product. If you would like to purchase {product_title}, please visit the following link: {product_link}', 'yith-woocommerce-waiting-list' );
		}

		/**
		 * Email Trigger
		 *
		 * @since 1.0.0
		 */
		public function trigger( $users, $product_id ) {
			$this->init_email_attributes();
			$this->init_form_fields();
			$this->init_settings();

			parent::trigger( $users, $product_id );
		}

		/**
		 * Add custom email placeholder to default array
		 *
		 * @since  1.5.0
		 * @author Francesco Licandro
		 * @param array        $placeholders
		 * @param object       $product
		 * @param array|string $users
		 * @return array
		 */
		public function email_add_placeholders( $placeholders, $product, $users ) {
			$link_label = apply_filters( 'yith_waitlist_link_label_instock_email', __( 'link', 'yith-woocommerce-waiting-list' ), $product );
			$link       = ( $this->get_email_type() == 'html' ) ? '<a href="' . $product->get_permalink() . '">' . $link_label . '</a>' : $product->get_permalink();
			// let third part filter link
			$link = apply_filters( 'yith_waitlist_link_html_instock_email', $link, $product, $this->get_email_type() );

			$placeholders['{product_link}'] = $link;

			return $placeholders;
		}
	}
}

return new YITH_WCWTL_Mail_Instock();