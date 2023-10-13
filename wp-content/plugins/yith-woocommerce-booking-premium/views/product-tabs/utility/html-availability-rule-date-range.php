<?php
/**
 * Availability Rule - Date Range.
 *
 * @var string $field_name       The field name.
 * @var int    $index            The index.
 * @var int    $date_range_index The date range index.
 * @var string $from             The 'from' value.
 * @var string $to               The 'to' value.
 * @var string $type               The type of availability range (specific or generic).
 *
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

$_field_name = "{$field_name}[{$index}][date_ranges][{$date_range_index}]";
$_type       = $type ?? 'generic';

?>
<div class="yith-wcbk-availability-rule__date-range" data-index="<?php echo esc_attr( $date_range_index ); ?>">
	<?php
	yith_wcbk_print_fields(
		array(
			array(
				'title'             => __( 'From', 'yith-booking-for-woocommerce' ),
				'class'             => 'bk_ar_enable_if_type_specific',
				'type'              => 'admin-datepicker',
				'name'              => $_field_name . '[from]',
				'custom_attributes' => 'specific' === $_type ? array() : array( 'disabled' => 'disabled' ),
				'value'             => 'specific' === $_type ? $from : '',
			),
			array(
				'type'              => 'day-month',
				'class'             => 'yith-wcbk-availability-rule__date-range__generic-month bk_ar_enable_if_type_generic',
				'name'              => $_field_name . '[from]',
				'custom_attributes' => 'generic' === $_type ? '' : 'disabled="disabled"',
				'value'             => 'generic' === $_type ? $from : '',
			),
			array(
				'type'              => 'admin-datepicker',
				'class'             => 'bk_ar_enable_if_type_specific',
				'title'             => __( 'To', 'yith-booking-for-woocommerce' ),
				'name'              => $_field_name . '[to]',
				'custom_attributes' => 'specific' === $_type ? array() : array( 'disabled' => 'disabled' ),
				'value'             => 'specific' === $_type ? $to : '',
			),
			array(
				'type'              => 'day-month',
				'class'             => 'yith-wcbk-availability-rule__date-range__generic-month bk_ar_enable_if_type_generic',
				'name'              => $_field_name . '[to]',
				'custom_attributes' => 'generic' === $_type ? '' : 'disabled="disabled"',
				'value'             => 'generic' === $_type ? $to : '',
			),
		)
	);
	?>

	<div class="yith-wcbk-availability-rule__condition__actions">
		<span class="yith-icon yith-icon-trash yith-wcbk-trash-icon-action yith-wcbk-availability-rule__date-range__action yith-wcbk-availability-rule__date-range__action--delete"></span>
	</div>
</div>
