<?php
/**
 * Services module init.
 *
 * @package YITH\Booking\Modules\Services
 */

defined( 'YITH_WCBK' ) || exit;

require_once __DIR__ . '/includes/class-yith-wcbk-services-module.php';

require_once __DIR__ . '/includes/data/class-yith-wcbk-service.php';

require_once __DIR__ . '/includes/class-yith-wcbk-service-data-store.php';
require_once __DIR__ . '/includes/class-yith-wcbk-service-tax-admin.php';
require_once __DIR__ . '/includes/class-yith-wcbk-services-products.php';
require_once __DIR__ . '/includes/class-yith-wcbk-services-product-data-extension.php';
require_once __DIR__ . '/includes/class-yith-wcbk-services-booking-data-extension.php';
require_once __DIR__ . '/includes/class-yith-wcbk-services-bookings.php';
require_once __DIR__ . '/includes/class-yith-wcbk-services-shortcodes.php';

require_once __DIR__ . '/includes/functions.php';

return YITH_WCBK_Services_Module::get_instance();
