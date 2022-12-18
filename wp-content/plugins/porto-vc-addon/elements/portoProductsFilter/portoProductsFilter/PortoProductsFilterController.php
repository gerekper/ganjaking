<?php
namespace porto\portoProductsFilter;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use VisualComposer\Framework\Container;
use VisualComposer\Framework\Illuminate\Support\Module;
use VisualComposer\Helpers\Traits\EventsFilters;
use VisualComposer\Helpers\Traits\WpFiltersActions;
class PortoProductsFilterController extends Container implements Module {

	use EventsFilters;
	use WpFiltersActions;
	public function __construct() {
		if ( ! defined( 'PORTO_FUNC_URL' ) ) {
			return;
		}
		if ( ! defined( 'VCV_PORTO_PRODUCTS_FILTER_CONTROLLER' ) ) {
			$this->addFilter(
				'vcv:editor:variables vcv:editor:variables/portoProductsFilter',
				'getProductFilters'
			);
			define( 'VCV_PORTO_PRODUCTS_FILTER_CONTROLLER', true );
		}
	}
	/**
	 * @param $variables
	 * @param $payload
	 *
	 * @return array
	 */
	protected function getProductFilters( $variables, $payload ) {
		$filter_areas         = array(
			array(
				'label' => __( 'Category', 'porto-vc-addon' ),
				'value' => 'category',
			),
			array(
				'label' => __( 'Price', 'porto-vc-addon' ),
				'value' => 'price',
			),
		);
		$attribute_taxonomies = wc_get_attribute_taxonomies();

		if ( ! empty( $attribute_taxonomies ) ) {
			foreach ( $attribute_taxonomies as $tax ) {
				if ( taxonomy_exists( wc_attribute_taxonomy_name( $tax->attribute_name ) ) ) {
					$filter_areas[] = array(
						'label' => $tax->attribute_name,
						'value' => $tax->attribute_name,
					);
				}
			}
		}
		$variables[] = array(
			'key'   => 'filterAreas',
			'value' => $filter_areas,
		);

		return $variables;
	}
}
