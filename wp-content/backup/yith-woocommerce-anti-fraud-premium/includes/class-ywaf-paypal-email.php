<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YWAF_PayPal_Verify' ) ) {

	/**
	 * Implements Coupon Mail for YWCES plugin
	 *
	 * @class   YWAF_PayPal_Verify
	 * @package Yithemes
	 * @since   1.0.0
	 * @author  Your Inspiration Themes
	 * @extends WC_Email
	 *
	 */
	class YWAF_PayPal_Verify extends WC_Email {

		/**
		 * @var string key for PayPal verification
		 */
		var $verify_key;

		/**
		 * Constructor
		 *
		 * Initialize email type and set templates paths
		 *
		 * @since   1.0.0
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$this->id             = 'yith-anti-fraud';
			$this->customer_email = true;
			$this->description    = __( 'YITH WooCommerce Anti-Fraud is the best way to understand and recognize all suspicious purchases made in your e-commerce site.', 'yith-woocommerce-anti-fraud' );
			$this->title          = __( 'Anti-Fraud PayPal Verification', 'yith-woocommerce-anti-fraud' );
			$this->template_base  = YWAF_TEMPLATE_PATH . '/';
			$this->template_html  = 'emails/paypal-email.php';
			$this->template_plain = 'emails/plain/paypal-email.php';

			parent::__construct();
			$this->plugin_id = '';

		}

		/**
		 * Trigger email send
		 *
		 * @since   1.0.0
		 *
		 * @param   $order        WC_Order
		 * @param   $mail_address string
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function trigger( $order, $mail_address ) {

			$subject          = str_replace( '{site_title}', get_option( 'blogname' ), get_option( 'ywaf_paypal_mail_subject' ) );
			$this->object     = $order;
			$this->verify_key = base64_encode( base64_encode( '#' . $order->get_id() ) . ',' . base64_encode( $mail_address ) . ',' . base64_encode( $order->get_date_created() ) );
			$this->heading    = $subject;
			$this->subject    = $subject;
			$this->recipient  = $mail_address;

			$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), array() );

		}

		/**
		 * Get email type.
		 *
		 * @since   1.0.5
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function get_email_type() {
			return $this->get_option( 'ywaf_paypal_mail_type' );
		}


		/**
		 * Get HTML content
		 *
		 * @since   1.0.0
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function get_content_html() {

			ob_start();

			wc_get_template( $this->template_html, array(
				'email_heading' => $this->get_heading(),
				'order'         => $this->object,
				'mail_body'     => $this->get_option( 'ywaf_paypal_mail_body' ),
				'verify_key'    => $this->verify_key,
				'sent_to_admin' => false,
				'plain_text'    => false,
				'email'         => $this,

			), false, $this->template_base );

			return ob_get_clean();

		}

		/**
		 * Get Plain content
		 *
		 * @since   1.0.0
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function get_content_plain() {

			ob_start();

			wc_get_template( $this->template_plain, array(
				'email_heading' => $this->get_heading(),
				'order'         => $this->object,
				'mail_body'     => $this->get_option( 'ywaf_paypal_mail_body' ),
				'verify_key'    => $this->verify_key,
				'sent_to_admin' => false,
				'plain_text'    => true,
				'email'         => $this,
			), false, $this->template_base );

			return ob_get_clean();

		}

		/**
		 * Checks if this email is enabled and will be sent.
		 * @since   1.0.4
		 * @return  bool
		 * @author  Alberto Ruggiero
		 */
		public function is_enabled() {
			return ( get_option( 'ywaf_paypal_enable' ) === 'yes' );
		}

		/**
		 * Get email subject.
		 *
		 * @since   1.2.4
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function get_subject() {
			return apply_filters( 'woocommerce_email_subject_' . $this->id, $this->format_string( $this->get_option( 'ywaf_paypal_mail_subject', $this->get_default_subject() ) ), $this->object );
		}


		/**
		 * Get email heading.
		 *
		 * @since   1.2.4
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function get_heading() {
			return apply_filters( 'woocommerce_email_heading_' . $this->id, $this->format_string( $this->get_option( 'ywaf_paypal_mail_subject', $this->get_default_subject() ) ), $this->object );
		}

		/**
		 * Admin Panel Options Processing - Saves the options to the DB
		 *
		 * @since   1.0.4
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function process_admin_options() {
			woocommerce_update_options( $this->form_fields );
		}

		/**
		 * Override option key.
		 *
		 * @since   1.0.4
		 *
		 * @param   $key string
		 *
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function get_field_key( $key ) {
			return $key;
		}

		/**
		 * Get plugin option.
		 *
		 * @since   1.0.4
		 *
		 * @param $key         string
		 * @param $empty_value mixed
		 *
		 * @return  mixed
		 * @author  Alberto Ruggiero
		 */
		public function get_option( $key, $empty_value = null ) {

			$setting = get_option( $key );

			// Get option default if unset.
			if ( ! $setting ) {
				$form_fields = $this->get_form_fields();
				$setting     = isset( $form_fields[ $key ] ) ? $this->get_field_default( $form_fields[ $key ] ) : '';
			}

			return $setting;

		}

		/**
		 * Initialise Settings Form Fields
		 *
		 * @since   1.0.4
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function init_form_fields() {

			$this->form_fields = array(
				'ywaf_paypal_mail_subject' => array(
					'title'       => __( 'Email subject', 'yith-woocommerce-anti-fraud' ),
					'type'        => 'text',
					'description' => sprintf( __( 'Available placeholders: %s', 'yith-woocommerce-anti-fraud' ), '<code>{site_title}</code>' ),
					'id'          => 'ywaf_paypal_mail_subject',
					'default'     => __( '[{site_title}] Confirm your PayPal email address', 'yith-woocommerce-anti-fraud' ),
					'css'         => 'width: 400px;',
					'desc_tip'    => true,
				),
				'ywaf_paypal_mail_body'    => array(
					'title'       => __( 'Email body', 'yith-woocommerce-anti-fraud' ),
					'type'        => 'textarea',
					'description' => sprintf( __( 'Available placeholders: %s', 'yith-woocommerce-anti-fraud' ), '<code>{site_title}, {site_email}</code>' ),
					'id'          => 'ywaf_paypal_mail_body',
					'default'     => __( 'Hi!
We have received your order on {site_title}, but to complete we have to verify your PayPal email address.

If you haven\'t made or authorized any purchase, please, contact PayPal support service immediately,
and email us to {site_email} for having your money back.', 'yith-woocommerce-anti-fraud' ),
					'css'         => 'resize: vertical; width: 100%; min-height: 40px; height:200px',
					'desc_tip'    => true,
				),
				'ywaf_paypal_mail_type'    => array(
					'title'       => __( 'Email type', 'yith-woocommerce-anti-fraud' ),
					'type'        => 'select',
					'default'     => 'html',
					'id'          => 'ywaf_paypal_mail_type',
					'description' => __( 'Choose which format of email to send.', 'yith-woocommerce-anti-fraud' ),
					'class'       => 'email_type wc-enhanced-select',
					'options'     => $this->get_email_type_options(),
					'desc_tip'    => true,
				),
			);

		}

	}

}

return new YWAF_PayPal_Verify();