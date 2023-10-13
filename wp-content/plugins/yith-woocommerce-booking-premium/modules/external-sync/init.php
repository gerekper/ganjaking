<?php
/**
 * External Sync module init.
 *
 * @package YITH\Booking\Modules\ExternalSync
 */

defined( 'YITH_WCBK' ) || exit;

require_once __DIR__ . '/includes/class-yith-wcbk-external-sync-module.php';

require_once __DIR__ . '/includes/class-yith-wcbk-external-sync-product-data-extension.php';
require_once __DIR__ . '/includes/class-yith-wcbk-external-sync-products.php';
require_once __DIR__ . '/includes/class-yith-wcbk-external-sync-bookings.php';

require_once __DIR__ . '/includes/data/class-yith-wcbk-booking-external.php';

require_once __DIR__ . '/includes/class-yith-wcbk-booking-externals.php';
require_once __DIR__ . '/includes/class-yith-wcbk-booking-external-sources.php';
require_once __DIR__ . '/includes/class-yith-wcbk-ics-parser.php';

require_once __DIR__ . '/includes/functions.php';

return YITH_WCBK_External_Sync_Module::get_instance();
