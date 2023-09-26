<?php

namespace ACP\Sorting\FormatValue;

use ACP\Sorting\FormatValue;

class ShortCodeCount implements FormatValue {

	public function format_value( $content ) {
		$shortcodes = ac_helper()->string->get_shortcodes( $content );

		return $shortcodes
			? array_sum( $shortcodes )
			: false;
	}

}
