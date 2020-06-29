<?php
/**
 * Class WC_Store_Credit_Email_Send_Credit file.
 *
 * @package WC_Store_Credit/Emails
 * @since   3.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_Store_Credit_Email_Send_Credit', false ) ) {
	/**
	 * Customer Send Store Credit Email.
	 */
	class WC_Store_Credit_Email_Send_Credit extends WC_Email {

		/**
		 * The Store Credit coupon.
		 *
		 * @var WC_Coupon
		 */
		public $coupon;

		/**
		 * The customer who will receive the coupon.
		 *
		 * @var WC_Customer
		 */
		public $customer;

		/**
		 * Additional arguments.
		 *
		 * @var array
		 */
		public $args = array();

		/**
		 * Constructor.
		 *
		 * @since 3.0.0
		 */
		public function __construct() {
			$this->id             = 'wc_store_credit_send_credit';
			$this->customer_email = true;
			$this->title          = _x( 'Send Store Credit', 'email title', 'woocommerce-store-credit' );
			$this->description    = _x( 'Email used for sending a Store Credit coupon to a customer.', 'email desc', 'woocommerce-store-credit' );
			$this->template_base  = WC_STORE_CREDIT_PATH . 'templates/';
			$this->template_html  = 'emails/customer-store-credit.php';
			$this->template_plain = 'emails/plain/customer-store-credit.php';
			$this->placeholders   = array(
				'{site_title}'    => $this->get_blogname(),
				'{site_address}'  => wp_parse_url( home_url(), PHP_URL_HOST ),
				'{coupon_code}'   => '',
				'{coupon_amount}' => '',
			);

			// Triggers.
			add_action( 'wc_store_credit_send_credit_to_customer_notification', array( $this, 'trigger' ), 10, 3 );

			// Call parent constructor.
			parent::__construct();
		}

		/**
		 * Gets the default email subject.
		 *
		 * @since 3.0.0
		 *
		 * @return string
		 */
		public function get_default_subject() {
			return _x( 'You have received a coupon', 'email subject', 'woocommerce-store-credit' );
		}

		/**
		 * Gets the default email heading.
		 *
		 * @since 3.0.0
		 *
		 * @return string
		 */
		public function get_default_heading() {
			return _x( 'You have been given {coupon_amount} credit', 'email heading', 'woocommerce-store-credit' );
		}

		/**
		 * Default content to show below main email content.
		 *
		 * Fallback for the method `get_default_additional_content` method introduced in WC 3.7.
		 *
		 * @since 3.0.0
		 *
		 * @return string
		 */
		public function get_default_additional_content() {
			return '';
		}

		/**
		 * Return content from the additional_content field.
		 *
		 * Fallback for the method `get_additional_content` method introduced in WC 3.7.
		 *
		 * @since 3.0.0
		 *
		 * @return string
		 */
		public function get_additional_content() {
			$content = $this->get_option( 'additional_content', '' );

			/** This filter is documented in woocommerce/includes/emails/class-wc-email.php */
			return apply_filters( 'woocommerce_email_additional_content_' . $this->id, $this->format_string( $content ), $this->object );
		}

		/**
		 * Triggers the sending of this email.
		 *
		 * @since 3.0.0
		 *
		 * @param WC_Coupon $coupon       The coupon object.
		 * @param mixed     $the_customer Customer object, email or ID.
		 * @param array     $args         Optional. Additional arguments.
		 */
		public function trigger( $coupon, $the_customer, $args = array() ) {
			$this->setup_locale();

			$customer       = ( is_email( $the_customer ) ? null : wc_store_credit_get_customer( $the_customer ) );
			$customer_email = ( $customer ? $customer->get_email() : $the_customer );

			$this->recipient = $customer_email;
			$this->coupon    = $coupon;
			$this->customer  = $customer;
			$this->args      = $args;

			$coupon_amount = wc_price( $this->coupon->get_amount() );

			$this->placeholders['{coupon_code}']   = $this->coupon->get_code();
			$this->placeholders['{coupon_amount}'] = ( 'plain' === $this->get_email_type() ? wp_strip_all_tags( $coupon_amount ) : $coupon_amount );

			if ( $this->is_enabled() && $this->get_recipient() ) {
				$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
			}

			$this->restore_locale();
		}

		/**
		 * Gets the content arguments.
		 *
		 * @param string $type Optional. The content type [html, plain].
		 *
		 * @since 3.0.0
		 *
		 * @return array
		 */
		public function get_content_args( $type = 'html' ) {
			return array(
				'email_heading'      => $this->get_heading(),
				'additional_content' => $this->get_additional_content(),
				'sent_to_admin'      => false,
				'plain_text'         => ( 'plain' === $type ),
				'email'              => $this,
				'coupon'             => $this->coupon,
				'customer'           => $this->customer,
				'args'               => $this->args,
			);
		}

		/**
		 * Gets content HTML.
		 *
		 * @since 3.0.0
		 *
		 * @return string
		 */
		public function get_content_html() {
			return wc_store_credit_get_template_html(
				$this->template_html,
				$this->get_content_args()
			);
		}

		/**
		 * Get content plain.
		 *
		 * @since 3.0.0
		 *
		 * @return string
		 */
		public function get_content_plain() {
			return wc_store_credit_get_template_html(
				$this->template_plain,
				$this->get_content_args( 'plain' )
			);
		}

		/**
		 * Initialise Settings Form Fields.
		 *
		 * @since 3.0.0
		 */
		public function init_form_fields() {
			parent::init_form_fields();

			// Fallback for the 'additional_content' field introduced in WC 3.7.
			if ( ! isset( $this->form_fields['additional_content'] ) ) {
				$offset           = count( $this->form_fields ) - 1;
				$placeholder_text = sprintf(
					/* translators: %s: list of placeholders */
					__( 'Available placeholders: %s', 'woocommerce-store-credit' ),
					'<code>' . esc_html( implode( '</code>, <code>', array_keys( $this->placeholders ) ) ) . '</code>'
				);

				$this->form_fields = array_merge(
					array_slice( $this->form_fields, 0, $offset ),
					array(
						'additional_content' => array(
							'title'       => _x( 'Additional content', 'email field label', 'woocommerce-store-credit' ),
							'description' => _x( 'Text to appear below the main email content.', 'email field desc', 'woocommerce-store-credit' ) . ' ' . $placeholder_text,
							'css'         => 'width:400px; height: 75px;',
							'placeholder' => __( 'N/A', 'woocommerce-store-credit' ),
							'type'        => 'textarea',
							'default'     => $this->get_default_additional_content(),
							'desc_tip'    => true,
						),
					),
					array_slice( $this->form_fields, $offset )
				);
			}
		}
	}
}

return new WC_Store_Credit_Email_Send_Credit();
