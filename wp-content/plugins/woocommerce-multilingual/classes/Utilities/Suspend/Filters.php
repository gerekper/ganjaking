<?php

namespace WCML\Utilities\Suspend;

use WPML\FP\Logic;
use WPML\FP\Relation;
use function WPML\FP\pipe;
use function WPML\FP\spreadArgs;

class Filters implements Suspend {

	/** @var \WPML\Collect\Support\Collection $suspended */
	private $suspended;

	public function __construct( array $filtersToSuspend ) {
		$this->suspended = wpml_collect( $filtersToSuspend )->filter( spreadArgs( 'remove_filter' ) );
	}

	/**
	 * @return void
	 */
	public function resume() {
		$this->suspended
			// Make sure the filter has not been already restored.
			->filter( pipe( spreadArgs( 'has_filter' ), Relation::equals( false ), Logic::not() ) )
			->each( spreadArgs( 'add_filter' ) );
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
