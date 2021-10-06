<?php

namespace ACP\Editing\View;

use ACP\Editing\View;

class Select extends View {

	use OptionsTrait;

	public function __construct( array $options = [] ) {
		parent::__construct( 'select' );

		$this->set_options( $options );
	}

}