<?php
/**
 * Day-month field.
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

$field_id          = $id ?? '';
$name              = $name ?? '';
$class             = $class ?? '';
$custom_attributes = $custom_attributes ?? '';
$data              = $data ?? array();
$value             = ! empty( $value ) ? $value : '';
if ( strpos( $value, '-' ) === false ) {
	$value = '01-01';
}

list( $month, $day ) = explode( '-', $value );

$day   = absint( $day );
$month = absint( $month );

?>
<div id="<?php echo esc_attr( $field_id ); ?>"
		class="yith-wcbk-day-month <?php echo esc_attr( $class ); ?>"
		data-value="<?php echo esc_attr( $value ); ?>"
	<?php
	yith_plugin_fw_html_attributes_to_string( $custom_attributes, true );
	yith_plugin_fw_html_data_to_string( $data, true );
	?>
>
	<input type="hidden" class="yith-wcbk-day-month__value" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>"/>

	<input type="number" class="yith-wcbk-day-month__day" min="1" max="31" value="<?php echo esc_attr( $day ); ?>"/>

	<select class="yith-wcbk-day-month__month">
		<?php foreach ( yith_wcbk_get_months_array( true ) as $month_id => $month_name ) : ?>
			<option value="<?php echo esc_attr( $month_id ); ?>" <?php selected( $month_id, $month ); ?>><?php echo esc_html( $month_name ); ?></option>
		<?php endforeach; ?>
	</select>
</div>
