<?php

namespace ACP\Search\Query\Bindings;

class Media extends Post {

	/**
	 * @var array
	 */
	protected $mime_types = [];

	/**
	 * @return array
	 */
	public function get_mime_types() {
		return $this->mime_types;
	}

	/**
	 * @param array $mime_types
	 *
	 * @return $this
	 */
	public function mime_types( $mime_types ) {
		$this->mime_types = (array) $mime_types;

		return $this;
	}

}