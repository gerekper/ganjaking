<?php
/**
 * Time select field.
 *
 * @var string $id
 * @var string $name
 * @var string $class
 * @var string $value
 * @var array  $data
 * @var array  $custom_attributes
 *
 * @package YITH\Booking\Templates\Fields
 */

defined( 'YITH_WCBK' ) || exit;

$default_time_value = array( '00', '00' );
$time_value         = ! empty( $value ) ? explode( ':', $value ) : $default_time_value;
$time_value         = 2 === count( $time_value ) ? $time_value : $default_time_value;

$hour   = $time_value[0];
$minute = $time_value[1];

$step = apply_filters( 'yith_wcbk_time_select_edit_booking_minute_step', 15 );

?>
<span class="yith-wcbk-time-select__container <?php echo esc_attr( $class ?? '' ); ?>">
	<input type="hidden" class="yith-wcbk-time-select"
			id="<?php echo esc_attr( $id ?? '' ); ?>"
			value="<?php echo esc_attr( $value ); ?>"

		<?php if ( ! ! $name ) : ?>
			name="<?php echo esc_attr( $name ); ?>"
		<?php endif; ?>

		<?php yith_plugin_fw_html_attributes_to_string( $custom_attributes, true ); ?>
		<?php yith_plugin_fw_html_data_to_string( $data, true ); ?>
	/>
	<select class="yith-wcbk-time-select-hour">
		<?php for ( $i = 0; $i < 24; $i ++ ) : ?>
			<?php
			$option_value = $i < 10 ? '0' . $i : $i;
			?>
			<option value='<?php echo esc_attr( $option_value ); ?>'
				<?php selected( $hour, $option_value, true ); ?>
			><?php echo esc_html( $option_value ); ?></option>
		<?php endfor; ?>
	</select>
	<span class="yith-wcbk-time-select__separator">:</span>
	<select class="yith-wcbk-time-select-minute">
		<?php for ( $i = 0; $i < 60; $i += $step ) : ?>
			<?php
			$option_value = $i < 10 ? '0' . $i : $i;
			?>
			<option value='<?php echo esc_attr( $option_value ); ?>'
				<?php selected( $minute, $option_value, true ); ?>
			><?php echo esc_html( $option_value ); ?></option>
		<?php endfor; ?>
	</select>
</span>
