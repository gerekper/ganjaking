<?php

namespace WCML\Rest\Wrapper\Reports;

use WCML\Rest\Exceptions\InvalidLanguage;
use WCML\Rest\Wrapper\Handler;
use WPML\FP\Obj;

class TopSeller extends Handler {

	/** @var \SitePress */
	private $sitepress;

	public function __construct( \SitePress $sitepress ) {
		$this->sitepress = $sitepress;
	}

	/**
	 * Check the language information for top sellers response.
	 *
	 * @param \WP_REST_Response $response
	 * @param object            $object
	 * @param \WP_REST_Request  $request
	 *
	 * @throws InvalidLanguage
	 *
	 * @return \WP_REST_Response|false
	 */
	public function prepare( $response, $object, $request ) {

		$language = Obj::prop( 'lang', $request->get_params() );

		if ( $language ) {

			if ( ! $this->sitepress->is_active_language( $language ) ) {
				throw new InvalidLanguage( $language );
			}

			$response->data['lang'] = $language;

			$product_lang = $this->sitepress->get_language_for_element( $object->product_id, 'post_' . get_post_type( $object->product_id ) );

			if( $product_lang !== $language ){
				return false;
			}
		}

		return $response;
	}

}