<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'YITH_FUNDS_MultiVendor' ) ) {

	class YITH_FUNDS_MultiVendor {

		public function __construct() {

			add_action( 'yith_wcmv_suborder_created', array( $this, 'add_funds_in_sub_orders' ), 20, 3 );
			add_filter( 'yith_account_funds_show_order_metabox', array( $this, 'show_fund_editor_metabox' ) ,10, 3 );
			add_filter( 'ywf_operation_type', array($this, 'add_commission_type'),10, 1 );

		}

		/**
		 * @param int $suborder_id
		 * @param int $parent_order_id
		 * @param int $vendor_id
		 */
		public function add_funds_in_sub_orders( $suborder_id, $parent_order_id, $vendor_id ) {

			$parent_order   = wc_get_order( $parent_order_id );
			$order_total    = $parent_order->get_total('edit');
			$suborder       = wc_get_order( $suborder_id );
			$suborder_total = $suborder->get_total();


			$partial_payment    = $parent_order->get_meta( 'ywf_partial_payment' );
			$funds_used         = $parent_order->get_meta( '_order_funds' );
			$order_fund_removed = $parent_order->get_meta( '_order_fund_removed' );


			if ( ! empty( $funds_used ) ) {

				if ( 'yes' == $partial_payment ) {
					$suborder->update_meta_data( 'ywf_partial_payment', 'yes' );
				}


				$suborder_funds = round( ( $funds_used * $suborder_total )  / ( $order_total+$funds_used ) ,2 );

				$suborder->update_meta_data( '_order_funds', $suborder_funds );

				$suborder->set_total( $suborder_total - $suborder_funds );
				$suborder->update_meta_data( '_order_fund_removed', $order_fund_removed );

				$suborder->save();
			}
		}

		/**
		 * @param bool $show
		 * @param string $is_partial
		 * @param WC_Order $order
		 */
		public function show_fund_editor_metabox( $show, $is_partial, $order ){

			$vendor_can_refund = get_option( 'yith_wpv_vendors_option_order_refund_synchronization' );
			$vendor_can_refund = 'yes' == $vendor_can_refund;
			$order_id = $order->get_id();

			$vendor = yith_get_vendor( 'current', 'user' );
			if($vendor->is_valid() && $vendor->has_limited_access()) {
				if ( wp_get_post_parent_id( $order_id ) != 0 ) {

					if ( 'yes' == $is_partial && ! $vendor_can_refund ) {
						$show = false;
					}
				}
			}
			return $show;
		}

		/**
		 * @param array $operations
		 *
		 * @return array
		 */
		public function add_commission_type($operations) {

			$vendor = yith_get_vendor( 'current', 'user' );

			if( $vendor->is_valid() && $vendor->has_limited_access() ){
				$operations['commission'] = __( 'Commission', 'yith-woocommerce-account-funds');

			}
		return $operations;
		}


	}
}

new YITH_FUNDS_MultiVendor();