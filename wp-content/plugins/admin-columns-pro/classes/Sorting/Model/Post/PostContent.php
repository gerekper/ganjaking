<?php

namespace ACP\Sorting\Model\Post;

use ACP\Sorting\FormatValue;

class PostContent extends FieldFormat {

	public function __construct() {
		parent::__construct( 'post_content', new FormatValue\StripContent(), null, 200 );
	}

}