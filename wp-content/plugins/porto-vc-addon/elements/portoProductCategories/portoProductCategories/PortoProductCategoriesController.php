<?php
namespace porto\portoProducts;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use VisualComposer\Framework\Container;
use VisualComposer\Framework\Illuminate\Support\Module;
use VisualComposer\Helpers\Traits\EventsFilters;
use VisualComposer\Helpers\Traits\WpFiltersActions;
class PortoProductCategoriesController extends Container implements Module {

	use EventsFilters;
	use WpFiltersActions;
	public function __construct() {
		if ( ! defined( 'PORTO_FUNC_URL' ) ) {
			return;
		}
		if ( ! defined( 'VCV_PORTO_PRODUCT_CATS_CONTROLLER' ) ) {
			$this->addFilter(
				'vcv:editor:variables vcv:editor:variables/portoProductCategories',
				'getProductCategoriesValues'
			);
			define( 'VCV_PORTO_PRODUCT_CATS_CONTROLLER', true );
		}

		$this->addFilter( 'vcv:autocomplete:portoProductCatIDs:render', 'getPortoProductCatIDs' );

	}
	/**
	 * @param $variables
	 * @param $payload
	 *
	 * @return array
	 */
	protected function getProductCategoriesValues( $variables, $payload ) {
		$image_sizes = array();
		foreach ( porto_sh_commons( 'image_sizes' ) as $value => $key ) {
			$image_sizes[] = array(
				'label' => str_replace( '&amp;', '&', esc_js( $value ) ),
				'value' => esc_js( $key ),
			);
		}
		$variables[] = array(
			'key'   => 'portoImageSizes',
			'value' => $image_sizes,
		);

		$nav_types = array();
		foreach ( porto_sh_commons( 'carousel_nav_types' ) as $value => $key ) {
			$nav_types[] = array(
				'label' => str_replace( '&amp;', '&', esc_js( $value ) ),
				'value' => esc_js( $key ),
			);
		}
		$variables[] = array(
			'key'   => 'portoCarouselNavTypes',
			'value' => $nav_types,
		);

		return $variables;
	}

	protected function getPortoProductCatIDs( $response, $payload ) {
		$search              = $payload['searchValue'];
		$cats                = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => false,
				'search'     => sanitize_text_field( $search ),
			)
		);
		$response['results'] = [];
		if ( is_array( $cats ) && ! empty( $cats ) ) {
			foreach ( $cats as $value ) {
				$data                  = [];
				$data['value']         = esc_html( $value->term_id );
				$data['label']         = esc_html( $value->name );
				$response['results'][] = $data;
			}
		}
		return $response;
	}
}
