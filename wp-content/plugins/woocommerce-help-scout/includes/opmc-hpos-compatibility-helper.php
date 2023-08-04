<?php

 use Automattic\WooCommerce\Utilities\OrderUtil;
 use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;




if ( ! function_exists( 'opmc_hpos_get_post_meta' ) ) {
	function opmc_hpos_get_post_meta( $post_id, $meta_key ) {
		$meta_value = '';
		if ( class_exists( 'Automattic\WooCommerce\Utilities\OrderUtil' ) && OrderUtil::custom_orders_table_usage_is_enabled() ) {
			// HPOS usage is enabled.
			$order     = wc_get_order( $post_id );
			$meta_value = $order->get_meta($meta_key, true);
		} else {
			$meta_value = get_post_meta( $post_id, $meta_key, true );
		}
		return $meta_value;
	}
}

if ( ! function_exists( 'opmc_hpos_update_post_meta' ) ) {
	function opmc_hpos_update_post_meta( $post_id, $meta_key, $meta_value ) {
		if ( class_exists( 'Automattic\WooCommerce\Utilities\OrderUtil' ) && OrderUtil::custom_orders_table_usage_is_enabled() ) {
			$order = wc_get_order( $post_id );
			$order->update_meta_data( $meta_key, $meta_value );
			$order->save();
		} else {
			update_post_meta( $post_id, $meta_key, $meta_value );
		}
	}
}

if ( ! function_exists( 'opmc_hpos_delete_post_meta' ) ) {
	function opmc_hpos_delete_post_meta( $post_id, $meta_key, $meta_value ) {
		if ( class_exists( 'Automattic\WooCommerce\Utilities\OrderUtil' ) && OrderUtil::custom_orders_table_usage_is_enabled() ) {
			$order = wc_get_order( $post_id );
			$order->delete_meta_data( $meta_key, $meta_value );
			$order->save();
		} else {
			delete_post_meta( $post_id, $meta_key );
		}
	}
}

if ( ! function_exists( 'opmc_hpos_get_post_type' ) ) {
	function opmc_hpos_get_post_type( $post_id ) {
		if ( class_exists( 'Automattic\WooCommerce\Utilities\OrderUtil' ) && OrderUtil::custom_orders_table_usage_is_enabled() ) {
			$post_type = OrderUtil::get_order_type( $post_id );
		} else {
			$post_type = get_post_type( $post_id );
		}
		return $post_type;
	}
}

if (!function_exists('opmc_hpos_add_meta_box')) {
	function opmc_hpos_add_meta_box( $id, $title, $callback, $screen, $context = 'advanced', $priority = 'default' ) {
		if ( class_exists( CustomOrdersTableController::class ) &&
			 function_exists( 'wc_get_container' ) &&
			 wc_get_container()->get( CustomOrdersTableController::class )->custom_orders_table_usage_is_enabled() ) {
			$screen = wc_get_page_screen_id( $screen );
		} else {
			$screen = $screen;
		}

		add_meta_box(
			$id,
			$title,
			$callback,
			$screen,
			$context,
			$priority
		);
	}
}
