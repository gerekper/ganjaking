<?php
/**
 * Email registration verify class
 *
 * @author  YITH <plugins@yithemes.com>
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
		 */
		public function __construct() {

			$this->id             = 'yith_wcmap_verify_account';
			$this->title          = __( 'YITH Customize My Account Verify Account', 'yith-woocommerce-customize-myaccount-page' );
			$this->customer_email = true;
			$this->description    = '';

			$this->heading = __( '{blogname} - Verify your account', 'yith-woocommerce-customize-myaccount-page' );
			$this->subject = __( 'You need to verify your account email.', 'yith-woocommerce-customize-myaccount-page' );

			$this->template_html  = 'email/customer-verify-account.php';
			$this->template_plain = 'email/plain/customer-verify-account.php';

			// Call parent constructor.
			parent::__construct();
		}

		/**
		 * Get content html function.
		 *
		 * @access public
		 * @since  1.0.0
		 * @return string
		 */
		public function get_content_html() {

			ob_start();

			/**
			 * APPLY_FILTERS: yith_wcmap_verify_account_email_attr
			 *
			 * Filters the array with the attributes needed for the email template.
			 *
			 * @param array $atts Array of attributes.
			 *
			 * @return array
			 */
			wc_get_template(
				$this->template_html,
				apply_filters(
					'yith_wcmap_verify_account_email_attr',
					array(
						'email_heading' => $this->get_heading(),
						'blogname'      => $this->get_blogname(),
						'customer'      => $this->customer,
						'verify_url'    => esc_url( $this->verify_url ),
						'sent_to_admin' => false,
						'plain_text'    => false,
						'email'         => $this,
					)
				),
				'',
				YITH_WCMAP_TEMPLATE_PATH
			);

			return ob_get_clean();
		}

		/**
		 * Trigger Function
		 *
		 * @access public
		 * @since  2.5.0
		 * @param string|integer $customer_id The customer id.
		 * @param string|integer $to          Destination link.
		 * @return void
		 */
		public function trigger( $customer_id, $to = 0 ) {

			if ( ! $this->is_enabled() || ! $customer_id ) {
				return;
			}

			if ( ! $to ) {
				$to = wc_get_page_id( 'myaccount' );
			}

			$this->customer  = new WP_User( $customer_id );
			$this->recipient = stripslashes( $this->customer->user_email );

			/**
			 * APPLY_FILTERS: yith_wcmap_email_verify_account_url
			 *
			 * Filters the URL to verify the account.
			 *
			 * @param string $url URL to verify the account.
			 *
			 * @return string
			 */
			$this->verify_url = apply_filters(
				'yith_wcmap_email_verify_account_url',
				add_query_arg(
					array(
						'c'      => get_user_meta( $this->customer->ID, '_ywcmap_validation_code', true ),
						'action' => 'ywcmap_confirm_email_action',
						'to'     => intval( $to ),
					),
					home_url()
				)
			);

			// send!
			if ( $this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() ) ) {
				/**
				 * DO_ACTION: yith_wcmap_verify_account_sent_correctly
				 *
				 * Allows to trigger some action when the email to verify the account has been sent correctly.
				 *
				 * @param int $customer_id Customer ID.
				 */
				do_action( 'yith_wcmap_verify_account_sent_correctly', $customer_id );
			} else {
				/**
				 * DO_ACTION: yith_wcmap_verify_account_sent_error
				 *
				 * Allows to trigger some action when the email to verify the account has not been sent correctly.
				 *
				 * @param int $customer_id Customer ID.
				 */
				do_action( 'yith_wcmap_verify_account_sent_error', $customer_id );
			}
		}

		/**
		 * Get plain content function.
		 *
		 * @access public
		 * @since  1.0.0
		 * @return string
		 */
		public function get_content_plain() {
			ob_start();

			/**
			 * APPLY_FILTERS: yith_wcmap_verify_account_email_plain_attr
			 *
			 * Filters the array with the attributes needed for the plain email template.
			 *
			 * @param array $atts Array of attributes.
			 *
			 * @return array
			 */
			wc_get_template(
				$this->template_plain,
				apply_filters(
					'yith_wcmap_verify_account_email_plain_attr',
					array(
						'email_heading' => $this->get_heading(),
						'blogname'      => $this->get_blogname(),
						'customer'      => $this->customer,
						'verify_url'    => esc_url( $this->verify_url ),
						'sent_to_admin' => false,
						'plain_text'    => true,
						'email'         => $this,
					)
				),
				'',
				YITH_WCMAP_TEMPLATE_PATH
			);

			return ob_get_clean();
		}
	}
}

return new YITH_WCMAP_Verify_Account();
