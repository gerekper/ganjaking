<?php
/**
 * Class YITH_WCBK_Orders_Premium
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\Premium
 */

defined( 'YITH_WCBK' ) || exit;

if ( ! class_exists( 'YITH_WCBK_Orders_Premium' ) ) {
	/**
	 * YITH_WCBK_Orders_Premium class.
	 *
	 * @since   5.0.0
	 */
	class YITH_WCBK_Orders_Premium extends YITH_WCBK_Orders {

		/**
		 * The constructor.
		 */
		protected function __construct() {
			parent::__construct();

			if ( 'yes' === get_option( 'yith-wcbk-show-booking-data-in-order-items', 'no' ) ) {
				add_filter( 'woocommerce_order_item_get_formatted_meta_data', array( $this, 'add_booking_data_in_order_items' ), 10, 2 );
			}
		}

		/**
		 * Add booking data in order items.
		 *
		 * @param array         $formatted_meta The formatted meta.
		 * @param WC_Order_Item $order_item     The order item.
		 *
		 * @return array
		 */
		public function add_booking_data_in_order_items( array $formatted_meta, WC_Order_Item $order_item ) {
			$the_booking_id = $order_item->get_meta( '_booking_id' );

			if ( $the_booking_id ) {
				$is_admin = is_admin();
				$is_email = did_action( 'woocommerce_email_header' ) === ( did_action( 'woocommerce_email_footer' ) + 1 );
				$booking  = yith_get_booking( $the_booking_id );
				$data     = ! ! $booking ? $booking->get_booking_data_to_display( $is_admin && ! $is_email ? 'admin' : 'frontend' ) : array();
				unset( $data['order'], $data['product'], $data['user'], $data['status'] );

				foreach ( $data as $key => $value ) {
					$the_value = $value['display'] ?? '';
					$the_label = $value['label'] ?? '';
					if ( $the_value && $the_label ) {
						$formatted_meta[] = (object) array(
							'key'           => '', // key and value are required here. They are set to empty strings to prevent saving those meta when saving the order.
							'value'         => '',
							'display_key'   => $the_label,
							'display_value' => $the_value,
						);
					}
				}
			}

			return $formatted_meta;
		}
	}
}
