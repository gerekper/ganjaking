<?php
/**
 * Class WC_OD_Email_Order_Delivery_Note file
 *
 * @package WC_OD\Emails
 * @since   1.4.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Email_Order_Delivery_Note' ) ) {

	/**
	 * Order Delivery Note Email.
	 *
	 * An email sent to the admin when a note related with the delivery preferences is added to an order.
	 *
	 * @class   WC_OD_Email_Order_Delivery_Note
	 * @extends WC_OD_Email_Order_Note
	 */
	class WC_OD_Email_Order_Delivery_Note extends WC_OD_Email_Order_Note {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id             = 'order_delivery_note';
			$this->title          = __( 'Order Delivery Note', 'woocommerce-order-delivery' );
			$this->description    = __( 'Order Delivery Note emails are sent to chosen recipient(s) when a note related with the delivery preferences is added to an order.', 'woocommerce-order-delivery' );
			$this->subject        = __( '[{site_title}] New order delivery note ({order_number}) - {order_date}', 'woocommerce-order-delivery' );
			$this->heading        = __( 'New order delivery note', 'woocommerce-order-delivery' );
			$this->template_html  = 'emails/admin-order-delivery-note.php';
			$this->template_plain = 'emails/plain/admin-order-delivery-note.php';

			// Triggers for this email.
			add_action( 'wc_od_added_shop_order_note_notification', array( $this, 'trigger' ), 10, 3 );

			// Call parent constructor.
			parent::__construct();
		}
	}

}

return new WC_OD_Email_Order_Delivery_Note();
