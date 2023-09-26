<?php
/**
 * Deprecated functions
 *
 * @package WC_OD/Functions
 * @since   1.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Looks at the current screen and loads the correct list table handler.
 *
 * @since 1.4.0
 * @deprecated 2.4.0
 */
function wc_od_admin_setup_screen() {
	wc_deprecated_function( __FUNCTION__, '2.4.0', 'WC_OD_Admin->setup_screen()' );
}

/**
 * Updates the columns in the shop order list.
 *
 * @since 1.0.0
 * @deprecated 2.4.0
 *
 * @param array $columns The shop order columns.
 * @return array The modified shop order columns.
 */
function wc_od_admin_shop_order_columns( $columns ) {
	wc_deprecated_function( __FUNCTION__, '2.4.0', 'WC_OD_Admin_List_Table_Orders->get_columns()' );

	return $columns;
}

/**
 * Updates the sortable columns in the shop order list.
 *
 * @since 1.0.0
 * @deprecated 2.4.0
 *
 * @param array $columns The sortable columns list.
 * @return array The filtered sortable columns list.
 */
function wc_od_admin_shop_order_sort_columns( $columns ) {
	wc_deprecated_function( __FUNCTION__, '2.4.0', 'WC_OD_Admin_List_Table_Orders->sortable_columns()' );

	return $columns;
}

/**
 * Prints the content for the custom orders columns.
 *
 * @since 1.0.0
 * @deprecated 2.4.0
 *
 * @global WP_Post $post The current post.
 *
 * @param string $column_id The column ID.
 */
function wc_od_admin_shop_order_posts_column( $column_id ) {
	global $post;

	wc_deprecated_function( __FUNCTION__, '2.4.0', 'WC_OD_Admin_List_Table_Orders->output_column()' );

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
 * @deprecated 2.4.0
 *
 * @param int     $order_id The order ID.
 * @param WP_Post $post     The post instance.
 */
function wc_od_admin_process_shop_order_meta( $order_id, $post ) {
	wc_deprecated_function( __FUNCTION__, '2.4.0', 'WC_OD_Meta_Box_Order_Delivery::save()' );

	WC_OD_Meta_Box_Order_Delivery::save( $order_id, $post );
}
