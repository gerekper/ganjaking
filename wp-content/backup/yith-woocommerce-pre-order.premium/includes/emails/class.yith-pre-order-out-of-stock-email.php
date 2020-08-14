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
 * @class      YITH_Pre_Order_Out_Of_Stock_Email
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Mora <carlos.eugenio@yourinspiration.it>
 *
 */

if ( ! class_exists( 'YITH_Pre_Order_Out_Of_Stock_Email' ) ) {
	/**
	 * Class YITH_Pre_Order_Out_Of_Stock_Email
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 */
	class YITH_Pre_Order_Out_Of_Stock_Email extends WC_Email {

		public $email_body;

		public function __construct() {

			$this->id = 'yith_ywpo_out_of_stock';

			$this->title         = esc_html__( 'YITH Pre-Order: Out-of-Stock products turn into Pre-Order automatically', 'yith-pre-order-for-woocommerce' );
			$this->description   = esc_html__( 'The administrator will receive an email when a product is out-of-stock and turned into Pre-Order.', 'yith-pre-order-for-woocommerce' );
			$this->heading       = esc_html__( 'A product turned into Pre-Order', 'yith-pre-order-for-woocommerce' );
			$this->subject       = esc_html__( 'A product turned into Pre-Order', 'yith-pre-order-for-woocommerce' );
			$this->email_body    = esc_html__( 'Hi admin! We would like to inform you that the product {product_name} is now "Out-of-Stock" and turned into a Pre-Order product.', 'yith-pre-order-for-woocommerce' );
			$this->template_html = 'emails/pre-order-out-of-stock.php';

			add_action( 'yith_ywpo_out_of_stock', array( $this, 'trigger' ) );

			parent::__construct();

			$this->recipient = $this->get_option( 'recipient', get_option( 'admin_email' ) );
			$this->email_type = 'html';
		}

		public function trigger( $product_id ) {
			if ( ! $this->is_enabled() ) {
				return;
			}
			$this->object     = $product_id;
			$product          = wc_get_product( $product_id );

			$this->placeholders = array(
				'{product_name}' => $product->get_title()
			);

			$this->email_body = $this->get_option( 'email_body', esc_html__( 'Hi admin! We would like to inform you that the product {product_name} is now "Out-of-Stock" and turned into a Pre-Order product.', 'yith-pre-order-for-woocommerce' ) );

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
				'recipient' => array(
					'title'         => esc_html__( 'Recipient(s)', 'yith-pre-order-for-woocommerce' ),
					'type'          => 'text',
					'description'   => sprintf( esc_html__( 'Enter recipients (comma separated) for this email. Defaults to %s.', 'yith-pre-order-for-woocommerce' ), '<code>' . esc_attr( get_option( 'admin_email' ) ) . '</code>' ),
					'placeholder'   => '',
					'default'       => '',
					'desc_tip'      => true,
				),
				'subject'    => array(
					'title'       => esc_html__( 'Subject', 'yith-pre-order-for-woocommerce' ),
					'type'        => 'text',
					'description' => sprintf( esc_html__( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'yith-pre-order-for-woocommerce' ), $this->subject ),
					'placeholder' => '',
					'default'     => $this->subject,
					'desc_tip'    => true
				),
				'heading'    => array(
					'title'       => esc_html__( 'Email Heading', 'yith-pre-order-for-woocommerce' ),
					'type'        => 'text',
					'description' => sprintf( esc_html__( 'This controls the main heading included in the email notification. Leave blank to use the default heading: <code>%s</code>.', 'yith-pre-order-for-woocommerce' ), $this->heading ),
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
return new YITH_Pre_Order_Out_Of_Stock_Email();
