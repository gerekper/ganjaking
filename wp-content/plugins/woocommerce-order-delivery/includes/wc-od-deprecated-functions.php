<?php
/**
 * Deprecated functions
 *
 * @package WC_OD/Functions
 * @since   1.7.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets the timezone string for the site.
 *
 * @since 1.0.4
 * @deprecated 1.9.0
 *
 * @return string PHP timezone string for the site.
 */
function wc_od_get_timezone_string() {
	wc_deprecated_function( __FUNCTION__, '1.9.0', 'wc_timezone_string' );

	return wc_timezone_string();
}

/**
 * Gets the unix timestamp for a date already adjusted in the site's timezone.
 *
 * @since 1.0.4
 * @deprecated 1.9.0
 *
 * @throws Exception In case of error.
 *
 * @param string $date A local datetime string.
 * @return string The unix timestamp.
 */
function wc_od_local_datetime_to_timestamp( $date ) {
	wc_deprecated_function( __FUNCTION__, '1.9.0' );

	$datetime = new DateTime( $date, new DateTimeZone( wc_timezone_string() ) );

	return $datetime->format( 'U' );
}

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
