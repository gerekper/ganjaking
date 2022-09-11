<?php

namespace WPML\TaxonomyTermTranslation;

use WPML\FP\Relation;
use WPML\LIB\WP\Hooks as WpHooks;
use function WPML\FP\spreadArgs;

class Hooks implements \IWPML_Backend_Action, \IWPML_Frontend_Action {

	const KEY_SKIP_FILTERS = 'wpml_skip_filters';

	public function add_hooks() {
		WpHooks::onFilter( 'term_exists_default_query_args' )
			->then( spreadArgs( function( $args ) {
				return array_merge(
					$args,
					[
						'cache_domain'         => microtime(), // Prevent caching of the query
						self::KEY_SKIP_FILTERS => true,
					]
				);
			} ) );
	}

	/**
	 * @param array $args
	 *
	 * @return bool
	 */
	public static function shouldSkip( $args ) {
		return (bool) Relation::propEq( self::KEY_SKIP_FILTERS, true, (array) $args );
	}
}