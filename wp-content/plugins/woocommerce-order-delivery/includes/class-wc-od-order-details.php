<?php
/**
 * Class to handle the delivery date section in the order details and emails templates
 *
 * @package WC_OD
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'WC_OD_Order_Details' ) ) {

	class WC_OD_Order_Details {

		use WC_OD_Singleton_Trait;

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		protected function __construct() {
			add_action( 'woocommerce_view_order', array( $this, 'order_details' ), 20 );
			add_action( 'woocommerce_thankyou', array( $this, 'order_details' ), 20 );
		}

		/**
		 * Displays the delivery details in the view order screen.
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

			if ( ! $delivery_date ) {
				return;
			}

			$time_frame = $order->get_meta( '_delivery_time_frame' );

			wc_od_order_delivery_details(
				array(
					'order_id'            => $order_id, // Deprecated.
					'order'               => $order,
					'date'                => $delivery_date,
					'time_frame'          => $time_frame,
					'delivery_date'       => wc_od_localize_date( $delivery_date ), // Deprecated.
					'delivery_time_frame' => $time_frame, // Deprecated.
				)
			);
		}
	}
}
