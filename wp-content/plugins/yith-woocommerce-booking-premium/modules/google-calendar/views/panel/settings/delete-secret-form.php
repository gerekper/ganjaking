<?php
/**
 * View for Google Calendar Delete Client Secret
 *
 * @package YITH\Booking\Views\GoogleCalendar
 */

defined( 'YITH_WCBK' ) || exit();

$url = yith_wcbk()->google_calendar_sync()->google_calendar()->get_delete_client_secret_url();
?>

<a href="<?php echo esc_url( $url ); ?>" class='yith-plugin-fw__button--delete'><?php esc_html_e( 'Delete Client Secret', 'yith-booking-for-woocommerce' ); ?></a>
