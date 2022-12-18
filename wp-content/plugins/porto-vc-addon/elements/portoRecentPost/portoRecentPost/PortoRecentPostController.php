<?php
namespace porto\portoRecentPost;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use VisualComposer\Framework\Container;
use VisualComposer\Framework\Illuminate\Support\Module;
use VisualComposer\Helpers\Traits\EventsFilters;
use VisualComposer\Helpers\Traits\WpFiltersActions;
class PortoRecentPostController extends Container implements Module {

	use EventsFilters;
	use WpFiltersActions;
	public function __construct() {
		if ( ! defined( 'PORTO_FUNC_URL' ) ) {
			return;
		}
		if ( ! defined( 'VCV_PORTO_RECENT_POST_CONTROLLER' ) ) {
			$this->addFilter(
				'vcv:editor:variables vcv:editor:variables/portoRecentPost',
				'getImageSize'
			);
			define( 'VCV_PORTO_RECENT_POST_CONTROLLER', true );
		}
		$this->addFilter( 'vcv:autocomplete:portoBlogCatIDs:render', 'getBlogCatIDs' );

	}
	/**
	 * @param $variables
	 * @param $payload
	 *
	 * @return array
	 */
	protected function getImageSize( $variables, $payload ) {
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

		return $variables;
	}

	protected function getBlogCatIDs( $response, $payload ) {
		$search              = $payload['searchValue'];
		$cats                = get_terms(
			array(
				'taxonomy'   => 'category',
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
