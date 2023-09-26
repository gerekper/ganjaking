<?php
/**
 * WooCommerce Order Delivery Meta Boxes.
 *
 * @package WC_OD/Admin
 * @since   2.4.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_OD_Admin_Meta_Boxes class.
 */
class WC_OD_Admin_Meta_Boxes {

	/**
	 * Initialize the meta boxes.
	 *
	 * @since 2.4.0
	 */
	public static function init() {
		add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_boxes' ) );
		add_action( 'woocommerce_process_shop_order_meta', 'WC_OD_Meta_Box_Order_Delivery::save', 35, 2 );
	}

	/**
	 * Adds custom meta boxes.
	 *
	 * @since 2.4.0
	 */
	public static function add_meta_boxes() {
		$screen   = wc_od_get_order_admin_screen();
		$order_id = wc_od_get_current_post_or_object_id( $screen );

		if ( $order_id && wc_od_order_is_local_pickup( $order_id ) ) {
			$title = __( 'Pickup details', 'woocommerce-order-delivery' );
		} else {
			$title = __( 'Delivery details', 'woocommerce-order-delivery' );
		}

		add_meta_box( 'woocommerce-order-delivery', $title, 'WC_OD_Meta_Box_Order_Delivery::output', $screen, 'side', 'core' );
	}
}

WC_OD_Admin_Meta_Boxes::init();
