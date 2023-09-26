<?php

namespace ACP\Search\Comparison\Post\Date;

use ACP\Search\Comparison\Post;
use ACP\Search\Operators;

class PostModified extends Post\Date {

	public function operators() {
		return new Operators( [
			Operators::EQ,
			Operators::GT,
			Operators::LT,
			Operators::BETWEEN,
			Operators::TODAY,
			Operators::PAST,
			Operators::LT_DAYS_AGO,
			Operators::GT_DAYS_AGO,
		], false );
	}

	public function get_field() {
		return 'post_modified';
	}

}