<?php
namespace porto\portoCircularBar;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use VisualComposer\Framework\Container;
use VisualComposer\Framework\Illuminate\Support\Module;
use VisualComposer\Helpers\Traits\EventsFilters;
use VisualComposer\Helpers\Traits\WpFiltersActions;
class PortoCircularController extends Container implements Module {

	use EventsFilters;
	use WpFiltersActions;
	public function __construct() {
		if ( ! defined( 'PORTO_FUNC_URL' ) ) {
			return;
		}
		if ( ! defined( 'VCV_PORTO_CIRCULAR_CONTROLLER' ) ) {
			$this->wpAddAction( 'wp_enqueue_scripts', 'addScript' );
			define( 'VCV_PORTO_CIRCULAR_CONTROLLER', true );
		}
	}
	protected function addScript() {
		wp_enqueue_script( 'easypiechart' );
	}
}
