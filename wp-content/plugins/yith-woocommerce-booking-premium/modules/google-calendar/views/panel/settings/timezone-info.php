<?php
/**
 * Timezone info.
 *
 * @var bool   $is_calendar_sync_enabled
 * @var string $google_calendar_timezone
 * @var string $current_timezone
 *
 * @package YITH\Booking\Views
 */

defined( 'YITH_WCBK' ) || exit;
?>

<?php if ( $is_calendar_sync_enabled ) : ?>
	<div class='yith-wcbk-google-calendar-timezone-info'>
		<?php
		if ( $google_calendar_timezone !== $current_timezone ) {
			echo sprintf(
			// translators: 1. WordPress timezone; 2. Google calendar timezone.
				esc_html__( 'Please note: your WordPress Timezone %1$s is different by your Google Calendar Timezone %2$s', 'yith-booking-for-woocommerce' ),
				'<code>' . esc_html( $current_timezone ) . '</code>',
				'<code>' . esc_html( $google_calendar_timezone ) . '</code>'
			);
		} else {
			echo sprintf(
			// translators: %s is the timezone.
				esc_html__( 'Timezone %s', 'yith-booking-for-woocommerce' ),
				'<code>' . esc_html( $current_timezone ) . '</code>'
			);
		}
		?>
	</div>
<?php endif ?>
