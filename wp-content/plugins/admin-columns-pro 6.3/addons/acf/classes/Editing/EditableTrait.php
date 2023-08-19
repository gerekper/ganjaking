<?php

namespace ACA\ACF\Editing;

use ACA\ACF\Column;
use LogicException;

trait EditableTrait {

	/**
	 * @var EditingModelFactory
	 */
	protected $editing_factory;

	public function set_editing_model_factory( EditingModelFactory $factory ) {
		$this->editing_factory = $factory;
	}

	public function editing() {
		if ( ! $this->editing_factory instanceof EditingModelFactory ) {
			throw new LogicException( 'No valid EditingModelFactory set' );
		}

		if ( ! $this instanceof Column ) {
			throw new LogicException( 'Trait can only be used in a %s class', Column::class );
		}

		return $this->editing_factory->create( $this->get_field(), $this );
	}

}