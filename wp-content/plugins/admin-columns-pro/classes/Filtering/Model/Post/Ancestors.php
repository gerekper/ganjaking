<?php

namespace ACP\Filtering\Model\Post;

use ACP\Search;

/**
 * @deprecated NEWVERSION
 */
class Ancestors extends Search\Comparison\Post\Ancestors {

	public function __construct( $column ) {
		parent::__construct();
	}

}