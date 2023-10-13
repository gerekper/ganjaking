<?php
/**
 * Google Maps module init.
 *
 * @package YITH\Booking\Modules\GoogleMaps
 */

defined( 'YITH_WCBK' ) || exit;

require_once __DIR__ . '/includes/class-yith-wcbk-google-maps-module.php';
require_once __DIR__ . '/includes/class-yith-wcbk-google-maps-product-data-extension.php';
require_once __DIR__ . '/includes/class-yith-wcbk-google-maps-shortcodes.php';
require_once __DIR__ . '/includes/class-yith-wcbk-maps.php';

return YITH_WCBK_Google_Maps_Module::get_instance();
