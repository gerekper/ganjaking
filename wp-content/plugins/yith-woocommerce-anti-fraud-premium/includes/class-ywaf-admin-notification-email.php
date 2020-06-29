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

if ( ! class_exists( 'YWAF_Admin_Notification' ) ) {

	/**
	 * Implements Admin Notification email for YWAF plugin
	 *
	 * @class   YWAF_Admin_Notification
	 * @package Yithemes
	 * @since   1.0.5
	 * @author  Your Inspiration Themes
	 * @extends WC_Email
	 *
	 */
	class YWAF_Admin_Notification extends WC_Email {

		/**
		 * Constructor
		 *
		 * Initialize email type and set templates paths
		 *
		 * @since   1.0.5
		 * @author  Alberto Ruggiero
		 */
		public function __construct() {

			$this->id             = 'yith-anti-fraud-admin';
			$this->title          = __( 'Anti-Fraud Admin Notification', 'yith-woocommerce-anti-fraud' );
			$this->description    = __( 'YITH WooCommerce Anti-Fraud is the best way to understand and recognize all suspicious purchases made in your e-commerce site.', 'yith-woocommerce-anti-fraud' );
			$this->template_base  = YWAF_TEMPLATE_PATH . '/';
			$this->template_html  = 'emails/admin-email.php';
			$this->template_plain = 'emails/plain/admin-email.php';

			parent::__construct();

			$this->plugin_id = '';
			$this->recipient = $this->get_option( 'ywaf_admin_mail_recipient' );

		}

		/**
		 * Trigger email send
		 *
		 * @since   1.0.5
		 *
		 * @param   $order WC_Order
		 *
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function trigger( $order ) {

			$this->object                         = $order;
			$this->placeholders['{order_number}'] = $order->get_order_number();

			if ( ! $this->get_recipient() ) {
				return;
			}

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
			return $this->get_option( 'ywaf_admin_mail_type' );
		}

		/**
		 * Get email subject.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_subject() {
			return apply_filters( 'woocommerce_email_subject_' . $this->id, $this->format_string( __( '[{site_title}] Anti-fraud checks on order #{order_number}', 'yith-woocommerce-anti-fraud' ) ), $this->object );
		}

		/**
		 * Get email heading.
		 *
		 * @since  3.1.0
		 * @return string
		 */
		public function get_heading() {
			return __( 'Anti-Fraud check', 'yith-woocommerce-anti-fraud' );
		}

		/**
		 * Get HTML content
		 *
		 * @since   1.0.5
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function get_content_html() {

			ob_start();

			wc_get_template( $this->template_html, array(
				'email_heading' => $this->get_heading(),
				'order'         => $this->object,
				'sent_to_admin' => true,
				'plain_text'    => false,
				'email'         => $this
			), false, $this->template_base );

			return ob_get_clean();

		}

		/**
		 * Get Plain content
		 *
		 * @since   1.0.5
		 * @return  string
		 * @author  Alberto Ruggiero
		 */
		public function get_content_plain() {

			ob_start();

			wc_get_template( $this->template_plain, array(
				'email_heading' => $this->get_heading(),
				'order'         => $this->object,
				'sent_to_admin' => true,
				'plain_text'    => true,
				'email'         => $this
			), false, $this->template_base );

			return ob_get_clean();

		}

		/**
		 * Checks if this email is enabled and will be sent.
		 * @since   1.0.5
		 * @return  bool
		 * @author  Alberto Ruggiero
		 */
		public function is_enabled() {
			return ( get_option( 'ywaf_admin_mail_enable' ) === 'yes' );
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
		 * @since   1.0.5
		 * @return  void
		 * @author  Alberto Ruggiero
		 */
		public function init_form_fields() {

			$this->form_fields = array(
				'ywaf_admin_mail_recipient' => array(
					'title'       => __( 'Recipient(s)', 'yith-woocommerce-anti-fraud' ),
					'type'        => 'text',
					'id'          => 'ywaf_admin_mail_recipient',
					'description' => sprintf( __( 'Enter recipients (comma separated) for this email. Defaults to <code>%s</code>', 'yith-woocommerce-anti-fraud' ), esc_attr( get_option( 'admin_email' ) ) ),
					'default'     => esc_attr( get_option( 'admin_email' ) ),
					'desc_tip'    => true,

				),
				'ywaf_admin_mail_type'      => array(
					'title'       => __( 'Email type', 'yith-woocommerce-anti-fraud' ),
					'type'        => 'select',
					'default'     => 'html',
					'id'          => 'ywaf_admin_mail_type',
					'description' => __( 'Choose which format of email to send.', 'yith-woocommerce-anti-fraud' ),
					'class'       => 'email_type wc-enhanced-select',
					'options'     => $this->get_email_type_options(),
					'desc_tip'    => true,
				),
			);
		}

	}

}

return new YWAF_Admin_Notification();