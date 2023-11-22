<?php

namespace WCML\Exporter;

use WCML\Terms\SuspendWpmlFiltersFactory as SuspendTermsFilterFactory;
use WCML\Utilities\Suspend\PostsQueryFiltersFactory as SuspendPostsQueryFiltersFactory;
use WPML\API\Sanitize;
use WPML\FP\Obj;
use WPML\LIB\WP\Hooks;
use function WPML\FP\spreadArgs;
use function WPML\FP\tap;

class AllLanguagesHooks implements \IWPML_Backend_Action, \IWPML_DIC_Action {

	const KEY_EXPORT_ALL_LANGUAGES = 'wpml_export_all_languages';

	/**
	 * @var \SitePress $sitepress
	 */
	private $sitepress;

	public function __construct( \SitePress $sitepress ) {
		$this->sitepress = $sitepress;
	}

	public function add_hooks() {
		Hooks::onAction( 'woocommerce_product_export_row' )
			->then( [ $this, 'addLanguageField' ] );

		Hooks::onFilter( 'woocommerce_product_export_product_query_args' )
			->then( spreadArgs( tap( [ $this, 'suspendWpmlLanguageFilters' ] ) ) );
	}

	/**
	 * @return void
	 */
	public function addLanguageField() {
		if ( 'all' === $this->sitepress->get_current_language() ) {
			echo '<input type="hidden" name="' . self::KEY_EXPORT_ALL_LANGUAGES . '" value="1" />';
		}
	}

	/**
	 * @return void
	 */
	public function suspendWpmlLanguageFilters() {
		if ( $this->isExportingAllLanguages() ) {
			SuspendPostsQueryFiltersFactory::create();
			SuspendTermsFilterFactory::create();
		}
	}

	/**
	 * @return bool
	 */
	private function isExportingAllLanguages() {
		$formQueryString = Sanitize::string( (string) Obj::prop( 'form', $_POST ) );
		wp_parse_str( $formQueryString, $queryArgs );

		return isset( $queryArgs[ self::KEY_EXPORT_ALL_LANGUAGES ] );
	}
}
