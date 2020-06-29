<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCPO_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Pre_Order_For_Sale_Date_Changed_Email
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Mora <carlos.eugenio@yourinspiration.it>
 *
 */

if ( ! class_exists( 'YITH_Pre_Order_For_Sale_Date_Changed_Email' ) ) {
	/**
	 * Class YITH_Pre_Order_For_Sale_Date_Changed_Email
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 */
	class YITH_Pre_Order_For_Sale_Date_Changed_Email extends WC_Email {

		public $email_body;

		public function __construct() {

			$this->id = 'yith_ywpo_sale_date_changed';
			$this->customer_email = true;

			$this->title         = esc_html__( 'YITH Pre-Order: For sale date has changed', 'yith-pre-order-for-woocommerce' );
			$this->description   = esc_html__( 'If the administrator changes the "for sale" date, the user will receive an email', 'yith-pre-order-for-woocommerce' );
			$this->heading       = esc_html__( 'Your Pre-Order product has a new release date!', 'yith-pre-order-for-woocommerce' );
			$this->subject       = esc_html__( 'Your Pre-Order product has a new release date!' , 'yith-pre-order-for-woocommerce' );
			$this->email_body    = esc_html__( 'Hi {customer_name}, we would like to inform you that the release date of the product {product_name} you purchased in Pre-Order, has changed from {previous_sale_date} ({offset_name}) to {new_sale_date} ({offset_name})', 'yith-pre-order-for-woocommerce' );
			$this->template_html = 'emails/pre-order-date-changed.php';

			add_action( 'yith_ywpo_sale_date_changed', array( $this, 'trigger' ), 10, 4 );

			parent::__construct();
			$this->email_type = 'html';
		}

		public function trigger( $customer, $product_id, $previous_sale_date, $new_sale_date ) {
			if ( ! $this->is_enabled() ) {
				return;
			}

			$this->object = array(
				'customer_name'      => $customer['name'],
				'customer_email'     => $customer['email'],
				'customer_order_id'  => $customer['order'],
				'product_id'         => $product_id,
				'previous_sale_date' => $previous_sale_date,
				'new_sale_date'      => $new_sale_date,
			);

			$product = wc_get_product( $product_id );

			$gmt_offset = get_option( 'gmt_offset' );
			if ( 0 <= $gmt_offset )
				$offset_name = '+' . $gmt_offset;
			else
				$offset_name = (string)$gmt_offset;

			$offset_name = str_replace( array( '.25', '.5', '.75' ), array( ':15', ':30', ':45' ), $offset_name );
			$offset_name = 'UTC' . $offset_name;

			$this->placeholders = array(
				'{customer_name}'      => $customer['name'],
				'{product_name}'       => $product->get_title(),
				'{previous_sale_date}' => $previous_sale_date,
				'{new_sale_date}'      => $new_sale_date,
				'{offset_name}'        => $offset_name
			);

			$this->recipient = $customer['email'];

			$this->email_body = $this->get_option( 'email_body', esc_html__( 'Hi {customer_name}, we would like to inform you that the release date of the product {product_name} you purchased in Pre-Order, has changed from {previous_sale_date} to {new_sale_date} ({offset_name})', 'yith-pre-order-for-woocommerce' ) );

			$this->send( $this->get_recipient(),
				$this->get_subject(),
				$this->get_content(),
				$this->get_headers(),
				$this->get_attachments() );
		}

		public function get_content_html() {
			return wc_get_template_html( $this->template_html, array(
				'email_heading' => $this->get_heading(),
				'sent_to_admin' => true,
				'plain_text'    => false,
				'email'         => $this
			),
				'',
				YITH_WCPO_TEMPLATE_PATH );
		}

		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'    => array(
					'title'   => esc_html__( 'Enable/Disable', 'yith-pre-order-for-woocommerce' ),
					'type'    => 'checkbox',
					'label'   => esc_html__( 'Enable this email notification', 'yith-pre-order-for-woocommerce' ),
					'default' => 'yes'
				),
				'subject'    => array(
					'title'       => esc_html__( 'Subject', 'yith-pre-order-for-woocommerce' ),
					'type'        => 'text',
					'description' => sprintf( esc_html__( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce' ), $this->subject ),
					'placeholder' => '',
					'default'     => $this->subject,
					'desc_tip'    => true
				),
				'heading'    => array(
					'title'       => esc_html__( 'Email Heading', 'yith-pre-order-for-woocommerce' ),
					'type'        => 'text',
					'description' => sprintf( esc_html__( 'This controls the main heading included in the email notification. Leave blank to use the default heading: <code>%s</code>.', 'woocommerce' ), $this->heading ),
					'placeholder' => '',
					'default'     => $this->heading,
					'desc_tip'    => true
				),
				'email_body' => array(
					'title'       => esc_html__( 'Email Body', 'yith-pre-order-for-woocommerce' ),
					'type'        => 'textarea',
					'description' => sprintf( esc_html__( 'Defaults to <code>%s</code>', 'yith-pre-order-for-woocommerce' ), $this->email_body ),
					'placeholder' => '',
					'default'     => $this->email_body,
				)
			);
		}

	}

}
return new YITH_Pre_Order_For_Sale_Date_Changed_Email();