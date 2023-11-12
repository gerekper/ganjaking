<?php
/**
 * WooCommerce Subscriptions Plugin compatibility
 *
 * @package NeverSettle\WooCommerce-Amazon-Fulfillment
 * @since 4.2.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'NS_MCF_Abstract_Compatibility' ) ) {
	return;
}

if ( ! class_exists( 'NS_MCF_Woo_Subs' ) ) {

	/**
	 * WooCommerce Subscriptions Compatibility class.
	 *
	 * @link https://woocommerce.com/products/woocommerce-subscriptions/
	 */
	class NS_MCF_Woo_Subs extends NS_MCF_Abstract_Compatibility {


		/**
		 * The single instance of the class.
		 *
		 * @var NS_MCF_Woo_Subs $instance
		 */
		private static $instance;

		/**
		 * SINGLETON INSTANCE
		 */
		public static function get_instance( NS_FBA $ns_fba ): NS_MCF_Woo_Subs {
			if ( null === self::$instance ) {
				self::$instance = new self( $ns_fba );
			}
			return self::$instance;
		}

		/**
		 * This method is used by the subclass to implement compatibility checks and change normal behaviour.
		 * This is only executed is the function `is_active()` returns true.
		 *
		 * @return void
		 */
		public function maybe_apply_compatibility() {
			add_filter( 'ns_fba_skip_post_fulfillment_order', array( $this, 'check_if_order_contains_switch' ), 10, 2 );
		}

		/**
		 * Check if current order contains a subscription switch.
		 * We do not want to fulfil the order twice if its just a switch.
		 *
		 * @param bool     $send Boolean response to send order.
		 * @param WC_Order $order The order to be sent to Seller Partner.
		 *
		 * @return void|bool
		 */
		public function check_if_order_contains_switch( $send, $order ) {
			if ( ! function_exists( 'wcs_order_contains_switch' ) ) {
				return false;
			}

			$switch_processed = wcs_get_objects_property( $order, 'completed_subscription_switch' );

			if ( ! wcs_order_contains_switch( $order ) || 'true' === $switch_processed ) {
				return false;
			}

			$order->update_meta_data( '_sent_to_fba', gmdate( 'm-d-Y H:i:s', time() ) );
			$order->add_order_note( __( 'Subscription switch. Skipping fulfilment', $this->ns_fba->text_domain ) );
			$order->save();
			return true;
		}
	}
}
