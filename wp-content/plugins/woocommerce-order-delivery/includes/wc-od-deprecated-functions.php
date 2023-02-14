<?php
/**
 * Deprecated functions
 *
 * @package WC_OD/Functions
 * @since   1.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Outputs the content for the wc_od_day_range field.
 *
 * @since 1.0.0
 * @deprecated 2.0.0
 *
 * @param array $field The field data.
 */
function wc_od_day_range_field( $field ) {
	wc_deprecated_function( __FUNCTION__, '2.0.0' );

	$field_id = $field['id'];
	$value    = WC_OD()->settings()->get_setting( $field_id );
	?>
	<label for="<?php echo $field_id; ?>">
		<?php
		printf(
			__( 'Between %1$s and %2$s days.', 'woocommerce-order-delivery' ),
			sprintf(
				'<input id="%1$s" name="%1$s[min]" type="number" value="%2$s" style="%3$s" %4$s />',
				$field_id,
				esc_attr( $value['min'] ),
				esc_attr( $field['css'] ),
				implode( ' ', $field['custom_attributes'] )
			),
			sprintf(
				'<input id="%1$s" name="%1$s[max]" type="number" value="%2$s" style="%3$s" %4$s />',
				$field_id,
				esc_attr( $value['max'] ),
				esc_attr( $field['css'] ),
				implode( ' ', $field['custom_attributes'] )
			)
		);
		?>
	</label>
	<?php if ( $field['desc'] ) : ?>
		<p class="description"><?php echo $field['desc']; ?></p>
	<?php endif; ?>
	<?php
}

/**
 * Displays an admin notice when the minimum requirements are not satisfied for the Subscriptions extension.
 *
 * @since 1.4.1
 * @@deprecated 2.2.0
 */
function wc_od_subscriptions_requirements_notice() {
	wc_deprecated_function( __FUNCTION__, '2.2.0', 'WC_OD_Integration_Subscriptions::requirements_notice()' );

	WC_OD_Integration_Subscriptions::requirements_notice();
}

/**
 * Gets the rates of the specified `Table Rate Shipping` method.
 *
 * @since 1.6.0
 * @deprecated 2.2.0
 *
 * @param mixed $the_method Shipping method object or instance ID.
 * @return array|bool An array with the rates. False on failure.
 */
function wc_od_get_shipping_table_rates( $the_method ) {
	wc_deprecated_function( __FUNCTION__, '2.2.0', 'WC_OD_Integration_Table_Rate_Shipping::get_rates()' );

	$shipping_method = wc_od_get_shipping_method( $the_method );

	if ( ! $shipping_method instanceof WC_Shipping_Table_Rate ) {
		return false;
	}

	return WC_OD_Integration_Table_Rate_Shipping::get_rates( $shipping_method );
}

/**
 * Gets the shipping table rate by field.
 *
 * @since 1.6.0
 * @deprecated 2.2.0
 *
 * @param mixed  $the_method Shipping method object or instance ID.
 * @param string $field      The field key.
 * @param mixed  $value      The field value.
 * @return array|bool An array with the rate data. False on failure.
 */
function wc_od_get_shipping_table_rate_by_field( $the_method, $field, $value ) {
	wc_deprecated_function( __FUNCTION__, '2.2.0', 'WC_OD_Integration_Table_Rate_Shipping::get_rate_by_field()' );

	$shipping_method = wc_od_get_shipping_method( $the_method );

	if ( ! $shipping_method instanceof WC_Shipping_Table_Rate ) {
		return false;
	}

	return WC_OD_Integration_Table_Rate_Shipping::get_rate_by_field( $shipping_method, $field, $value );
}

/**
 * Gets the shipping table rate by ID.
 *
 * @since 1.6.0
 * @deprecated 2.2.0
 *
 * @param mixed $the_method Shipping method object or instance ID.
 * @param int   $rate_id    The rate ID.
 * @return array|bool An array with the rate data. False on failure.
 */
function wc_od_get_shipping_table_rate_by_id( $the_method, $rate_id ) {
	wc_deprecated_function( __FUNCTION__, '2.2.0', 'WC_OD_Integration_Table_Rate_Shipping::get_rate_by_id()' );

	return wc_od_get_shipping_table_rate_by_field( $the_method, 'rate_id', $rate_id );
}

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
