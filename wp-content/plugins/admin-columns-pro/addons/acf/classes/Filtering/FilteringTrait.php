<?php

namespace ACA\ACF\Filtering;

use ACA\ACF\Column;
use LogicException;

trait FilteringTrait {

	/**
	 * @var FilteringModelFactory
	 */
	protected $filtering_factory;

	public function set_filtering_model_factory( FilteringModelFactory $factory ) {
		$this->filtering_factory = $factory;
	}

	public function filtering() {
		if ( ! $this->filtering_factory instanceof FilteringModelFactory ) {
			throw new LogicException( 'No FilteringModelFactory is set outside the column class' );
		}

		if ( ! $this instanceof Column ) {
			throw new LogicException( 'Trait can only be used in a %s class', Column::class );
		}

		return $this->filtering_factory->create( $this->get_field(), $this );
	}

}