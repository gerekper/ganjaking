<?php

namespace ACA\JetEngine\Value\Format;

use ACA\JetEngine\Column\Meta;
use ACA\JetEngine\Field;

class DateTime extends Date {

	public function __construct( Meta $column, Field\Field $field ) {
		parent::__construct( $column, $field, 'Y-m-d\TH:i' );
	}

}