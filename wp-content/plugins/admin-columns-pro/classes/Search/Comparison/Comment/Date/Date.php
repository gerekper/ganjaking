<?php

namespace ACP\Search\Comparison\Comment\Date;

use ACP\Search\Comparison;

class Date extends Comparison\Comment\Date {

	/**
	 * @return string
	 */
	protected function get_field() {
		return 'comment_date';
	}

}