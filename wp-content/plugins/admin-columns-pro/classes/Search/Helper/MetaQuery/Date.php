<?php

namespace ACP\Search\Helper\MetaQuery;

use ACP\Search\Value;

abstract class Date extends Comparison {

	protected function get_date_format_from_type( $type ) {
		if ( $type === Value::INT ) {
			return 'U';
		}

		return 'Y-m-d H:i:s';
	}

}