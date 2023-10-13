<?php
/**
 * Default availabilities.
 *
 * @var string                   $field_name             The field name.
 * @var YITH_WCBK_Availability[] $default_availabilities Availabilities
 *
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit; // Exit if accessed directly.

$main_class  = 'yith-wcbk-default-availabilities';
$weekly_days = yith_wcbk_get_days_array( true, false );
?>
<div class="yith-wcbk-default-availabilities">
	<div class="yith-wcbk-default-availabilities__list">
		<?php
		$index = 0;
		foreach ( $default_availabilities as $availability ) {
			$days  = $weekly_days;
			$class = '';
			if ( ! $index ) {
				$availability->set_day( 'all' );

				$days  = array( 'all' => __( 'All days', 'yith-booking-for-woocommerce' ) );
				$class = 'yith-wcbk-default-availability--all-day';
			}

			yith_wcbk_get_view(
				'product-tabs/utility/html-availability.php',
				array(
					'main_class'   => $main_class,
					'class'        => $class,
					'field_name'   => $field_name,
					'index'        => $index,
					'availability' => $availability,
					'days'         => $days,
				)
			);
			$index ++;
		}
		?>
	</div>
	<div class="yith-wcbk-default-availabilities__actions">
		<span class='yith-wcbk-admin-action-link yith-wcbk-default-availabilities__actions__add-availability'>+ <?php echo esc_html__( 'Add options for specific days', 'yith-booking-for-woocommerce' ); ?></span>
	</div>
</div>

<script type="text/html" id="tmpl-yith-wcbk-default-availability">
	<?php
	$availability = new YITH_WCBK_Availability();

	yith_wcbk_get_view(
		'product-tabs/utility/html-availability.php',
		array(
			'main_class'   => $main_class,
			'field_name'   => $field_name,
			'index'        => '{{data.availabilityIndex}}',
			'availability' => $availability,
			'days'         => $weekly_days,
		)
	);
	?>
</script>

<script type="text/html" id="tmpl-yith-wcbk-default-availability-time-slot">
	<?php
	$_field_name = "{$field_name}[{{data.availabilityIndex}}][time_slots]";

	yith_wcbk_get_view(
		'product-tabs/utility/html-availability-time-slot.php',
		array(
			'main_class' => $main_class,
			'field_name' => $_field_name,
			'index'      => '{{data.timeSlotIndex}}',
			'from'       => '00:00',
			'to'         => '00:00',
		)
	);
	?>
</script>
