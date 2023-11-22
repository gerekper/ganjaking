<?php

namespace WCML\Rest\Wrapper;

use WCML_WC_Strings;
use WPML\FP\Obj;

class ProductAttributes extends Handler {

	/** @var WCML_WC_Strings $strings */
	private $strings;

	public function __construct(
		WCML_WC_Strings $strings
	) {
		$this->strings = $strings;
	}

	/**
	 * Translates attribute name in woocommerce_rest_prepare_product_attribute filter
	 *
	 * @param \WP_REST_Response $response
	 * @param object|\WP_Term   $object
	 * @param \WP_REST_Request  $request
	 *
	 * @return \WP_REST_Response
	 */
	public function prepare( $response, $object, $request ) {
		$langCode = Obj::prop( 'lang', $request->get_params() );

		$response->data['name'] = $this->strings->get_translated_string_by_name_and_context( \WCML_WC_Strings::DOMAIN_WORDPRESS, \WCML_WC_Strings::TAXONOMY_SINGULAR_NAME_PREFIX . $object->attribute_label, $langCode, $object->attribute_label );

		return $response;
	}

}
