<?php

namespace ACP\Editing\View;

use ACP\Editing\View;

class AdvancedSelect extends View {

	use OptionsTrait,
		MethodTrait,
		MultipleTrait;

	public function __construct( array $options = [] ) {
		parent::__construct( 'select2_dropdown' );

		$this->set_options( $options );
	}

}