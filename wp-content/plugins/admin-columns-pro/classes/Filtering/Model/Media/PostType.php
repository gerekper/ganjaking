<?php

namespace ACP\Filtering\Model\Media;

use AC\Column;
use ACP\Search;

/**
 * @deprecated NEWVERSION
 */
class PostType extends Search\Comparison\Media\PostType {

	public function __construct( Column $column ) {
		parent::__construct();
	}

}