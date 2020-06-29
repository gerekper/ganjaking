<?php

namespace ACP\Filtering\Markup\Ranged;

use ACP\Filtering\Markup\Ranged;

class Number extends Ranged {

	public function __construct( $name, $label, $min, $max ) {
		parent::__construct( $name, $label, $min, $max );

		$this->set_input_type( 'number' );
		$this->get_min()->set_attribute( 'placeholder', __( 'Min', 'codepress-admin-columns' ) );
		$this->get_max()->set_attribute( 'placeholder', __( 'Max', 'codepress-admin-columns' ) );
	}

}