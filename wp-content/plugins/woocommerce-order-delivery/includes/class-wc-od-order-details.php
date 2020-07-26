<?php
/**
 * Class to handle the delivery date section in the order details and emails templates
 *
 * @package WC_OD
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Order_Details' ) ) {

	class WC_OD_Order_Details extends WC_OD_Singleton {

		/**
		 * The order ID.
		 *
		 * @since 1.0.0
		 * @deprecated 1.1.0
		 *
		 * @var int The order ID.
		 */
		public $order_id;


		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		protected function __construct() {
			parent::__construct();

			add_action( 'woocommerce_view_order', array( $this, 'order_details' ), 20 );
			add_action( 'woocommerce_thankyou', array( $this, 'order_details' ), 20 );
		}

		/**
		 * Displays the delivery date section at the end of the order details.
		 *
		 * @since 1.0.0
		 *
		 * @param int $order_id The order ID.
		 */
		public function order_details( $order_id ) {
			$order = wc_get_order( $order_id );

			if ( ! $order ) {
				return;
			}

			$delivery_date = $order->get_meta( '_delivery_date' );

			if ( $delivery_date ) {
				$delivery_date_i18n = wc_od_localize_date( $delivery_date );

				if ( $delivery_date_i18n ) {
					wc_od_order_delivery_details(
						array(
							'title'               => __( 'Delivery details', 'woocommerce-order-delivery' ),
							'delivery_date'       => $delivery_date_i18n,
							'delivery_time_frame' => $order->get_meta( '_delivery_time_frame' ),
							'order_id'            => $order_id,
						)
					);
				}
			}
		}

		/**
		 * We use the email subject filter to capture the order data and
		 * include the delivery date section at the end of the emails.
		 *
		 * @since 1.0.0
		 * @deprecated 1.1.0 The order is fetched from the $email parameter in the 'email_footer' method.
		 *
		 * @param string $email_subject The email subject.
		 * @return string The email subject.
		 */
		public function capture_order( $email_subject ) {
			wc_deprecated_function( __METHOD__, '1.1.0' );

			return $email_subject;
		}

		/**
		 * Displays the delivery date section at the end of the emails.
		 *
		 * @since 1.0.0
		 * @deprecated 1.4.1
		 */
		public function email_footer() {
			wc_deprecated_function( __METHOD__, '1.4.1', 'Moved to WC_OD_Emails->delivery_details()' );
		}

	}
}
