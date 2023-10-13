<?php
/**
 * Availability Rule - Availability time slot
 *
 * @var string     $main_class The main class (this is used to create all the classes of the elements in it).
 * @var string     $field_name The field name.
 * @var int|string $index      The time-slot index.
 * @var string     $from       From time.
 * @var string     $to         To time.
 * @var bool       $disabled   Disabled flag.
 *
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

$_field_name       = "{$field_name}[{$index}]";
$disabled          = $disabled ?? false;
$custom_attributes = array();
if ( $disabled ) {
	$custom_attributes['disabled'] = 'disabled';
}

?>
<div class="yith-wcbk-availability__time-slot <?php echo esc_attr( $main_class ); ?>__availability__time-slot" data-index="<?php echo esc_attr( $index ); ?>">
	<?php
	yith_wcbk_print_fields(
		array(
			array(
				'type'              => 'time-select',
				'name'              => $_field_name . '[from]',
				'value'             => $from,
				'custom_attributes' => $custom_attributes,
			),
			array(
				'yith-field'                     => true,
				'type'                           => 'html',
				'html'                           => '<span class="yith-wcbk-availability__time-slot__separator">-</span>',
				'yith-wcbk-field-show-container' => false,
			),
			array(
				'type'              => 'time-select',
				'name'              => $_field_name . '[to]',
				'value'             => $to,
				'custom_attributes' => $custom_attributes,
			),
		)
	);
	?>

	<div class="yith-wcbk-availability__time-slot__actions <?php echo esc_attr( $main_class ); ?>__availability__time-slot__actions">
		<span class="yith-icon yith-icon-trash yith-wcbk-trash-icon-action yith-wcbk-availability__time-slot__action--delete <?php echo esc_attr( $main_class ); ?>__availability__time-slot__action--delete"></span>
	</div>
</div>
