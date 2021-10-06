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

if ( ! class_exists( 'WC_SC_Email' ) ) {
	/**
	 * The Smart Coupons Email class
	 *
	 * @extends \WC_Email
	 */
	class WC_SC_Email extends WC_Email {

		/**
		 * Email args defaults
		 *
		 * @var array
		 */
		public $email_args = array(
			'email'                         => '',
			'coupon'                        => array(),
			'discount_type'                 => 'smart_coupon',
			'smart_coupon_type'             => '',
			'receiver_name'                 => '',
			'message_from_sender'           => '',
			'gift_certificate_sender_name'  => '',
			'gift_certificate_sender_email' => '',
			'from'                          => '',
			'sender'                        => '',
			'is_gift'                       => false,
		);

		/**
		 * Get shop page url
		 *
		 * @return string $url Shop page url
		 */
		public function get_url() {

			global $woocommerce_smart_coupon;

			if ( $woocommerce_smart_coupon->is_wc_gte_30() ) {
				$page_id = wc_get_page_id( 'shop' );
			} else {
				$page_id = woocommerce_get_page_id( 'shop' );
			}

			$url = ( get_option( 'permalink_structure' ) ) ? get_permalink( $page_id ) : get_post_type_archive_link( 'product' );

			return $url;
		}

		/**
		 * Function to get sender name.
		 *
		 * @return string $sender_name Sender name.
		 */
		public function get_sender_name() {

			if ( isset( $this->email_args['gift_certificate_sender_name'] ) && ! empty( $this->email_args['gift_certificate_sender_name'] ) ) {
				$sender_name = $this->email_args['gift_certificate_sender_name'];
			} else {
				$sender_name = is_callable( array( $this, 'get_blogname' ) ) ? $this->get_blogname() : '';
			}

			return $sender_name;
		}

		/**
		 * Function to get sender email.
		 *
		 * @return string $sender_email Sender email.
		 */
		public function get_sender_email() {

			$sender_email = isset( $this->email_args['gift_certificate_sender_email'] ) ? $this->email_args['gift_certificate_sender_email'] : '';

			return $sender_email;
		}

		/**
		 * Initialize Settings Form Fields
		 */
		public function init_form_fields() {

			/* translators: %s: list of placeholders */
			$placeholder_text = sprintf( __( 'Available placeholders: %s', 'woocommerce-smart-coupons' ), '<code>' . implode( '</code>, <code>', array_keys( $this->placeholders ) ) . '</code>' );

			$this->form_fields = array(
				'enabled'    => array(
					'title'   => __( 'Enable/Disable', 'woocommerce-smart-coupons' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable this email notification', 'woocommerce-smart-coupons' ),
					'default' => 'yes',
				),
				'email_type' => array(
					'title'       => __( 'Email type', 'woocommerce-smart-coupons' ),
					'type'        => 'select',
					'description' => __( 'Choose which format of email to send.', 'woocommerce-smart-coupons' ),
					'default'     => 'html',
					'class'       => 'email_type wc-enhanced-select',
					'options'     => $this->get_email_type_options(),
					'desc_tip'    => true,
				),
				'subject'    => array(
					'title'       => __( 'Subject', 'woocommerce-smart-coupons' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => $placeholder_text,
					'placeholder' => $this->get_default_subject(),
					'default'     => '',
				),
				'heading'    => array(
					'title'       => __( 'Email heading', 'woocommerce-smart-coupons' ),
					'type'        => 'text',
					'desc_tip'    => true,
					'description' => $placeholder_text,
					'placeholder' => $this->get_default_heading(),
					'default'     => '',
				),
			);
		}

		/**
		 * Function to update SC admin email settings when WC email settings get updated
		 */
		public function process_admin_options() {
			// Save regular options.
			parent::process_admin_options();

			$is_email_enabled = $this->get_field_value( 'enabled', $this->form_fields['enabled'] );

			if ( ! empty( $is_email_enabled ) ) {
				update_option( 'smart_coupons_is_send_email', $is_email_enabled, 'no' );
			}
		}

	}
}
