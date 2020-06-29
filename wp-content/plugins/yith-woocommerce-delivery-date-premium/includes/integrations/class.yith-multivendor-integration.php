<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'YITH_Delivery_Date_Multivendor' ) ) {

	class YITH_Delivery_Date_Multivendor {

		public function __construct() {
			//Add into calendar only parent order ( YITH Multivendor )
			add_filter( 'yith_delivery_date_order_has_child', array( $this, 'check_if_order_has_child' ), 10, 2 );
			//add delivery date information into suborders ( YITH Multivendor )
			add_action( 'yith_wcmv_checkout_order_processed', array( $this, 'save_delivery_meta_into_suborders' ), 10 );
			//check if the suborders are shipped to carrier
			add_action( 'yith_delivery_date_suborders_shipped', array( $this, 'shipped_suborders' ), 10, 2 );
		}


		/**
		 * check if the order has suborders
		 *
		 * @param bool $has_child
		 * @param int $order_id
		 *
		 * @return bool
		 * @since 1.0.3
		 * @author YITHEMES
		 */
		public function check_if_order_has_child( $has_child, $order_id ) {

			return wp_get_post_parent_id( $order_id ) != 0;
		}


		/**
		 * synchronize suborders
		 *
		 * @param int $suborder_id
		 *
		 * @since 1.0.3
		 * @author YITHEMES
		 */
		public function save_delivery_meta_into_suborders( $suborder_id ) {

			$parent_order_id = wp_get_post_parent_id( $suborder_id );

			if ( $parent_order_id != 0 ) {

				$parent_order = wc_get_order( $parent_order_id );
				$sub_order    = wc_get_order( $suborder_id );

				$option = get_option( 'ywcdd_processing_type', 'checkout' );

				if ( 'checkout' == $option ) {

					//get meta form order parent
					$delivery_date      = $parent_order->get_meta( 'ywcdd_order_delivery_date' );
					$last_shipping_date = $parent_order->get_meta( 'ywcdd_order_shipping_date' );
					$time_from          = $parent_order->get_meta( 'ywcdd_order_slot_from' );
					$time_to            = $parent_order->get_meta( 'ywcdd_order_slot_to' );
					$carrier_name       = $parent_order->get_meta( 'ywcdd_order_carrier' );
					$carrier_id         = $parent_order->get_meta( 'ywcdd_order_carrier_id' );
					$proc_method        = $parent_order->get_meta( 'ywcdd_order_processing_method' );


					//Save meta into suborder
					$sub_order->update_meta_data( 'ywcdd_order_delivery_date', $delivery_date );
					$sub_order->update_meta_data( 'ywcdd_order_shipping_date', $last_shipping_date );
					$sub_order->update_meta_data( 'ywcdd_order_slot_from', $time_from );
					$sub_order->update_meta_data( 'ywcdd_order_slot_to', $time_to );
					$sub_order->update_meta_data( 'ywcdd_order_carrier', $carrier_name );
					$sub_order->update_meta_data( 'ywcdd_order_carrier_id', $carrier_id );
					$sub_order->update_meta_data( 'ywcdd_order_processing_method', $proc_method );

					$sub_order->save();

				}
				$enable_gdpr_option = get_option( 'ywcdd_user_privacy', 'no' );


			}
		}


		/**
		 * shipped to carrier all suborders
		 *
		 * @param int $order_id
		 * @param string $shipped
		 *
		 * @author YITHEMES
		 * @since 1.0.3
		 */
		public function shipped_suborders( $order_id, $shipped ) {

			$order               = wc_get_order( $order_id );
			$order_has_suborders = yit_get_prop( $order, 'has_sub_order' );

			if ( $order_has_suborders ) {

				$suborders_id = YITH_Orders::get_suborder( $order_id );

				foreach ( $suborders_id as $suborder_id ) {
					$suborder = wc_get_order( $suborder_id );
					yit_save_prop( $suborder, 'ywcdd_order_shipped', $shipped );
				}
			}
		}
	}
}

new YITH_Delivery_Date_Multivendor();