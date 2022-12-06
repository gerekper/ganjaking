<?php

namespace ACA\ACF\Sorting;

use ACA\ACF\Column;
use LogicException;

trait SortableTrait {

	/**
	 * @var SortingModelFactory
	 */
	protected $sorting_factory;

	public function set_sorting_model_factory( SortingModelFactory $factory ) {
		$this->sorting_factory = $factory;
	}

	public function sorting() {
		if ( ! $this->sorting_factory instanceof SortingModelFactory ) {
			throw new LogicException( 'No SortingModelFactory is set outside the column class' );
		}

		if ( ! $this instanceof Column ) {
			throw new LogicException( 'Trait can only be used in a %s class', Column::class );
		}

		return $this->sorting_factory->create( $this->get_field(), $this->get_meta_key(), $this );
	}

}