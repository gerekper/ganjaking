<?php

namespace ACP\Editing\View;

use ACP\Editing\View;

class Legacy extends View {

	public function __construct( array $args ) {
		parent::__construct( $args['type'] );

		$this->args = $args;
	}

}