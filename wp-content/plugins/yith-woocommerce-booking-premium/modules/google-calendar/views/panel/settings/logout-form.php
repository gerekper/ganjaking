<?php
/**
 * View for Google Calendar Logout Form
 *
 * @package YITH\Booking\Views\GoogleCalendar
 */

defined( 'YITH_WCBK' ) || exit();

$url = yith_wcbk()->google_calendar_sync()->google_calendar()->get_logout_url();
?>

<a href="<?php echo esc_url( $url ); ?>" class='yith-plugin-fw__button--secondary'><?php esc_html_e( 'Logout', 'yith-booking-for-woocommerce' ); ?></a>
