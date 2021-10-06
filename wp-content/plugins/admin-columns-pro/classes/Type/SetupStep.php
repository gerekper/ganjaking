<?php

namespace ACP\Type;

class SetupStep {

	/**
	 * @var string
	 */
	private $step;

	public function __construct( $step ) {
		$this->step = (string) $step;
	}

	/**
	 * @return string
	 */
	public function get_value() {
		return $this->step;
	}

}