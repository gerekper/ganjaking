<?php
/**
 * Costs module init.
 *
 * @package YITH\Booking\Modules\Costs
 */

defined( 'YITH_WCBK' ) || exit;

require_once __DIR__ . '/includes/class-yith-wcbk-costs-module.php';

require_once __DIR__ . '/includes/class-yith-wcbk-costs-product-data-extension.php';
require_once __DIR__ . '/includes/class-yith-wcbk-costs-products.php';

require_once __DIR__ . '/includes/data/class-yith-wcbk-product-extra-cost.php';
require_once __DIR__ . '/includes/data/class-yith-wcbk-product-extra-cost-custom.php';

require_once __DIR__ . '/includes/functions.php';

return YITH_WCBK_Costs_Module::get_instance();
