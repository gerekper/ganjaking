<?php

namespace ACA\ACF\Search;

use ACA\ACF\Column;
use LogicException;

trait SearchableTrait {

	/**
	 * @var SearchComparisonFactory
	 */
	protected $search_factory;

	public function set_search_comparison_factory( SearchComparisonFactory $factory ) {
		$this->search_factory = $factory;
	}

	public function search() {
		if ( ! $this->search_factory instanceof SearchComparisonFactory ) {
			throw new LogicException( 'No comparison factory set' );
		}

		if ( ! $this instanceof Column ) {
			throw new LogicException( sprintf( 'Trait can only be used in a %s class', Column::class ) );
		}

		return $this->search_factory->create( $this->get_field(), $this->get_meta_key(), $this->get_meta_type() );
	}

}