<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YITH_WCCOS_Email' ) ) {
	class YITH_WCCOS_Email extends WC_Email {
		/**
		 * @type string
		 */
		public $heading = '';
		/**
		 * @type string
		 */
		public $from_name = '';
		/**
		 * @type string
		 */
		public $from_email = '';
		/**
		 * @type string
		 */
		public $custom_message = '';
		/**
		 * @type string
		 */
		public $custom_email_address = '';
		/**
		 * @type bool
		 */
		public $sent_to_admin = false;
		/**
		 * @type bool
		 */
		public $display_order_info = false;
		/**
		 * @type WC_Order
		 */
		public $order;

		public function __construct() {
			$this->id          = "custom_order_status_email";
			$this->title       = __( 'Custom Order Status Mail', 'yith-woocommerce-custom-order-status' );
			$this->description = __( "YITH WooCommerce Custom Order Status Mail", 'yith-woocommerce-custom-order-status' );

			$this->template_html  = 'emails/custom_status_email_template.php';
			$this->template_plain = 'emails/plain/custom_status_email_template.php';
			$this->template_base  = YITH_WCCOS_TEMPLATE_PATH . '/';

			// Triggers
			add_action( 'yith_wccos_custom_order_status_notification', array( $this, 'trigger' ) );

			parent::__construct();
		}

		public function is_pretty_mail_active() {
			return class_exists( 'WooCommerce_Pretty_Emails' ) && defined( 'MBWPE_TPL_PATH' );
		}

		/**
		 * Trigger.
		 *
		 * @param array $args
		 */
		public function trigger( $args ) {
			if ( ! $this->is_enabled() ) {
				return;
			}

			$requested_fields = array(
				'heading',
				'subject',
				'from_name',
				'from_email',
				'display_order_info',
				'custom_email_address',
				'order',
				'recipient',
				'sent_to_admin',
			);
			if ( $args ) {
				$args = apply_filters( 'yith_wccos_email_trigger_args', $args, $this );

				foreach ( $requested_fields as $field ) {
					if ( ! isset( $args[ $field ] ) ) {
						return;
					}
				}

				$this->sent_to_admin        = $args['sent_to_admin'];
				$this->customer_email       = ! $this->sent_to_admin;
				$this->setup_locale();

				$this->order                = $args['order'];
				$this->object               = $this->order;
				$this->heading              = apply_filters( 'yith_wccos_email_heading', $args['heading'], $this->order, $this );
				$this->subject              = $this->format_string( apply_filters( 'yith_wccos_email_subject', $args['subject'], $this->order, $this ) );
				$this->from_name            = stripslashes( $args['from_name'] );
				$this->from_email           = $args['from_email'];
				$this->display_order_info   = $args['display_order_info'];
				$this->custom_email_address = $args['custom_email_address'];
				$this->custom_message       = stripslashes( nl2br( apply_filters( 'yith_wccos_custom_message', $args['custom_message'], $this->order, $this ) ) );
				$this->recipient            = $args['recipient'];

				if ( $this->get_recipient() ) {
					$this->send( $this->get_recipient(), $this->subject, $this->get_content(), $this->get_headers(), $this->get_attachments() );
				}

				$this->restore_locale();
			}
		}

		/**
		 * @param $content
		 * @param $order_id
		 *
		 * @return string
		 * @deprecated since 1.1.14 use YITH_WCCOS_Email::format_string instead
		 */
		public function apply_shortcode( $content, $order_id ) {
			return stripslashes( nl2br( $this->format_string( $content ) ) );
		}

		/**
		 * @param mixed $string
		 *
		 * @return string
		 */
		public function format_string( $string ) {
			$order = $this->order;

			$placeholders = array(
				'{customer_first_name}' => yit_get_prop( $order, 'billing_first_name', true ),
				'{customer_last_name}'  => yit_get_prop( $order, 'billing_last_name', true ),
				'{order_date}'          => date_i18n( wc_date_format(), strtotime( yit_get_prop( $order, 'order_date', true ) ) ),
				'{order_number}'        => $order->get_order_number(),
				'{order_value}'         => yit_get_prop( $order, 'order_total', true ),
				'{billing_address}'     => $order->get_formatted_billing_address(),
				'{shipping_address}'    => $order->get_formatted_shipping_address(),
			);

			$placeholders = apply_filters( 'yith_wccos_email_placeholders', $placeholders, $order, $this );

			$string = strtr( $string, $placeholders );

			preg_match_all( '/\{[^}]+\}/', $string, $custom_placeholders );

			if ( ! empty( $custom_placeholders ) && ! empty( $custom_placeholders[0] ) ) {
				foreach ( $custom_placeholders[0] as $occurrence ) {
					$meta_key   = str_replace( array( '{', '}' ), '', $occurrence );
					$meta_value = $order->get_meta( $meta_key );
					$meta_value = ! ! $meta_value ? $meta_value : '';

					$string = str_replace( $occurrence, $meta_value, $string );
				}
			}

			return parent::format_string( $string );
		}

		public function get_content_html() {
			$base = $this->is_pretty_mail_active() ? $this->template_base . 'pretty-emails/' : $this->template_base;

			ob_start();
			wc_get_template( $this->template_html, array(
				'order'              => $this->order,
				'email_heading'      => $this->format_string( $this->heading ),
				'custom_message'     => $this->format_string( $this->custom_message ),
				'display_order_info' => $this->display_order_info,
				'sent_to_admin'      => $this->sent_to_admin,
				'email'              => $this,
				'plain_text'         => false,
			), '', $base );

			return ob_get_clean();
		}

		/**
		 * Get content plain.
		 *
		 * @return string
		 */
		public function get_content_plain() {
			ob_start();
			wc_get_template( $this->template_plain, array(
				'order'              => $this->order,
				'email_heading'      => $this->format_string( $this->heading ),
				'custom_message'     => $this->format_string( $this->custom_message ),
				'display_order_info' => $this->display_order_info,
				'sent_to_admin'      => $this->sent_to_admin,
				'email'              => $this,
				'plain_text'         => true,
			), '', $this->template_base );

			return ob_get_clean();
		}

		/**
		 * Initialise Settings Form Fields - these are generic email options most will use.
		 */
		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'    => array(
					'title'   => __( 'Enable/Disable', 'woocommerce' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable this email notification', 'woocommerce' ),
					'default' => 'yes',
				),
				'email_type' => array(
					'title'       => __( 'Email type', 'woocommerce' ),
					'type'        => 'select',
					'description' => __( 'Choose which format of email to send.', 'woocommerce' ),
					'default'     => 'html',
					'class'       => 'email_type wc-enhanced-select',
					'options'     => $this->get_email_type_options(),
				),
			);
		}
	}
}

return new YITH_WCCOS_Email();