<?php
/**
 * Admin date-picker field.
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

$field_id                  = ! empty( $id ) ? $id : '';
$name                      = ! empty( $name ) ? $name : '';
$class                     = ! empty( $class ) ? $class : '';
$class                     = 'yith-wcbk-admin-date-picker ' . $class;
$value                     = ! empty( $value ) ? $value : '';
$data                      = ! empty( $data ) ? $data : array();
$custom_attributes         = ! empty( $custom_attributes ) && is_array( $custom_attributes ) ? $custom_attributes : array();
$default_custom_attributes = array( 'autocomplete' => 'off' );
$custom_attributes         = wp_parse_args( $custom_attributes, $default_custom_attributes );
?>
<span class="yith-wcbk-printer-field__admin-datepicker">
	<?php
	yith_wcbk_print_field(
		array(
			'type'              => 'text',
			'id'                => $field_id,
			'name'              => $name,
			'class'             => $class,
			'data'              => $data,
			'value'             => $value,
			'custom_attributes' => $custom_attributes,
		)
	);
	?>
	<span class="yith-wcbk-printer-field__admin-datepicker__icon"><span class="yith-wcbk-icon yith-wcbk-icon-calendar"></span></span>
</span>
