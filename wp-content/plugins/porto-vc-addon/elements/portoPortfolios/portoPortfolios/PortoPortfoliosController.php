<?php
namespace porto\portoPortfolios;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use VisualComposer\Framework\Container;
use VisualComposer\Framework\Illuminate\Support\Module;
use VisualComposer\Helpers\Traits\EventsFilters;
use VisualComposer\Helpers\Traits\WpFiltersActions;
class PortoPortfoliosController extends Container implements Module {

	use EventsFilters;
	use WpFiltersActions;
	public function __construct() {
		if ( ! defined( 'PORTO_FUNC_URL' ) ) {
			return;
		}
		if ( ! defined( 'VCV_PORTO_PORTFOLIOS_CONTROLLER' ) ) {
			$this->addFilter(
				'vcv:editor:variables vcv:editor:variables/portoPortfolios',
				'getPortfolioValues'
			);
			define( 'VCV_PORTO_PORTFOLIOS_CONTROLLER', true );
		}
		$this->addFilter( 'vcv:autocomplete:portoPortfolioIDs:render', 'getPortfolioIDs' );
		$this->addFilter( 'vcv:autocomplete:portoPortfolioCatIDs:render', 'getPortfolioCatIDs' );

	}
	/**
	 * @param $variables
	 * @param $payload
	 *
	 * @return array
	 */
	protected function getPortfolioValues( $variables, $payload ) {
		$portfolio_layouts = array();
		foreach ( porto_sh_commons( 'portfolio_layout' ) as $value => $key ) {
			$portfolio_layouts[] = array(
				'label' => str_replace( '&amp;', '&', esc_js( $value ) ),
				'value' => esc_js( $key ),
			);
		}
		$variables[] = array(
			'key'   => 'portoPortfolioLayouts',
			'value' => $portfolio_layouts,
		);

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

	protected function getPortfolioCatIDs( $response, $payload ) {
		$search              = $payload['searchValue'];
		$cats                = get_terms(
			array(
				'taxonomy'   => 'portfolio_cat',
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
	protected function getPortfolioIDs( $response, $payload ) {
		$search_value = $payload['searchValue'];
		global $wpdb;
		$product_infos = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT ID AS id, post_title AS title
					FROM {$wpdb->posts} 
					WHERE post_type = 'portfolio' AND post_status = 'publish' AND ( ID = %d OR post_title LIKE '%%%s%%' )",
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
