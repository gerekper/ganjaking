<?php
/**
 * Premium module init.
 *
 * @package YITH\Booking\Modules\Premium
 */

defined( 'YITH_WCBK' ) || exit;

require_once __DIR__ . '/includes/class-yith-wcbk-premium-module.php';

// Extensible and core classes.
require_once __DIR__ . '/includes/class-yith-wcbk-admin-premium.php';
require_once __DIR__ . '/includes/class-yith-wcbk-frontend-premium.php';
require_once __DIR__ . '/includes/class-yith-wcbk-cart-premium.php';
require_once __DIR__ . '/includes/class-yith-wcbk-orders-premium.php';
require_once __DIR__ . '/includes/class-yith-wcbk-emails-premium.php';
require_once __DIR__ . '/includes/class-yith-wcbk-cron.php';
require_once __DIR__ . '/includes/class-yith-wcbk-cart-checkout-blocks.php';

// Premium extensions.
require_once __DIR__ . '/includes/class-yith-wcbk-premium-products.php';

require_once __DIR__ . '/includes/functions.php';

return YITH_WCBK_Premium_Module::get_instance();
