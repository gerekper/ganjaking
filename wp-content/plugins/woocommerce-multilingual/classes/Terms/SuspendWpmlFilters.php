<?php

namespace WCML\Terms;

use WCML\Utilities\Suspend\Filters as SuspendFilters;
use WCML\Utilities\Suspend\Suspend;
use WPML\Collect\Support\Collection;
use function WPML\FP\spreadArgs;

class SuspendWpmlFilters implements Suspend {

	/**
	 * @var SuspendFilters $suspendFilters
	 */
	private $suspendFilters;

	/**
	 * @var null|Collection $filterArgs
	 */
	private $filterArgs;

	public function __construct( SuspendFilters $suspendFilters ) {
		$this->suspendFilters = $suspendFilters;
		$this->getTaxonomyChildrenOptionFiltersArgs()->each( spreadArgs( 'add_filter' ) );
	}

	/**
	 * @return Collection
	 */
	private function getTaxonomyChildrenOptionFiltersArgs() {
		$this->filterArgs = $this->filterArgs ?: wpml_collect( get_taxonomies() )
			->filter( 'is_taxonomy_translated' )
			->map( function( $taxonomy ) {
				return [ "pre_option_{$taxonomy}_children", self::getTaxonomyChildrenInAllLanguages( $taxonomy ), 20 ];
			} );

		return $this->filterArgs;
	}

	/**
	 * We will force to get the taxonomy children in "all" languages.
	 *
	 * @see \WPML_Term_Filters::pre_option_tax_children()
	 *
	 * @param string $taxonomy
	 *
	 * @return \Closure
	 */
	private static function getTaxonomyChildrenInAllLanguages( $taxonomy ) {
		return function() use ( $taxonomy ) {
			return get_option( "{$taxonomy}_children_all", false );
		};
	}

	/**
	 * @return void
	 */
	public function resume() {
		$this->suspendFilters->resume();
		$this->getTaxonomyChildrenOptionFiltersArgs()->each( spreadArgs( 'remove_filter' ) );
	}

	/**
	 * @param callable $function
	 *
	 * @return mixed
	 */
	public function runAndResume( callable $function ) {
		$result = $function();

		$this->resume();

		return $result;
	}
}
