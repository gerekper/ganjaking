<?php
namespace porto\portoProducts;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
use VisualComposer\Framework\Container;
use VisualComposer\Framework\Illuminate\Support\Module;
use VisualComposer\Helpers\Traits\EventsFilters;
use VisualComposer\Helpers\Traits\WpFiltersActions;
class PortoProductsController extends Container implements Module {

	use EventsFilters;
	use WpFiltersActions;
	public function __construct() {
		if ( ! defined( 'PORTO_FUNC_URL' ) ) {
			return;
		}
		if ( ! defined( 'VCV_PORTO_PRODUCTS_CONTROLLER' ) ) {
			$this->addFilter(
				'vcv:editor:variables vcv:editor:variables/portoProducts',
				'getProductLayouts'
			);
			define( 'VCV_PORTO_PRODUCTS_CONTROLLER', true );
		}

		$this->addFilter( 'vcv:autocomplete:portoProductCats:render', 'getPortoProductCats' );
		$this->addFilter( 'vcv:autocomplete:portoProductIDs:render', 'getPortoProductIDs' );
		$this->addFilter( 'vcv:ajaxForm:render:response', 'renderForm' );
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

		$attributes     = array(
			array(
				'label' => '',
				'value' => '',
			),
		);
		$attributes_tax = wc_get_attribute_taxonomies();
		foreach ( $attributes_tax as $attribute ) {
			$attributes[] = array(
				'label' => $attribute->attribute_label,
				'value' => $attribute->attribute_name,
			);
		}
		$variables[] = array(
			'key'   => 'portoCarouselAttrs',
			'value' => $attributes,
		);

		return $variables;
	}

	protected function getPortoProductCats( $response, $payload ) {
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
				$data['value']         = esc_html( $value->slug );
				$data['label']         = esc_html( $value->name );
				$response['results'][] = $data;
			}
		}
		return $response;
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

	protected function renderForm( $response, $payload ) {
		if ( 'porto:productsAttr:form' === $payload['action'] ) {
			$element = $payload['element'];
			if ( ! empty( $element['attribute'] ) ) {
				$selected = ! empty( $element['filter'] ) && ! empty( $element['filter']['filter'] ) ? $element['filter']['filter'] : array();
				if ( ! is_array( $selected ) ) {
					$selected = array( $selected );
				}
				$html  = '<div class="vcv-ui-form-checkboxes">';
				$terms = get_terms(
					array(
						'taxonomy'   => wc_attribute_taxonomy_name( $element['attribute'] ),
						'hide_empty' => false,
					)
				);
				if ( ! empty( $terms ) ) {
					foreach ( $terms as $term ) {
						$html .= '<label class="vcv-ui-form-checkbox"><input type="checkbox" name="filter" value="' . esc_attr( $term->slug ) . '" ' . checked( in_array( $term->slug, $selected ), true, false ) . '><span class="vcv-ui-form-checkbox-indicator"></span>' . esc_html( $term->name ) . '</label>';
					}
				}
				$html            .= '</div>';
				$response['html'] = $html;
			}
		}

		return $response;
	}
}
