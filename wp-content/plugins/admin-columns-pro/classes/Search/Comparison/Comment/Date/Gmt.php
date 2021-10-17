<?php

namespace ACP\Search\Comparison\Comment\Date;

use ACP\Search\Comparison;

class Gmt extends Comparison\Comment\Date {

	/**
	 * @return string
	 */
	protected function get_field() {
		return 'comment_date_gmt';
	}

}