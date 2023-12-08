<?php

namespace ACP\Filtering\Model\Comment;

use AC\Column;
use ACP\Search;

/**
 * @deprecated NEWVERSION
 */
class User extends Search\Comparison\Comment\User {

	public function __construct( Column $column ) {
		parent::__construct();
	}

}