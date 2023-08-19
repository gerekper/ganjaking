<?php

namespace ACP\Editing\View;

use ACP\Editing\View;

class Number extends View {

	use MinMaxTrait,
		StepTrait;

	public function __construct() {
		parent::__construct( 'number' );

		$this->set( 'range_step', 'any' );
	}

}