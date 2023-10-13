<?php

namespace WCML\Terms\Count;

use WCML\Terms\SuspendWpmlFiltersFactory;
use WPML\FP\Fns;
use WPML\FP\Obj;
use WPML\LIB\WP\Hooks as WpHooks;
use function WPML\FP\spreadArgs;

class Hooks implements \IWPML_Backend_Action, \IWPML_REST_Action {

	/**
	 * @return void
	 */
	public function add_hooks() {
		WpHooks::onFilter( 'woocommerce_product_recount_terms', PHP_INT_MAX )
			->then( spreadArgs( [ self::class, 'disableTermFilters' ] ) );

		WpHooks::onAction( 'icl_save_term_translation', 10, 2 )
			->then( spreadArgs( [ self::class, 'recountOnSaveTermTranslation' ] ) );

		WpHooks::onAction( 'wpml_sync_term_hierarchy_done' )
			->then( [ self::class, 'recountAllTermsInShutdown' ] );
	}

	/**
	 * At the top of `_wc_term_recount` we suspend all WPML term filters,
	 * and we resume it at the bottom once `wc_term_counts` transient
	 * is deleted.
	 *
	 * @see _wc_term_recount()
	 *
	 * @param bool $shouldRecountTerms
	 *
	 * @return bool
	 */
	public static function disableTermFilters( $shouldRecountTerms ) {
		if ( $shouldRecountTerms ) {
			$filtersSuspend = SuspendWpmlFiltersFactory::create();

			add_action( 'delete_transient_wc_term_counts', function() use ( $filtersSuspend ) {
				$filtersSuspend->resume();
			} );

		}

		return $shouldRecountTerms;
	}

	/**
	 * @param \stdClass $originalTax
	 * @param int       $translatedTerm
	 *
	 * @return void
	 */
	public static function recountOnSaveTermTranslation( $originalTax, $translatedTerm ) {
		$taxonomyName = Obj::prop( 'taxonomy', $originalTax );

		if ( in_array( $taxonomyName, [ 'product_cat', 'product_tag' ], true ) ) {
			SuspendWpmlFiltersFactory::create()->runAndResume( function () use ( $translatedTerm, $taxonomyName ) {
				_wc_term_recount( [ (int) Obj::prop( 'term_taxonomy_id', $translatedTerm ) ], get_taxonomy( $taxonomyName ) );
			} );
		}
	}

	/**
	 * @return void
	 */
	public static function recountAllTermsInShutdown() {
		WpHooks::onAction( 'shutdown' )->then( Fns::once( [ self::class, 'recountAllTerms' ] ) );
	}

	/**
	 * @return void
	 */
	public static function recountAllTerms() {
		SuspendWpmlFiltersFactory::create()->runAndResume( function() {
			wc_recount_all_terms();
		} );
	}
}
