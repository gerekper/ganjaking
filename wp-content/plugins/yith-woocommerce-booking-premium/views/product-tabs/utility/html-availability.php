<?php
/**
 * Availability Rule - Availability.
 *
 * @var string                 $main_class   The main class (this is used to create all the classes of the elements in it).
 * @var string                 $class        The class.
 * @var string                 $field_name   The field name.
 * @var int|string             $index        The availability index.
 * @var YITH_WCBK_Availability $availability The availability.
 * @var array|null             $days         Days.
 *
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

$_field_name   = "{$field_name}[{$index}]";
$full_day_type = $availability->is_full_day() ? 'all-day' : 'set-hours';
$days          = $days ?? array( 'all' => __( 'All days', 'yith-booking-for-woocommerce' ) ) + yith_wcbk_get_days_array( true, false );
$singe_day     = 1 === count( $days ) ? current( $days ) : false;
$class         = $class ?? '';
?>
<div class="yith-wcbk-availability <?php echo esc_attr( $main_class ); ?>__availability <?php echo esc_attr( $class ); ?>"
		data-index="<?php echo esc_attr( $index ); ?>"
		data-full-day-type="<?php echo esc_attr( $full_day_type ); ?>"
>
	<?php

	yith_wcbk_print_fields(
		array(
			array(
				'title'                          => __( 'Set', 'yith-booking-for-woocommerce' ),
				'yith-field'                     => true,
				'type'                           => 'select',
				'class'                          => 'yith-wcbk-availability__day',
				'name'                           => $_field_name . '[day]',
				'options'                        => $days,
				'value'                          => $availability->get_day( 'edit' ),
				'yith-wcbk-field-show-container' => ! $singe_day,
			),
			array(
				'yith-field'                     => true,
				'type'                           => 'html',
				'html'                           => $singe_day ? '<div class="yith-wcbk-availability__single-day-text">' . esc_html( $singe_day ) . '</div>' : '',
				'yith-wcbk-field-show-container' => false,
			),
			array(
				'yith-field'                     => true,
				'type'                           => 'html',
				'html'                           => '<div class="yith-wcbk-availability__set-as-label">' . esc_html( _x( 'as', 'Set day AS bookable', 'yith-booking-for-woocommerce' ) ) . '</div>',
				'yith-wcbk-field-show-container' => false,
			),
			array(
				'yith-field' => true,
				'type'       => 'select',
				'name'       => $_field_name . '[bookable]',
				'options'    => array(
					'yes' => __( 'Bookable', 'yith-booking-for-woocommerce' ),
					'no'  => __( 'Not bookable', 'yith-booking-for-woocommerce' ),
				),
				'value'      => $availability->get_bookable( 'edit' ),
			),
			array(
				'yith-field' => true,
				'type'       => 'select',
				'class'      => 'yith-wcbk-availability__full-day-type',
				'options'    => array(
					'all-day'   => __( 'All day', 'yith-booking-for-woocommerce' ),
					'set-hours' => __( 'Set hours', 'yith-booking-for-woocommerce' ),
				),
				'value'      => $full_day_type,
			),
		)
	);
	?>

	<div class="yith-wcbk-availability__time-slots <?php echo esc_attr( $main_class ); ?>__availability__time-slots">
		<div class="yith-wcbk-availability__time-slots__list <?php echo esc_attr( $main_class ); ?>__availability__time-slots__list">
			<?php

			$slots         = $availability->get_time_slots( 'edit' );
			$slot_index    = 0;
			$slot_disabled = $availability->is_full_day();

			if ( ! $slots ) {
				// Default time slots, if there are no one.
				$slots = array(
					array(
						'from' => '00:00',
						'to'   => '00:00',
					),
				);
			}

			foreach ( $slots as $slot ) {
				$slot_field_name = $_field_name . '[time_slots]';

				yith_wcbk_get_view(
					'product-tabs/utility/html-availability-time-slot.php',
					array(
						'main_class' => $main_class,
						'field_name' => $slot_field_name,
						'index'      => $slot_index,
						'from'       => $slot['from'] ?? '00:00',
						'to'         => $slot['to'] ?? '00:00',
						'disabled'   => $slot_disabled,
					)
				);
				$slot_index ++;
			}
			?>
		</div>
		<div class="yith-wcbk-availability__time-slots__actions <?php echo esc_attr( $main_class ); ?>__availability__time-slots__actions">
			<span class='yith-wcbk-admin-action-link yith-wcbk-availability__time-slots__add-time-slot <?php echo esc_attr( $main_class ); ?>__availability__time-slots__add-time-slot'>+ <?php echo esc_html__( 'Add hours', 'yith-booking-for-woocommerce' ); ?></span>
		</div>
	</div>

	<div class="yith-wcbk-availability__actions <?php echo esc_attr( $main_class ); ?>__availability__actions">
		<span class="yith-icon yith-icon-trash yith-wcbk-trash-icon-action yith-wcbk-availability__action--delete <?php echo esc_attr( $main_class ); ?>__availability__action--delete"></span>
	</div>
</div>
