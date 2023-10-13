<?php
/**
 * Date-picker field.
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

$field_id = ! empty( $id ) ? $id : '';
$name     = ! empty( $name ) ? $name : '';
$class    = ! empty( $class ) ? $class : '';
$value    = ! empty( $value ) ? $value : '';
$data     = ! empty( $data ) ? $data : array();
?>
<div class="yith-wcbk-date-picker-wrapper yith-wcbk-clearfix">
	<?php
	yith_wcbk_print_field(
		array(
			'type'              => 'text',
			'id'                => $field_id,
			'name'              => $name,
			'class'             => 'yith-wcbk-date-picker ' . $class,
			'data'              => $data,
			'value'             => $value,
			'custom_attributes' => 'readonly',
		)
	);
	?>

	<?php
	yith_wcbk_print_field(
		array(
			'type'              => 'text',
			'id'                => $field_id . '--formatted',
			'name'              => '',
			'class'             => 'yith-wcbk-date-picker--formatted ' . $class,
			'custom_attributes' => 'readonly',
		)
	);
	?>
	<span class="yith-wcbk-booking-date-icon yith-icon yith-icon-calendar"></span>
</div>
