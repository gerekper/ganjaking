<?php
/**
 * Admin Init
 *
 * @package WC_OD/Admin
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Admin init.
 *
 * @since 1.0.0
 * @deprecated 1.6.0
 */
function wc_od_admin_init() {
	wc_deprecated_function( 'wc_od_admin_init', '1.6.0' );
}

/**
 * Looks at the current screen and loads the correct list table handler.
 *
 * Based on the method WC_Admin_Post_Types::setup_screen().
 *
 * @since 1.4.0
 */
function wc_od_admin_setup_screen() {
	$screen_id = wc_od_get_current_screen_id();

	switch ( $screen_id ) {
		case 'edit-shop_order':
			include_once 'list-table/class-wc-od-admin-list-table-orders.php';
			new WC_OD_Admin_List_Table_Orders();
			break;
	}

	// Ensure the table handler is only loaded once. Prevents multiple loads if a plugin calls check_ajax_referer many times.
	remove_action( 'current_screen', 'wc_od_admin_setup_screen', 20 );
	remove_action( 'check_ajax_referer', 'wc_od_admin_setup_screen', 20 );
}
add_action( 'current_screen', 'wc_od_admin_setup_screen', 20 );
add_action( 'check_ajax_referer', 'wc_od_admin_setup_screen', 20 );


/** Shop Orders functions *****************************************************/


/**
 * Updates the columns in the shop order list.
 *
 * Added 'shipping_date' column in the version 1.4.0.
 *
 * @since 1.0.0
 *
 * @param array $columns The shop order columns.
 * @return array The modified shop order columns.
 */
function wc_od_admin_shop_order_columns( $columns ) {
	$index = array_search( 'order_date', array_keys( $columns ) );
	$modified_columns = array_slice( $columns, 0, $index );
	$modified_columns['shipping_date'] = __( 'Shipping Date', 'woocommerce-order-delivery' );
	$modified_columns['delivery_date'] = __( 'Delivery Date', 'woocommerce-order-delivery' );
	$modified_columns = array_merge( $modified_columns, array_slice( $columns, $index ) );

	return $modified_columns;
}
add_filter( 'manage_edit-shop_order_columns', 'wc_od_admin_shop_order_columns', 20 );

/**
 * Updates the sortable columns in the shop order list.
 *
 * Added 'shipping_date' column in the version 1.4.0.
 *
 * @since 1.0.0
 *
 * @param array $columns The sortable columns list.
 * @return array The filtered sortable columns list.
 */
function wc_od_admin_shop_order_sort_columns( $columns ) {
	$columns['shipping_date'] = 'shipping_date';
	$columns['delivery_date'] = 'delivery_date';

	return $columns;
}
add_filter( "manage_edit-shop_order_sortable_columns", 'wc_od_admin_shop_order_sort_columns' );

/**
 * Prints the content for the custom orders columns.
 *
 * @since 1.0.0
 *
 * @global WP_Post $post The current post.
 *
 * @param string $column_id The column ID.
 */
function wc_od_admin_shop_order_posts_column( $column_id ) {
	global $post;

	if ( in_array( $column_id, array( 'shipping_date', 'delivery_date' ), true ) ) {
		$date = wc_od_get_order_meta( $post->ID, "_{$column_id}" );

		if ( $date ) {
			printf(
				'<time datetime="%1$s" title="%2$s">%3$s</time>',
				esc_attr( wc_od_localize_date( $date, 'c' ) ),
				esc_html( wc_od_localize_date( $date, get_option( 'date_format' ) ) ),
				esc_html( wc_od_localize_date( $date, wc_od_get_date_format( 'admin' ) ) )
			);

			// Maybe display the delivery time frame.
			if ( 'delivery_date' === $column_id ) {
				$time_frame = wc_od_get_order_meta( $post->ID, '_delivery_time_frame' );

				if ( $time_frame ) {
					printf(
						'<br><span class="delivery-time-frame">%1$s</span>',
						wp_kses_post( wc_od_time_frame_to_string( $time_frame ) )
					);
				}
			}
		} else {
			echo '<span class="na">–</span>';
		}
	}
}
add_action( 'manage_shop_order_posts_custom_column', 'wc_od_admin_shop_order_posts_column', 20 );

/**
 * Adds the query vars for order by our custom columns.
 *
 * @since 1.0.0
 *
 * @global string $typenow The current post type.
 *
 * @param array $vars The query vars.
 * @return array The filtered query vars.
 */
function wc_od_admin_shop_order_orderby( $vars ) {
	global $typenow;

	if ( 'shop_order' !== $typenow ) {
		return $vars;
	}

	// Sorting
	if ( isset( $vars['orderby'] ) ) {
		if ( in_array( $vars['orderby'], array( 'shipping_date', 'delivery_date' ) ) ) {
			$vars = array_merge( $vars, array(
				'meta_key' => "_{$vars['orderby']}",
				'orderby'  => 'meta_value_num',
			) );
		}
	}

	return $vars;
}
add_filter( 'request', 'wc_od_admin_shop_order_orderby' );

/**
 * Filters the order by query for cast the meta_value as date.
 *
 * @since 1.0.0
 *
 * @global wpdb $wpdb The WordPress Database Access Abstraction Object.
 *
 * @param string $orderby The orderby query.
 * @param array $query    The query parameters.
 * @return string The filtered orderby query.
 */
function wc_od_admin_posts_orderby_date( $orderby, $query ) {
    global $wpdb;

	if ( 'shop_order' === $query->get( 'post_type' ) && in_array( $query->get( 'meta_key' ), array( '_shipping_date', '_delivery_date' ) ) ) {
		$orderby = "CAST( $wpdb->postmeta.meta_value AS DATE ) " . $query->get( 'order' );
	}

	return $orderby;
}
add_filter( 'posts_orderby', 'wc_od_admin_posts_orderby_date', 10, 2 );


/** Edit Order functions ******************************************************/


/**
 * Adds the date fields to the 'Order Details' meta box.
 *
 * @since 1.0.0
 * @deprecated 1.5.0 Moved code to the method `WC_OD_Meta_Box_Order_Delivery::output`.
 *
 * @param WC_Order $order The order.
 */
function wc_od_admin_order_data_after_order_details( $order ) {
	wc_deprecated_function( 'wc_od_admin_order_data_after_order_details', '1.5.0' );
}

/**
 * Saves the date fields on the edit-order page.
 *
 * The dates are saved before the address data (Priority 40).
 * You should use the $_POST variable to get the updated information.
 *
 * The change of the order status and the email notification is sent with priority 40. To attach the
 * dates correctly to the emails, we have to use a lower priority.
 *
 * @since 1.0.0
 * @since 1.5.0 Moved code to the method `WC_OD_Meta_Box_Order_Delivery::save`.
 *
 * @param int     $order_id The order ID.
 * @param WP_Post $post     The post instance.
 */
function wc_od_admin_process_shop_order_meta( $order_id, $post ) {
	WC_OD_Meta_Box_Order_Delivery::save( $order_id, $post );
}
add_action( 'woocommerce_process_shop_order_meta', 'wc_od_admin_process_shop_order_meta', 35, 2 );
