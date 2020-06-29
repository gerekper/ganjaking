<?php
/**
 * Email registration verify class
 *
 * @author  YITH
 * @package YITH WooCommerce Customize My Account Premium
 * @version 2.5.0
 */

if ( ! defined( 'YITH_WCMAP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCMAP_Verify_Account' ) ) {
	/**
	 * Email Class
	 * Extend WC_Email to send mail to customer
	 *
	 * @class       YITH_WCMAP_Verify_Account
	 * @since       2.5.0
	 * @extends     WC_Email
	 */
	class YITH_WCMAP_Verify_Account extends WC_Email {

		/**
		 * Customer object
		 *
		 * @since 2.5.0
		 * @var null|\WP_User
		 */
		public $customer = null;

		/**
		 * The verify account url
		 *
		 * @since 2.5.0
		 * @var string
		 */
		public $verify_url = '#';

		/**
		 * Constructor
		 *
		 * @since  2.5.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function __construct() {

			$this->id             = 'yith_wcmap_verify_account';
			$this->title          = __( 'YITH Customize My Account Verify Account', 'yith-woocommerce-customize-myaccount-page' );
			$this->customer_email = true;
			$this->description    = '';

			$this->heading = __( '{blogname}', 'yith-woocommerce-customize-myaccount-page' );
			$this->subject = __( 'You need to verify your account email.', 'yith-woocommerce-customize-myaccount-page' );

			$this->template_base  = YITH_WCMAP_TEMPLATE_PATH . '/email/';
			$this->template_html  = 'customer-verify-account.php';
			$this->template_plain = 'plain/customer-verify-account.php';

			// Call parent constructor
			parent::__construct();
		}

		/**
		 * Trigger Function
		 *
		 * @access public
		 * @since  2.5.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @param string|integer $to          Destination link
		 * @param string|integer $customer_id The customer id
		 * @return void
		 */
		public function trigger( $customer_id, $to = 0 ) {

			if ( ! $this->is_enabled() || ! $customer_id ) {
				return;
			}

			! $to && $to = wc_get_page_id( 'myaccount' );

			$this->customer   = new WP_User( $customer_id );
			$this->recipient  = stripslashes( $this->customer->user_email );
			$this->verify_url = apply_filters( 'yith_wcmap_email_verify_account_url', add_query_arg( array(
				'c'      => get_user_meta( $this->customer->ID, '_ywcmap_validation_code', true ),
				'action' => 'ywcmap_confirm_email_action',
				'to'     => intval( $to ),
			), home_url() ) );

			// send!
			if ( $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() ) ) {
				do_action( 'yith_wcmap_verify_account_sent_correctly', $customer_id );
			} else {
				do_action( 'yith_wcmap_verify_account_sent_error', $customer_id );
			}
		}

		/**
		 * get_content_html function.
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return string
		 */
		public function get_content_html() {

			ob_start();

			wc_get_template( $this->template_html, apply_filters( 'yith_wcmap_verify_account_email_attr', array(
				'email_heading' => $this->get_heading(),
				'blogname'      => $this->get_blogname(),
				'customer'      => $this->customer,
				'verify_url'    => esc_url( $this->verify_url ),
				'sent_to_admin' => false,
				'plain_text'    => false,
				'email'         => $this,
			) ), false, $this->template_base );

			return ob_get_clean();
		}

		/**
		 * get_content_plain function.
		 *
		 * @access public
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 * @return string
		 */
		public function get_content_plain() {
			ob_start();

			wc_get_template( $this->template_plain, apply_filters( 'yith_wcmap_verify_account_email_plain_attr', array(
				'email_heading' => $this->get_heading(),
				'blogname'      => $this->get_blogname(),
				'customer'      => $this->customer,
				'verify_url'    => esc_url( $this->verify_url ),
				'sent_to_admin' => false,
				'plain_text'    => true,
				'email'         => $this,
			) ), false, $this->template_base );

			return ob_get_clean();
		}
	}
}

return new YITH_WCMAP_Verify_Account();
