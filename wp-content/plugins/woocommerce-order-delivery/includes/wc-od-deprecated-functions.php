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
