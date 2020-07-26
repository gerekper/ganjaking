<?php
/**
 * Admin custom settings
 *
 * @package WC_OD
 * @since   1.0.0
 */

defined( 'ABSPATH' ) || exit;

/** Custom settings *********************************************************/


/**
 * Outputs the content for a custom field within a wrapper.
 *
 * @since 1.0.0
 *
 * @param array $field The field data.
 */
function wc_od_field_wrapper( $field ) {
	// Description handling.
	if ( true === $field['desc_tip'] ) {
		$field['desc_tip'] = $field['desc'];
		$field['desc']     = '';
	} else {
		$field['desc'] = wp_kses_post( $field['desc'] );
	}

	// Custom attributes handling.
	$custom_attributes = array();
	if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) ) {
		foreach ( $field['custom_attributes'] as $attribute => $attribute_value ) {
			$custom_attributes[] = esc_attr( $attribute ) . '="' . esc_attr( $attribute_value ) . '"';
		}
	}

	$field['custom_attributes'] = $custom_attributes;

	// Tooltips position was changed in WC 3.4.
	$tip_html     = ( $field['desc_tip'] ? wc_help_tip( $field['desc_tip'], true ) : '' );
	$tip_in_label = version_compare( WC()->version, '3.4', '>=' );
	?>
	<tr valign="top">
		<th scope="row" class="titledesc">
			<?php
			printf(
				'<label for="%1$s">%2$s%3$s</label>',
				esc_attr( $field['id'] ),
				esc_html( $field['title'] ),
				( $tip_in_label ? " {$tip_html}" : '' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			);

			if ( ! $tip_in_label ) :
				echo $tip_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			endif;
			?>
		</th>
		<td class="forminp forminp-<?php echo esc_attr( $field['type'] ); ?>">
			<?php
			/**
			 * Filters the function used for output the field content within a wrapper.
			 *
			 * @since 1.0.0
			 *
			 * @param callable $callable The callable function.
			 * @param array    $field    The field data.
			 */
			$callback = apply_filters( 'wc_od_field_wrapper_callback', "{$field['type']}_field", $field );

			if ( $callback && is_callable( $callback ) ) :
				call_user_func( $callback, $field );
			endif;
			?>
		</td>
	</tr>
	<?php
}

/**
 * Outputs the content for the wc_od_shipping_days field.
 *
 * @since 1.0.0
 * @param array $field The field data.
 */
function wc_od_shipping_days_field( $field ) {
	$week_days     = wc_od_get_week_days();
	$field_id      = $field['id'];
	$shipping_days = $field['value'];
	?>
	<fieldset>
	<?php foreach ( $shipping_days as $key => $data ) : ?>
		<label for="<?php echo esc_attr( "{$field_id}_{$key}" ); ?>" style="display:inline-block;width:125px;">
		<input id="<?php echo esc_attr( "{$field_id}_{$key}" ); ?>" type="checkbox" name="<?php echo esc_attr( $field_id . "[{$key}][enabled]" ); ?>" <?php checked( wc_string_to_bool( $data['enabled'] ), true ); ?> />
		<?php echo wp_kses_post( $week_days[ $key ] ); ?></label>

		<?php $limit_id = wc_od_maybe_prefix( "shipping_days_time_{$key}" ); ?>
		<label for="<?php echo esc_attr( $limit_id ); ?>">
			<span class="shipping-days-time-label" style="font-size:12px;"><?php _e( 'limit:', 'woocommerce-order-delivery' ); ?></span>
			<input class="timepicker" id="<?php echo esc_attr( $limit_id ); ?>" type="text" name="<?php echo esc_attr( $field_id . "[{$key}][time]" ); ?>" value="<?php echo esc_attr( $data['time'] ); ?>" style="width:80px;vertical-align:middle" />
		</label>
		<br>
	<?php endforeach; ?>

	<?php if ( $field['desc'] ) : ?>
		<p class="description"><?php echo $field['desc']; ?></p>
	<?php endif; ?>
	</fieldset>
	<?php
}

/**
 * Outputs the content for the wc_od_day_range field.
 *
 * @since 1.0.0
 * @param array $field The field data.
 */
function wc_od_day_range_field( $field ) {
	$field_id = $field['id'];
	$value    = WC_OD()->settings()->get_setting( $field_id );
	?>
	<label for="<?php echo $field_id; ?>">
	<?php
		printf( __( 'Between %1$s and %2$s days.', 'woocommerce-order-delivery' ),
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
 * Outputs the content for the wc_od_delivery_days field.
 *
 * @since 1.0.0
 *
 * @param array $field The field data.
 */
function wc_od_delivery_days_field( $field ) {
	wc_od_table_field( $field );
}

/**
 * Gets the instance for the wc_od_table field.
 *
 * @since 1.5.0
 *
 * @param array $field The field data.
 * @return mixed The table field instance.
 */
function wc_od_get_table_field( $field ) {
	$field_id = wc_od_no_prefix( $field['id'] );

	// TODO: Use the second parameter of the ucwords function for PHP 5.4.32+.
	$class_suffix = join( '_', array_map( 'ucfirst', preg_split( '/[_-]+/', $field_id ) ) );

	/**
	 * Filter the class name of the table field.
	 *
	 * @since 1.5.0
	 *
	 * @param string $class The class name.
	 * @param array  $field The field data.
	 */
	$class = apply_filters( 'wc_od_table_field_class', "WC_OD_Admin_Field_{$class_suffix}", $field );

	return new $class( $field );
}

/**
 * Outputs the content for a wc_od_table field.
 *
 * @since 1.5.0
 *
 * @param array $field The field data.
 */
function wc_od_table_field( $field ) {
	$instance = wc_od_get_table_field( $field );

	if ( $instance ) {
		$instance->output();
	}
}
