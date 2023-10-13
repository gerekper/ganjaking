<?php
/**
 * Search Forms module init.
 *
 * @package YITH\Booking\Modules\SearchForms
 */

defined( 'YITH_WCBK' ) || exit;

require_once __DIR__ . '/includes/class-yith-wcbk-search-forms-module.php';

require_once __DIR__ . '/includes/data/class-yith-wcbk-search-form.php';

require_once __DIR__ . '/includes/class-yith-wcbk-search-form-data-store.php';
require_once __DIR__ . '/includes/class-yith-wcbk-search-forms-frontend.php';
require_once __DIR__ . '/includes/class-yith-wcbk-search-forms-ajax.php';
require_once __DIR__ . '/includes/class-yith-wcbk-search-forms-shortcodes.php';

require_once __DIR__ . '/includes/class-yith-wcbk-search-form-widget.php';

require_once __DIR__ . '/includes/functions.php';

return YITH_WCBK_Search_Forms_Module::get_instance();
