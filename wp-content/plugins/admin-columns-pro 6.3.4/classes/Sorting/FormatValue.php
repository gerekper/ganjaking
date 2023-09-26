<?php

namespace ACP\Sorting;

interface FormatValue {

	/**
	 * @param mixed $value
	 *
	 * @return string|int|float|bool
	 */
	public function format_value( $value );

}