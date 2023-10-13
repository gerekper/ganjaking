<?php
/**
 * Actions in booking actions meta-box
 *
 * @var YITH_WCBK_Booking $booking        The booking.
 * @var string            $force_sync_url The URL for forcing synchronization.
 *
 * @author  YITH <plugins@yithemes.com>
 * @package YITH\Booking\Modules\GoogleCalendar
 */

defined( 'YITH_WCBK' ) || exit;

$sync_status = 'not-sync';
$date        = '';
$label       = __( 'not synchronized', 'yith-booking-for-woocommerce' );
if ( $booking->get_google_calendar_last_update() ) {
	$sync_status = 'sync';
	$date        = date_i18n( wc_date_format() . ' ' . wc_time_format(), $booking->get_google_calendar_last_update() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
	$label       = __( 'synchronized', 'yith-booking-for-woocommerce' );
}

$tip      = $label . ( $date ? '<br />' . $date : '' );
$icon_url = YITH_WCBK_ASSETS_URL . '/images/google-calendar.svg';
?>
<div class="yith-wcbk-booking-actions-metabox-google-calendar">
	<?php
	echo "<div class='yith-wcbk-google-calendar-sync-icon__container'>";
	echo '<img class="yith-wcbk-google-calendar-sync-icon" src="' . esc_attr( $icon_url ) . '" />';
	echo '<span class="yith-wcbk-google-calendar-sync-status ' . esc_attr( $sync_status ) . ' yith-icon yith-icon-update tips" data-tip="' . esc_attr( $tip ) . '"></span>';
	echo '</div>';
	echo "<div class='yith-wcbk-google-calendar-sync-force__container'>";
	echo '<a class="yith-wcbk-google-calendar-sync-force" href="' . esc_url( $force_sync_url ) . '">' . esc_html__( 'force sync', 'yith-booking-for-woocommerce' ) . '</a>';
	echo '</div>';
	?>
</div>
