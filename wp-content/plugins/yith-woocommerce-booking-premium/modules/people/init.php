<?php
/**
 * People module init.
 *
 * @package YITH\Booking\Modules\People
 */

defined( 'YITH_WCBK' ) || exit;

require_once __DIR__ . '/includes/class-yith-wcbk-people-module.php';

require_once __DIR__ . '/includes/class-yith-wcbk-people-product-data-extension.php';
require_once __DIR__ . '/includes/class-yith-wcbk-people-products.php';
require_once __DIR__ . '/includes/class-yith-wcbk-people-booking-data-extension.php';
require_once __DIR__ . '/includes/class-yith-wcbk-people-bookings.php';
require_once __DIR__ . '/includes/class-yith-wcbk-person-type-helper.php';

return YITH_WCBK_People_Module::get_instance();
