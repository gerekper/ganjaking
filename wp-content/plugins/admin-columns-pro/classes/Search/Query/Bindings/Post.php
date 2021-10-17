<?php

namespace ACP\Search\Query\Bindings;

use ACP\Search\Query\Bindings;

class Post extends Bindings {

	/**
	 * @var array
	 */
	protected $tax_query = [];

	/**
	 * @return array
	 */
	public function get_tax_query() {
		return $this->tax_query;
	}

	/**
	 * @param array $args
	 *
	 * @return $this
	 */
	public function tax_query( array $args ) {
		$this->tax_query = $args;

		return $this;
	}

}