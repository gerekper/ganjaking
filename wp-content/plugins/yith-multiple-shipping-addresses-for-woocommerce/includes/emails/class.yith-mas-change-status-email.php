<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCMAS_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

if ( ! class_exists( 'YITH_MAS_Shipping_Status_Change_Email' ) ) {
	/**
	 * Send an email to the customer when the Shipping Item status has changed
	 *
	 * @class YITH_MAS_Shipping_Status_Change_Email
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 * @since 1.0.0
	 */
	class YITH_MAS_Shipping_Status_Change_Email extends WC_Email {
		/*
		 * @var string $email_body Content of the email that can be modified
		 */
		public $email_body;

		/**
		 * Construct
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 * @since 1.0.0
		 */
		public function __construct() {

			$this->id = 'yith_ywcmas_shipping_status_change_email';
			$this->customer_email = true;

			$this->title         = _x( 'YITH Multiple Addresses Shipping: Shipping Status change email for user', 'Email descriptive title', 'yith-multiple-shipping-addresses-for-woocommerce' );
			$this->description   = esc_html__( 'Send an email to the customer when the Shipping Item status has changed', 'yith-multiple-shipping-addresses-for-woocommerce' );
			$this->heading       = esc_html__( 'Shipping Status', 'yith-multiple-shipping-addresses-for-woocommerce' );
			$this->subject       = esc_html__( 'The shipping status of your order has been updated', 'yith-multiple-shipping-addresses-for-woocommerce' );
			$this->email_body    = esc_html__( 'A package of your order {order_number} is {new_status}.', 'yith-multiple-shipping-addresses-for-woocommerce' );
			$this->template_html = 'emails/ywcmas-shipping-status-change-email.php';

			add_action( 'ywcmas_shipping_status_change_email', array( $this, 'trigger' ), 10, 5 );

			parent::__construct();
			$this->email_type = 'html';
		}

		/*
		 * Triggering action from 'ywcmas_shipping_status_change_email'
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 * @since 1.0.0
		 */
		public function trigger( $order_id, $shipping_item_id, $old_status, $new_status, $contents ) {
			if ( ! $this->is_enabled() ) {
				return;
			}
			$order = wc_get_order( $order_id );
			$this->recipient  = $order->get_billing_email();
			$this->email_body = $this->get_option( 'email_body', esc_html__( 'A package of your order {order_number} is {new_status}.', 'yith-multiple-shipping-addresses-for-woocommerce' ) );
			$this->order_id   = $order_id;
			$this->shipping_item_id    = $shipping_item_id;
			$this->old_status = $old_status;
			$this->new_status = $new_status;
			$this->contents   = $contents;

			$this->send( $this->get_recipient(),
				$this->get_subject(),
				$this->get_content(),
				$this->get_headers(),
				$this->get_attachments()
			);
		}

		public function get_content_html() {
			return wc_get_template_html( $this->template_html, array(
				'email_heading' => $this->get_heading(),
				'sent_to_admin' => false,
				'plain_text'    => false,
				'email'         => $this
			),
				'',
				YITH_WCMAS_TEMPLATE_PATH );
		}

		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'    => array(
					'title'   => esc_html__( 'Enable/Disable', 'yith-multiple-shipping-addresses-for-woocommerce' ),
					'type'    => 'checkbox',
					'label'   => esc_html__( 'Enable this email notification', 'yith-multiple-shipping-addresses-for-woocommerce' ),
					'default' => 'yes'
				),
				'subject'    => array(
					'title'       => esc_html__( 'Subject', 'yith-multiple-shipping-addresses-for-woocommerce' ),
					'type'        => 'text',
					'description' => sprintf( esc_html__( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'yith-multiple-shipping-addresses-for-woocommerce' ), $this->subject ),
					'placeholder' => '',
					'default'     => ''
				),
				'heading'    => array(
					'title'       => esc_html__( 'Email Heading', 'yith-multiple-shipping-addresses-for-woocommerce' ),
					'type'        => 'text',
					'description' => sprintf( esc_html__( 'This controls the main heading in the email notification. Leave blank to use the default heading: <code>%s</code>.', 'yith-multiple-shipping-addresses-for-woocommerce' ), $this->heading ),
					'placeholder' => '',
					'default'     => ''
				),
				'email_body' => array(
					'title'       => esc_html__( 'Email Body', 'yith-multiple-shipping-addresses-for-woocommerce' ),
					'type'        => 'textarea',
					'description' => sprintf( esc_html__( 'Defaults to <code>%s</code> Use placeholders {order_number}, {new_status} or {old_status}.', 'yith-multiple-shipping-addresses-for-woocommerce' ), $this->email_body ),
					'placeholder' => '',
					'default'     => '',
				)
			);
		}

	}

}
return new YITH_MAS_Shipping_Status_Change_Email();
