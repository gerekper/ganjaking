<?php

namespace ACP\Export\Model;

/**
 * Exportability model for outputting the column's output value
 * @since 4.1
 */
class StrippedValue extends Value {

	/**
	 * Strips all HTML from content. Also, replace <br> with a space for readability.
	 *
	 * @param int $id
	 *
	 * @return string
	 */
	public function get_value( $id ) {
		return strip_tags( str_replace( [ '<br/>', '<br>' ], ' ', parent::get_value( $id ) ) );
	}

}