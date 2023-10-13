<?php
/**
 * Resources module init.
 *
 * @package YITH\Booking\Modules\Resources
 */

defined( 'YITH_WCBK' ) || exit;

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/class-yith-wcbk-resources-module.php';

require_once __DIR__ . '/includes/data/class-yith-wcbk-resource.php';
require_once __DIR__ . '/includes/data/class-yith-wcbk-resource-data.php';
require_once __DIR__ . '/includes/data/class-yith-wcbk-resource-availability-handler.php';

require_once __DIR__ . '/includes/class-yith-wcbk-resource-data-store.php';
require_once __DIR__ . '/includes/class-yith-wcbk-resources-product-data-extension.php';
require_once __DIR__ . '/includes/class-yith-wcbk-resources-products.php';
require_once __DIR__ . '/includes/class-yith-wcbk-resources-booking-data-extension.php';
require_once __DIR__ . '/includes/class-yith-wcbk-resources-bookings.php';


return YITH_WCBK_Resources_Module::get_instance();
