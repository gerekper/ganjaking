<?php

namespace ACP\Editing\View;

use ACP\Editing\View;

class MultiInput extends View {

	public function __construct() {
		parent::__construct( 'multi_input' );
	}

	public function set_sub_type( $sub_type ) {
		$this->set( 'subtype', (string) $sub_type );

		return $this;
	}

}