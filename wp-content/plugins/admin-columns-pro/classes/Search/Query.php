<?php

namespace ACP\Search;

use AC\Registrable;
use ACP\Search\Query\Bindings;
use LogicException;

abstract class Query implements Registrable {

	/**
	 * @var Bindings[]
	 */
	protected $bindings;

	/**
	 * @param Bindings[] $bindings
	 */
	public function __construct( array $bindings ) {
		$this->bindings = $bindings;

		$this->validate_bindings();
	}

	/**
	 * @throws LogicException
	 */
	private function validate_bindings() {
		foreach ( $this->bindings as $bindings ) {
			if ( ! $bindings instanceof Bindings ) {
				throw new LogicException( 'Expected Bindings object.' );
			}
		}
	}

	/**
	 * @return array
	 */
	protected function get_meta_query() {
		$meta_query = [];

		foreach ( $this->bindings as $binding ) {
			$meta_query[] = $binding->get_meta_query();
		}

		$meta_query = array_filter( $meta_query );

		if ( $meta_query ) {
			$meta_query['relation'] = 'AND';
		}

		return $meta_query;
	}

}