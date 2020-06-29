<?php
/**
 * Class WC_OD_Email_Subscription_Delivery_Note file
 *
 * @package WC_OD\Emails
 * @since   1.4.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Email_Subscription_Delivery_Note' ) ) {

	/**
	 * Subscription Delivery Note Email.
	 *
	 * An email sent to the admin when a note related with the delivery preferences is added to a subscription.
	 *
	 * @class   WC_OD_Email_Subscription_Delivery_Note
	 * @extends WC_OD_Email_Order_Note
	 */
	class WC_OD_Email_Subscription_Delivery_Note extends WC_OD_Email_Order_Note {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id             = 'subscription_delivery_note';
			$this->title          = __( 'Subscription Delivery Note', 'woocommerce-order-delivery' );
			$this->description    = __( 'Subscription Delivery Note emails are sent to chosen recipient(s) when a note related with the delivery preferences is added to a subscription.', 'woocommerce-order-delivery' );
			$this->subject        = __( '[{site_title}] New subscription delivery note ({order_number})', 'woocommerce-order-delivery' );
			$this->heading        = __( 'New subscription delivery note', 'woocommerce-order-delivery' );
			$this->template_html  = 'emails/admin-subscription-delivery-note.php';
			$this->template_plain = 'emails/plain/admin-subscription-delivery-note.php';

			// Triggers for this email.
			add_action( 'wc_od_added_shop_subscription_note_notification', array( $this, 'trigger' ), 10, 3 );

			// Call parent constructor.
			parent::__construct();
		}

		/**
		 * Gets the content arguments.
		 *
		 * @since 1.4.0
		 *
		 * @param string $type Optional. The content type [html, plain].
		 *
		 * @return array
		 */
		public function get_content_args( $type = 'html' ) {
			$args = parent::get_content_args( $type );

			$args['subscription'] = $args['order'];
			unset( $args['order'] );

			return $args;
		}
	}
}

return new WC_OD_Email_Subscription_Delivery_Note();
