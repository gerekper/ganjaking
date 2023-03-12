<?php

namespace ACP\Export\Model;

/**
 * Strips all HTML from content. Also, replace <br> with a space for readability.
 */
class StrippedValue extends Value {

	public function get_value( $id ) {
		return strip_tags( str_replace( [ '<br/>', '<br>' ], ' ', parent::get_value( (int) $id ) ) );
	}

}