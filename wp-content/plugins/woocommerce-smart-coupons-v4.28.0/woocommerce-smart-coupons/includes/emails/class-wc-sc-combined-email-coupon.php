<?php
/**
 * Main class for Smart Coupons Email
 *
 * @author      StoreApps
 * @since       4.4.1
 * @version     1.2.1
 *
 * @package     woocommerce-smart-coupons/includes/emails/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'WC_SC_Combined_Email_Coupon' ) ) {
	/**
	 * The Smart Coupons Combined Email class
	 *
	 * @extends \WC_SC_Email
	 */
	class WC_SC_Combined_Email_Coupon extends WC_SC_Email {

		/**
		 * Set email defaults
		 */
		public function __construct() {

			$this->id = 'wc_sc_combined_email_coupon';

			$this->customer_email = true;

			// Set email title and description.
			$this->title       = __( 'Smart Coupons - Combined auto generated coupons email', 'woocommerce-smart-coupons' );
			$this->description = __( 'Send only one email instead of multiple emails when multiple coupons are generated per recipient.', 'woocommerce-smart-coupons' );

			// Use our plugin templates directory as the template base.
			$this->template_base = dirname( WC_SC_PLUGIN_FILE ) . '/templates/';

			// Email template location.
			$this->template_html  = 'combined-email.php';
			$this->template_plain = 'plain/combined-email.php';

			$this->placeholders = array(
				'{sender_name}' => '',
			);

			// Trigger for this email.
			add_action( 'wc_sc_combined_email_coupon_notification', array( $this, 'trigger' ) );

			// Call parent constructor to load any other defaults not explicity defined here.
			parent::__construct();
		}

		/**
		 * Get default email subject.
		 *
		 * @return string Default email subject
		 */
		public function get_default_subject() {
			return __( '{site_title}: Congratulations! You\'ve received coupons from {sender_name}', 'woocommerce-smart-coupons' );
		}

		/**
		 * Get default email heading.
		 *
		 * @return string Default email heading
		 */
		public function get_default_heading() {
			return __( 'You have received coupons.', 'woocommerce-smart-coupons' );
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
			$this->placeholders['{sender_name}'] = $this->get_sender_name();
		}

		/**
		 * Function to load email html content
		 *
		 * @return string Email content html
		 */
		public function get_content_html() {

			global $woocommerce_smart_coupon;

			$order         = $this->object;
			$url           = $this->get_url();
			$email_heading = $this->get_heading();

			$sender = '';
			$from   = '';

			$is_gift = isset( $this->email_args['is_gift'] ) ? $this->email_args['is_gift'] : '';

			if ( 'yes' === $is_gift ) {
				$sender_name  = $this->get_sender_name();
				$sender_email = $this->get_sender_email();
				if ( ! empty( $sender_name ) && ! empty( $sender_email ) ) {
					$sender = $sender_name . ' (' . $sender_email . ') ';
					$from   = ' ' . __( 'from', 'woocommerce-smart-coupons' ) . ' ';
				}
			}

			$email            = isset( $this->email_args['email'] ) ? $this->email_args['email'] : '';
			$receiver_details = isset( $this->email_args['receiver_details'] ) ? $this->email_args['receiver_details'] : '';

			$design           = get_option( 'wc_sc_setting_coupon_design', 'basic' );
			$background_color = get_option( 'wc_sc_setting_coupon_background_color', '#39cccc' );
			$foreground_color = get_option( 'wc_sc_setting_coupon_foreground_color', '#30050b' );
			$third_color      = get_option( 'wc_sc_setting_coupon_third_color', '#39cccc' );

			$show_coupon_description = get_option( 'smart_coupons_show_coupon_description', 'no' );

			$valid_designs = $woocommerce_smart_coupon->get_valid_coupon_designs();

			if ( ! in_array( $design, $valid_designs, true ) ) {
				$design = 'basic';
			}

			$design = ( 'custom-design' !== $design ) ? 'email-coupon' : $design;

			$coupon_styles = $woocommerce_smart_coupon->get_coupon_styles( $design, array( 'is_email' => 'yes' ) );

			$default_path  = $this->template_base;
			$template_path = $woocommerce_smart_coupon->get_template_base_dir( $this->template_html );

			ob_start();

			wc_get_template(
				$this->template_html,
				array(
					'email'                   => $email,
					'email_heading'           => $email_heading,
					'order'                   => $order,
					'url'                     => $url,
					'from'                    => $from,
					'background_color'        => $background_color,
					'foreground_color'        => $foreground_color,
					'third_color'             => $third_color,
					'coupon_styles'           => $coupon_styles,
					'sender'                  => $sender,
					'receiver_details'        => $receiver_details,
					'show_coupon_description' => $show_coupon_description,
					'design'                  => $design,
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
			$url           = $this->get_url();
			$email_heading = $this->get_heading();

			$sender = '';
			$from   = '';

			$is_gift = isset( $this->email_args['is_gift'] ) ? $this->email_args['is_gift'] : '';

			if ( 'yes' === $is_gift ) {
				$sender_name  = $this->get_sender_name();
				$sender_email = $this->get_sender_email();
				if ( ! empty( $sender_name ) && ! empty( $sender_email ) ) {
					$sender = $sender_name . ' (' . $sender_email . ') ';
					$from   = ' ' . __( 'from', 'woocommerce-smart-coupons' ) . ' ';
				}
			}

			$email            = isset( $this->email_args['email'] ) ? $this->email_args['email'] : '';
			$receiver_details = isset( $this->email_args['receiver_details'] ) ? $this->email_args['receiver_details'] : '';

			$default_path  = $this->template_base;
			$template_path = $woocommerce_smart_coupon->get_template_base_dir( $this->template_plain );

			ob_start();

			wc_get_template(
				$this->template_plain,
				array(
					'email'            => $email,
					'email_heading'    => $email_heading,
					'order'            => $order,
					'url'              => $url,
					'from'             => $from,
					'sender'           => $sender,
					'receiver_details' => $receiver_details,
				),
				$template_path,
				$default_path
			);

			return ob_get_clean();
		}

		/**
		 * Function to update SC admin email settings when WC email settings get updated
		 */
		public function process_admin_options() {
			// Save regular options.
			parent::process_admin_options();

			$is_email_enabled = $this->get_field_value( 'enabled', $this->form_fields['enabled'] );

			if ( ! empty( $is_email_enabled ) ) {
				update_option( 'smart_coupons_combine_emails', $is_email_enabled, 'no' );
			}
		}

	}
}
