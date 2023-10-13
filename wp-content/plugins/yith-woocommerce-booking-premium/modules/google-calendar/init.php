<?php
/**
 * Google Calendar module init.
 *
 * @package YITH\Booking\Modules\GoogleCalendar
 */

defined( 'YITH_WCBK' ) || exit;

require_once __DIR__ . '/includes/class-yith-wcbk-google-calendar-module.php';

require_once __DIR__ . '/includes/class-yith-wcbk-google-calendar-booking-data-extension.php';
require_once __DIR__ . '/includes/class-yith-wcbk-google-calendar-bookings.php';

require_once __DIR__ . '/includes/class-yith-wcbk-google-calendar-sync.php';
require_once __DIR__ . '/includes/class-yith-wcbk-google-calendar.php';

return YITH_WCBK_Google_Calendar_Module::get_instance();
