<?php
/**
 * Date-picker inline field.
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

$field_id          = ! empty( $id ) ? $id : '';
$name              = ! empty( $name ) ? $name : '';
$class             = ! empty( $class ) ? $class : '';
$value             = ! empty( $value ) ? $value : '';
$custom_attributes = ! empty( $custom_attributes ) ? $custom_attributes : array();
$value             = ! empty( $value ) ? $value : '';
$data              = ! empty( $data ) ? $data : array();

$hidden_form_field_id     = $field_id . '--hidden-form-field';
$data['update-on-change'] = '#' . $hidden_form_field_id;
$data['value']            = $value;

?>
<div class="yith-wcbk-date-picker-inline-wrapper yith-wcbk-clearfix">
	<div id="<?php echo esc_attr( $field_id ); ?>"
			class="yith-wcbk-date-picker yith-wcbk-date-picker--inline <?php echo esc_attr( $class ); ?>"
		<?php yith_plugin_fw_html_attributes_to_string( $custom_attributes, true ); ?>
		<?php yith_plugin_fw_html_data_to_string( $data, true ); ?>
	></div>

	<input id="<?php echo esc_attr( $hidden_form_field_id ); ?>"
			type="hidden"

		<?php if ( ! ! $name ) : ?>
			name="<?php echo esc_attr( $name ); ?>"
		<?php endif; ?>

			value="<?php echo esc_attr( $value ); ?>"
	/>
</div>
