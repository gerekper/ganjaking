<?php

namespace ACP\Filtering\Model\Comment;

use AC\Column;
use ACP\Search;

/**
 * @deprecated NEWVERSION
 */
class ReplyTo extends Search\Comparison\Comment\ReplyTo {

	public function __construct( Column $column ) {
		parent::__construct();
	}

}