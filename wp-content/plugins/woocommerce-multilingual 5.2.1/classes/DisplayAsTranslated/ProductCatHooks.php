<?php

namespace WCML\DisplayAsTranslated;

use WPML\FP\Just;
use WPML\FP\Obj;
use WPML\LIB\WP\Hooks;
use function WPML\FP\invoke;
use function WPML\FP\spreadArgs;

class ProductCatHooks implements \IWPML_Frontend_Action {

	/**
	 * @var bool $isTermsFilterLoaded
	 */
	private $isTermsFilterLoaded = false;

	const KEY_FIX_TERM_COUNT_ZERO = 'wcml_fix_term_count_zero';

	/** @var \WPML_Translation_Element_Factory $elementFactory */
	private $elementFactory;

	public function __construct( \WPML_Translation_Element_Factory $elementFactory ) {
		$this->elementFactory = $elementFactory;
	}

	public function add_hooks() {
		Hooks::onFilter( 'woocommerce_product_subcategories_args' )
			->then( spreadArgs( [ $this, 'addFixCountArg' ] ) );
	}

	/**
	 * @param array $args
	 *
	 * @return array
	 */
	public function addFixCountArg( $args ) {
		$this->loadTermsFilter();
		return (array) Obj::assoc( self::KEY_FIX_TERM_COUNT_ZERO, true, $args );
	}

	/**
	 * @return void
	 */
	private function loadTermsFilter() {
		if ( ! $this->isTermsFilterLoaded ) {
			Hooks::onFilter( 'get_terms', 10, 4 )
				->then( spreadArgs( [ $this, 'fixTermsWithZeroCount' ] ) );

			$this->isTermsFilterLoaded = true;
		}
	}

	/**
	 * @param array      $terms
	 * @param array|null $taxonomy
	 * @param array      $termQueryVars
	 *
	 * @return array
	 */
	public function fixTermsWithZeroCount( $terms, $taxonomy, $termQueryVars ) {
		if ( ! empty( $termQueryVars[ self::KEY_FIX_TERM_COUNT_ZERO ] ) ) {
			foreach ( $terms as $term ) {
				if ( ! $term->count ) {
					$term->count = $this->getSourceTermCount( $term );
				}
			}
		}

		return $terms;
	}

	/**
	 * @param \WP_Term $term
	 *
	 * @return int
	 */
	private function getSourceTermCount( $term ) {
		return (int) Just::of( $this->elementFactory->create_term( $term->term_taxonomy_id ) )
			->map( invoke( 'get_source_element' ) )
			->map( invoke( 'get_wp_object' ) )
			->map( Obj::prop( 'count' ) )
			->getOrElse( $term->count );
	}
}
