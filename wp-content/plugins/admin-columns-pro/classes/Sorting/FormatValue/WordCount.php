<?php

namespace ACP\Sorting\FormatValue;

use ACP\Sorting\FormatValue;

class WordCount implements FormatValue {

	public function format_value( $string ) {
		return ac_helper()->string->word_count( $string );
	}

}
