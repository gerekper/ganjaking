<?php

namespace WCML\Block\Convert;

use IWPML_DIC_Action;
use IWPML_Frontend_Action;
use WPML\Core\ISitePress;
use WCML\StandAlone\NullSitePress;
use SitePress;
use WCML\Rest\Frontend\Language;
use WPML\FP\Just;
use WPML\FP\Str;

class Hooks implements IWPML_Frontend_Action, IWPML_DIC_Action {

	/** @var SitePress|NullSitePress $sitepress */
	private $sitepress;

	/** @var Language $frontendRestLang */
	private $frontendRestLang;

	/**
	 * @param SitePress|NullSitePress $sitepress
	 * @param Language                $frontendRestLang
	 */
	public function __construct( ISitePress $sitepress, Language $frontendRestLang ) {
		$this->sitepress        = $sitepress;
		$this->frontendRestLang = $frontendRestLang;
	}

	public function add_hooks() {
		add_filter( 'render_block_data', [ $this, 'filterIdsInBlock' ] );
		add_filter( 'render_block_woocommerce/product-search', [ $this, 'filterProductSearchForm' ] );
		add_action( 'parse_query', [ $this, 'addCurrentLangToQueryVars' ] );

		if ( ! ( is_admin() || \WPML_URL_HTTP_Referer::is_post_edit_page() ) ) {
			add_filter( 'rest_request_before_callbacks', [ $this, 'useLanguageFrontendRestLang' ], 10, 3 );
		}
	}

	public function filterIdsInBlock( array $block ) {
		return ConverterProvider::get( $block['blockName'] )->convert( $block );
	}

	/**
	 * WC is caching query results in transients which name
	 * is based on the query vars hash.
	 *
	 * @param \WP_Query $query
	 */
	public function addCurrentLangToQueryVars( $query ) {
		if ( $query instanceof \Automattic\WooCommerce\Blocks\Utils\BlocksWpQuery ) {
			$query->query_vars['wpml_language'] = $this->sitepress->get_current_language();
		}
	}

	/**
	 * @param \WP_HTTP_Response|\WP_Error $response
	 * @param array                       $handler
	 * @param \WP_REST_Request            $request
	 *
	 * @return \WP_HTTP_Response|\WP_Error
	 */
	public function useLanguageFrontendRestLang( $response, $handler, $request ) {
		if ( $this->isWcRestRequest( $request ) ) {
			$lang = $this->frontendRestLang->get();

			if ( $lang ) {
				$this->sitepress->switch_lang( $lang );
			}
		}

		return $response;
	}

	/**
	 * @param \WP_REST_Request $request
	 *
	 * @return bool
	 */
	private function isWcRestRequest( \WP_REST_Request $request ) {
		return strpos( $request->get_route(), '/wc/blocks/' ) === 0
			|| strpos( $request->get_route(), '/wc/store/' ) === 0;
	}

	/**
	 * @param string $blockContent
	 *
	 * @return string
	 */
	public function filterProductSearchForm( $blockContent ) {
		// $replaceActionUrl :: string -> string
		$replaceActionUrl = Str::pregReplace( '/(<form[^>]*action=")([^"]*)"/', '$1' . home_url( '/' ) . '"' );

		// $addLanguageHiddenField :: string -> string
		$addLanguageHiddenField = [ $this->sitepress, 'get_search_form_filter' ];

		return Just::of( $blockContent )
			->map( $replaceActionUrl )
			->map( $addLanguageHiddenField )
			->get();
	}
}
