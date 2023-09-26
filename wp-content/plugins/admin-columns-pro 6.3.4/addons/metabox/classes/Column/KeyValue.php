<?php

namespace ACA\MetaBox\Column;

use ACA;
use ACP\ConditionalFormat\FilteredHtmlFormatTrait;
use ACP\ConditionalFormat\Formattable;

class KeyValue extends ACA\MetaBox\Column implements Formattable {

	use FilteredHtmlFormatTrait;

	public function format_single_value( $value, $id = null ) {
		if ( empty( $value ) ) {
			return $this->get_empty_char();
		}

		return sprintf( '<strong>%s</strong>: %s', $value[0], $value[1] );
	}

}