<?php

namespace ACP\Editing\View;

use ACP\Editing\View;

class ComputedNumber extends View {

	use MinMaxTrait,
		StepTrait;

	public function __construct() {
		parent::__construct( 'number_extended' );

		$this->set_step( 'any' )
		     ->set_revisioning( false );
	}

}