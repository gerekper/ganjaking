<?php
namespace porto\portoCompare;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use VisualComposer\Framework\Container;
use VisualComposer\Framework\Illuminate\Support\Module;
use VisualComposer\Helpers\Traits\EventsFilters;
use VisualComposer\Helpers\Traits\WpFiltersActions;
class PortoCompareController extends Container implements Module {

	use EventsFilters;
	use WpFiltersActions;
	public function __construct() {
		if ( ! defined( 'VCV_PORTO_COMPARE_CONTROLLER' ) && defined( 'YITH_WOOCOMPARE' ) && class_exists( 'YITH_Woocompare' ) ) {
			$this->addFilter(
				'vcv:editor:variables vcv:editor:variables/portoHeaderCompare',
				'getCompareCount'
			);
			define( 'VCV_PORTO_COMPARE_CONTROLLER', true );
		}

	}
	/**
	 * @param $variables
	 * @param $payload
	 *
	 * @return array
	 */
	protected function getCompareCount( $variables, $payload ) {
		global $yith_woocompare;

		$variables[] = array(
			'key'   => 'portoCompareCount',
			'value' => isset( $yith_woocompare->obj->products_list ) ? sizeof( $yith_woocompare->obj->products_list ) : 0,
		);

		return $variables;
	}

}
