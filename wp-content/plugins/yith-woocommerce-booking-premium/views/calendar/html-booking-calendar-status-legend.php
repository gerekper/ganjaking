<?php
/**
 * Booking Calendar Status Legend
 *
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit;

$statuses = yith_wcbk_get_booking_statuses();
?>

<div class="yith-wcbk-booking-calendar__status-legend__list">
	<?php foreach ( $statuses as $key => $label ) : ?>
		<div class="yith-wcbk-booking-calendar__status-legend__item <?php echo esc_attr( $key ); ?>">
			<span class="yith-wcbk-booking-calendar__status-legend__item__indicator"></span>
			<span class="yith-wcbk-booking-calendar__status-legend__item__label"><?php echo esc_html( $label ); ?></span>
		</div>
	<?php endforeach; ?>
</div>
