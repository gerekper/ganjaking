<?php

namespace ACP\Sorting\Model\Post;

use ACP;
use ACP\Sorting\FormatValue;
use ACP\Sorting\Model\WarningAware;

class AuthorName extends FieldFormat implements WarningAware {

	public function __construct( FormatValue $formatter ) {
		parent::__construct( 'post_author', $formatter );
	}

}