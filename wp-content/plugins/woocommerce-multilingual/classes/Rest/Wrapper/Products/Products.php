<?php

namespace WCML\Rest\Wrapper\Products;

use WCML\Rest\Exceptions\Generic;
use WCML\Rest\Exceptions\InvalidLanguage;
use WCML\Rest\Exceptions\InvalidProduct;
use WCML\Rest\ProductSaveActions;
use WCML\Rest\Wrapper\Handler;
use WCML\Utilities\Suspend\PostsQueryFiltersFactory as SuspendPostsQueryFiltersFactory;
use WPML\FP\Fns;
use WPML\FP\Obj;

class Products extends Handler {

	/** @var \SitePress */
	private $sitepress;
	/** @var \WPML_Post_Translation */
	private $wpmlPostTranslations;
	/** @var ProductSaveActions $productSaveActions */
	private $productSaveActions;
	/** @var \WCML_WC_Strings $strings */
	private $strings;

	public function __construct(
		\SitePress $sitepress,
		\WPML_Post_Translation $wpmlPostTranslations,
		ProductSaveActions $productSaveActions,
		\WCML_WC_Strings $strings
	) {
		$this->sitepress            = $sitepress;
		$this->wpmlPostTranslations = $wpmlPostTranslations;
		$this->productSaveActions   = $productSaveActions;
		$this->strings              = $strings;
	}

	/**
	 * @param array $args
	 * @param \WP_REST_Request $request Request object.
	 *
	 * @return array
	 */
	public function query( $args, $request ) {
		$data = $request->get_params();
		if ( isset( $data['lang'] ) && $data['lang'] === 'all' ) {
			SuspendPostsQueryFiltersFactory::create();
		}

		return $args;
	}


	/**
	 * Appends the language and translation information to the get_product response
	 *
	 * @param \WP_REST_Response $response
	 * @param object $object
	 * @param \WP_REST_Request $request
	 *
	 * @return \WP_REST_Response
	 */
	public function prepare( $response, $object, $request ) {
		$response->data['translations'] = [];

		$langCode = Obj::prop( 'lang', $request->get_params() );

		if ( array_key_exists( 'attributes', $response->data ) ) {
			foreach ( $response->data['attributes'] as &$attribute ) {
				$attribute['name'] = $this->strings->get_translated_string_by_name_and_context( \WCML_WC_Strings::DOMAIN_WORDPRESS, \WCML_WC_Strings::TAXONOMY_SINGULAR_NAME_PREFIX . $attribute['name'], $langCode, $attribute['name'] );
			}
		}

		$trid = $this->wpmlPostTranslations->get_element_trid( $response->data['id'] );

		if ( $trid ) {
			$translations = $this->wpmlPostTranslations->get_element_translations( $response->data['id'], $trid );
			foreach ( $translations as $translation ) {
				$response->data['translations'][ $this->wpmlPostTranslations->get_element_lang_code( $translation ) ] = $translation;
			}
			$response->data['lang'] = $this->wpmlPostTranslations->get_element_lang_code( $response->data['id'] );
		}

		return $response;
	}


	/**
	 * Sets the product information according to the provided language
	 *
	 * @param object|\WC_Abstract_Legacy_Product $object
	 * @param \WP_REST_Request                   $request
	 * @param bool                               $creating
	 *
	 * @throws InvalidLanguage
	 * @throws InvalidProduct
	 * @throws Generic
	 *
	 */
	public function insert( $object, $request, $creating ) {
		$getParam = Obj::prop( Fns::__, $request->get_params() );

		$langCode       = $getParam( 'lang' );
		$translationOf  = $getParam( 'translation_of' );
		$trid           = null;
		$sourceLangCode = null;

		if ( $langCode ) {

			if ( ! $this->sitepress->is_active_language( $langCode ) ) {
				throw new InvalidLanguage( $langCode );
			}

			if ( $translationOf ) {
				$trid = $this->wpmlPostTranslations->get_element_trid( $translationOf );

				if ( ! $trid ) {
					throw new InvalidProduct( $translationOf );
				}
			}

		} elseif ( $translationOf ) {
			throw new Generic( __( 'Using "translation_of" requires providing a "lang" parameter too', 'woocommerce-multilingual' ) );
		}

		$this->productSaveActions->run( $object, $trid, $langCode, $translationOf );
	}
}
