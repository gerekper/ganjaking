<?php
/**
 * Main class for Smart Coupons Acknowledgement Email
 *
 * @author      StoreApps
 * @since       4.7.8
 * @version     1.0.0
 *
 * @package     woocommerce-smart-coupons/includes/emails/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Acknowledgement_Email' ) ) {
	/**
	 * The Smart Copuons Email class
	 *
	 * @extends \WC_SC_Email
	 */
	class WC_SC_Acknowledgement_Email extends WC_SC_Email {

		/**
		 * Set email defaults
		 */
		public function __construct() {

			$this->id = 'wc_sc_acknowledgement_email';

			$this->customer_email = true;

			// Set email title and description.
			$this->title       = __( 'Smart Coupons - Acknowledgement email', 'woocommerce-smart-coupons' );
			$this->description = __( 'Send an acknowledgement email to the purchaser. One email per customer.', 'woocommerce-smart-coupons' );

			// Use our plugin templates directory as the template base.
			$this->template_base = dirname( WC_SC_PLUGIN_FILE ) . '/templates/';

			// Email template location.
			$this->template_html  = 'acknowledgement-email.php';
			$this->template_plain = 'plain/acknowledgement-email.php';

			$this->placeholders = array(
				'{coupon_type}' => '',
			);

			// Trigger for this email.
			add_action( 'wc_sc_acknowledgement_email_notification', array( $this, 'trigger' ) );

			// Call parent constructor to load any other defaults not explicity defined here.
			parent::__construct();
		}

		/**
		 * Get default email subject.
		 *
		 * @return string Default email subject
		 */
		public function get_default_subject() {
			return __( '{site_title}: {coupon_type} sent successfully', 'woocommerce-smart-coupons' );
		}

		/**
		 * Get default email heading.
		 *
		 * @return string Default email heading
		 */
		public function get_default_heading() {
			return __( '{coupon_type} sent successfully', 'woocommerce-smart-coupons' );
		}

		/**
		 * Determine if the email should actually be sent and setup email merge variables
		 *
		 * @param array $args Email arguements.
		 */
		public function trigger( $args = array() ) {

			$this->email_args = wp_parse_args( $args, $this->email_args );

			if ( ! isset( $this->email_args['email'] ) || empty( $this->email_args['email'] ) ) {
				return;
			}

			$this->setup_locale();

			$this->recipient = $this->email_args['email'];

			$order_id = isset( $this->email_args['order_id'] ) ? $this->email_args['order_id'] : 0;

			// Get order object.
			if ( ! empty( $order_id ) && 0 !== $order_id ) {
				$order = wc_get_order( $order_id );
				if ( is_a( $order, 'WC_Order' ) ) {
					$this->object = $order;
				}
			}

			$this->set_placeholders();

			$email_content = $this->get_content();
			// Replace placeholders with values in the email content.
			$email_content = ( is_callable( array( $this, 'format_string' ) ) ) ? $this->format_string( $email_content ) : $email_content;

			// Send email.
			if ( $this->is_enabled() && $this->get_recipient() ) {
				$this->send( $this->get_recipient(), $this->get_subject(), $email_content, $this->get_headers(), $this->get_attachments() );
			}

			$this->restore_locale();
		}

		/**
		 * Function to set placeholder variables used in email subject/heading
		 */
		public function set_placeholders() {
			$this->placeholders['{coupon_type}'] = $this->get_coupon_type();
		}

		/**
		 * Function to load email html content
		 *
		 * @return string Email content html
		 */
		public function get_content_html() {

			global $woocommerce_smart_coupon;

			$order         = $this->object;
			$email_heading = $this->get_heading();

			$email            = isset( $this->email_args['email'] ) ? $this->email_args['email'] : '';
			$receivers_detail = isset( $this->email_args['receivers_detail'] ) ? $this->email_args['receivers_detail'] : array();
			$receiver_name    = isset( $this->email_args['receiver_name'] ) ? $this->email_args['receiver_name'] : '';
			$receiver_count   = isset( $this->email_args['receiver_count'] ) ? $this->email_args['receiver_count'] : 0;

			$default_path  = $this->template_base;
			$template_path = $woocommerce_smart_coupon->get_template_base_dir( $this->template_html );

			ob_start();

			wc_get_template(
				$this->template_html,
				array(
					'email'                          => $email,
					'email_heading'                  => $email_heading,
					'order'                          => $order,
					'receivers_detail'               => $receivers_detail,
					'gift_certificate_receiver_name' => $receiver_name,
					'receiver_count'                 => $receiver_count,
				),
				$template_path,
				$default_path
			);

			return ob_get_clean();
		}

		/**
		 * Function to load email plain content
		 *
		 * @return string Email plain content
		 */
		public function get_content_plain() {

			global $woocommerce_smart_coupon;

			$order         = $this->object;
			$email_heading = $this->get_heading();

			$email            = isset( $this->email_args['email'] ) ? $this->email_args['email'] : '';
			$receivers_detail = isset( $this->email_args['receivers_detail'] ) ? $this->email_args['receivers_detail'] : array();
			$receiver_name    = isset( $this->email_args['receiver_name'] ) ? $this->email_args['receiver_name'] : '';
			$receiver_count   = isset( $this->email_args['receiver_count'] ) ? $this->email_args['receiver_count'] : 0;

			$default_path  = $this->template_base;
			$template_path = $woocommerce_smart_coupon->get_template_base_dir( $this->template_html );

			ob_start();

			wc_get_template(
				$this->template_plain,
				array(
					'email'                          => $email,
					'email_heading'                  => $email_heading,
					'order'                          => $order,
					'receivers_detail'               => $receivers_detail,
					'gift_certificate_receiver_name' => $receiver_name,
					'receiver_count'                 => $receiver_count,
				),
				$template_path,
				$default_path
			);

			return ob_get_clean();
		}

		/**
		 * Function to get coupon type for current coupon being sent.
		 *
		 * @return string $coupon_type Coupon type.
		 */
		public function get_coupon_type() {

			global $store_credit_label;

			$coupon_type = ( ! empty( $store_credit_label['singular'] ) ) ? ucwords( $store_credit_label['singular'] ) : __( 'Gift card', 'woocommerce-smart-coupons' );

			return $coupon_type;
		}

	}

}
