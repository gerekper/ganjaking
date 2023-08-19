<?php

namespace ACA\MetaBox\Column;

use ACA;
use ACP\ConditionalFormat\ConditionalFormatTrait;
use ACP\ConditionalFormat\Formattable;

class ButtonGroup extends ACA\MetaBox\Column implements Formattable {

	use ConditionalFormatTrait;

	public function format_single_value( $value, $id = null ) {
		return is_array( $value )
			? implode( ', ', $value )
			: $value;
	}

}