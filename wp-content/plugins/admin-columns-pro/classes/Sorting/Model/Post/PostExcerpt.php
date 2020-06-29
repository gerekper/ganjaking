<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\FormatValue;
use ACP\Sorting\Model\WarningAware;

class PostExcerpt extends Fields implements WarningAware {

	public function __construct() {
		parent::__construct( [ 'post_excerpt', 'post_content' ], new FormatValue\StripContent() );
	}

}