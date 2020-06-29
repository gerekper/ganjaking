<?php

namespace ACP\Search\Query\Bindings;

use ACP\Search\Query\Bindings;

class Comment extends Bindings {

	/**
	 * @var int
	 */
	protected $parent;

	/**
	 * @return int
	 */
	public function get_parent() {
		return $this->parent;
	}

	/**
	 * @param int $id
	 *
	 * @return $this
	 */
	public function parent( $id ) {
		$this->parent = absint( $id );

		return $this;
	}

}