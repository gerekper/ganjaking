<?php
namespace porto\portoHotspot;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use VisualComposer\Framework\Container;
use VisualComposer\Framework\Illuminate\Support\Module;
use VisualComposer\Helpers\Traits\EventsFilters;
use VisualComposer\Helpers\Traits\WpFiltersActions;
class PortoHotspotController extends Container implements Module {

	use EventsFilters;
	use WpFiltersActions;
	public function __construct() {
		if ( ! defined( 'PORTO_FUNC_URL' ) ) {
			return;
		}
		if ( ! defined( 'VCV_PORTO_HOTSPOT_CONTROLLER' ) ) {
			$this->addFilter(
				'vcv:editor:variables vcv:editor:variables/portoHotspot',
				'getProductLayouts'
			);
			define( 'VCV_PORTO_HOTSPOT_CONTROLLER', true );
		}
		$this->addFilter( 'vcv:autocomplete:portoProductIDs:render', 'getPortoProductIDs' );
	}

	/**
	 * @param $variables
	 * @param $payload
	 *
	 * @return array
	 */
	protected function getProductLayouts( $variables, $payload ) {
		$product_layouts = array();
		foreach ( porto_sh_commons( 'products_addlinks_pos' ) as $value => $key ) {
			$product_layouts[] = array(
				'label' => str_replace( '&amp;', '&', esc_js( $value ) ),
				'value' => esc_js( $key ),
			);
		}
		$variables[] = array(
			'key'   => 'portoProductLayouts',
			'value' => $product_layouts,
		);

		return $variables;
	}

	protected function getPortoProductIDs( $response, $payload ) {
		$search_value = $payload['searchValue'];
		global $wpdb;
		$product_infos = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ID AS id, post_title AS title
					FROM {$wpdb->posts} 
					WHERE post_type = 'product' AND post_status = 'publish' AND ( ID = %d OR post_title LIKE '%%%s%%' )",
				(int) $search_value > 0 ? (int) $search_value : -1,
				$wpdb->esc_like( stripslashes( $search_value ) )
			),
			ARRAY_A
		);

		$response['results'] = [];
		if ( is_array( $product_infos ) && ! empty( $product_infos ) ) {
			foreach ( $product_infos as $value ) {
				$data                  = [];
				$data['value']         = (int) $value['id'];
				$data['label']         = esc_html( $value['title'] );
				$response['results'][] = $data;
			}
		}
		return $response;
	}
}
