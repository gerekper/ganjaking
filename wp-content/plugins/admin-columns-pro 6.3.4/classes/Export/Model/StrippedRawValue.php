<?php

namespace ACP\Export\Model;

/**
 * Exportability model for outputting the column's raw value, but with stripped HTML tags
 */
class StrippedRawValue extends RawValue {

	public function get_value( $id ) {
		return strip_tags( parent::get_value( $id ) );
	}

}