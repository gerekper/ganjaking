<?php

namespace ACP\Filtering\Markup\Ranged;

use ACP\Filtering\Markup\Ranged;

class Date extends Ranged {

	public function __construct( $name, $label, $min, $max ) {
		parent::__construct( $name, $label, $min, $max );

		$this->get_min()->set_attribute( 'placeholder', __( 'Start date', 'codepress-admin-columns' ) );
		$this->get_max()->set_attribute( 'placeholder', __( 'End date', 'codepress-admin-columns' ) );
	}

}