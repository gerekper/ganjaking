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
 * @class      YITH_Pre_Order_Is_For_Sale_Email
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Mora <carlos.eugenio@yourinspiration.it>
 *
 */

if ( ! class_exists( 'YITH_Pre_Order_Is_For_Sale_Email' ) ) {
	/**
	 * Class YITH_Pre_Order_Date_End_Email
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 */
	class YITH_Pre_Order_Is_For_Sale_Email extends WC_Email {

		public $email_body;

		public function __construct() {
			$this->id = 'yith_ywpo_is_for_sale';
			$this->customer_email = true;

			$this->title         = esc_html__( 'YITH Pre-Order: Pre-order is now for sale', 'yith-pre-order-for-woocommerce' );
			$this->description   = esc_html__( 'The user who purchased some Pre-Order product will receive an email when the product will be for sale.', 'yith-pre-order-for-woocommerce' );
			$this->heading       = esc_html__( 'A Pre-Order product is for sale now!', 'yith-pre-order-for-woocommerce' );
			$this->subject       = esc_html__( 'A Pre-Order product is for sale now!', 'yith-pre-order-for-woocommerce' );
			$this->email_body    = esc_html__( 'Hi {customer_name}, the product {product_name} you purchased in Pre-Order is now available. Your order {order_number} is currently in process.' , 'yith-pre-order-for-woocommerce' );
			$this->template_html = 'emails/pre-order-is-for-sale.php';

			add_action( 'yith_ywpo_is_for_sale', array( $this, 'trigger' ), 10, 2 );

			parent::__construct();
			$this->email_type = 'html';
		}

		public function trigger( $customer, $product_id ) {
			if ( ! $this->is_enabled() ) {
				return;
			}

			$this->object = array(
				'customer_name'      => $customer['name'],
				'customer_email'     => $customer['email'],
				'customer_order_id'  => $customer['order'],
				'product_id'         => $product_id
			);

			$product = wc_get_product( $product_id );

			$this->placeholders = array(
				'{customer_name}' => $customer['name'],
				'{product_name}'  => $product->get_title(),
				'{order_number}'  => $customer['order']
			);

			$this->recipient = $customer['email'];

			$this->email_body = $this->get_option( 'email_body', esc_html__( 'Hi {customer_name}, the product {product_name} you purchased in Pre-Order is now available. Your order {order_number} is currently in process.', 'yith-pre-order-for-woocommerce' ) );

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
return new YITH_Pre_Order_Is_For_Sale_Email();