<?php
/**
 * Include the Opmc-hpos-compatibility-helper.php file if it hasn't been included before.
 *
 * This code includes the Opmc-hpos-compatibility-helper.php file in the current PHP script. It uses the include_once
 * function to ensure that the file is included only once, even if this code is executed multiple times.
 *
 * @param string $file_path The path to the Opmc-hpos-compatibility-helper.php file.
 * @return bool True if the file is successfully included, false otherwise.
 * @package   PHPCompatibility
 */

 use Automattic\WooCommerce\Utilities\OrderUtil;
 use Automattic\WooCommerce\Internal\DataStores\Orders\CustomOrdersTableController;

if ( ! function_exists( 'opmc_hpos_get_post_meta' ) ) {

	/**
	 * Retrieve post meta value for a specific post ID and meta key.
	 *
	 * @param int    $post_id   The ID of the post.
	 * @param string $meta_key  The meta key to retrieve the value for.
	 *
	 * @return mixed The value of the meta key for the post.
	 */
	function opmc_hpos_get_post_meta( $post_id, $meta_key ) {
		$meta_value = '';
		if ( class_exists( 'Automattic\WooCommerce\Utilities\OrderUtil' ) && OrderUtil::custom_orders_table_usage_is_enabled() ) {
			// HPOS usage is enabled.
			$order     = wc_get_order( $post_id );
			$meta_value = $order->get_meta( $meta_key, true );
		} else {
			$meta_value = get_post_meta( $post_id, $meta_key, true );
		}
		return $meta_value;
	}
}

if ( ! function_exists( 'opmc_hpos_update_post_meta' ) ) {
	/**
	 * Update post meta.
	 *
	 * @param int    $post_id    The ID of the post.
	 * @param string $meta_key   The meta key.
	 * @param mixed  $meta_value The meta value to update.
	 */
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
	/**
	 * Delete post meta for a specific post ID.
	 *
	 * @param int    $post_id The ID of the post.
	 * @param string $meta_key The meta key of the post meta.
	 * @param mixed  $meta_value Optional. The value of the post meta to delete. Default is empty.
	 */
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
	/**
	 * Function for opmc_hpos_get_post_type().
	 *
	 * @param int $post_id The ID of the post.
	 */
	function opmc_hpos_get_post_type( $post_id ) {
		if ( class_exists( 'Automattic\WooCommerce\Utilities\OrderUtil' ) && OrderUtil::custom_orders_table_usage_is_enabled() ) {
			$post_type = OrderUtil::get_order_type( $post_id );
		} else {
			$post_type = get_post_type( $post_id );
		}
		return $post_type;
	}
}

if ( ! function_exists( 'opmc_hpos_add_meta_box' ) ) {
	/**
	 * Adds a meta box to the specified screen.
	 *
	 * @param string   $id        Unique ID for the meta box.
	 * @param string   $title     Title of the meta box.
	 * @param callable $callback Callback function to render the meta box content.
	 * @param string   $screen    The screen or screens on which to show the meta box.
	 * @param string   $context   (Optional) The context in which to display the meta box. Default is 'advanced'.
	 * @param string   $priority  (Optional) The priority within the context where the meta box should be shown. Default is 'default'.
	 */
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
