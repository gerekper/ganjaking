<?php

namespace ACP\Bookmark\Type;

use InvalidArgumentException;

final class SegmentId {

	/**
	 * @var int
	 */
	private $identity;

	public function __construct( $identity ) {
		$this->identity = $identity;

		$this->validate();
	}

	private function validate() {
		if ( ! is_int( $this->identity ) ) {
			throw new InvalidArgumentException( 'Expected integer for identity.' );
		}
	}

	/**
	 * @return int
	 */
	public function get_id() {
		return $this->identity;
	}

}