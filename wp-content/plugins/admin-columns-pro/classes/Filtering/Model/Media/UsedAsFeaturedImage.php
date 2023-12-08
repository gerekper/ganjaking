<?php

namespace ACP\Filtering\Model\Media;

use AC\Column;
use ACP\Search;

/**
 * @deprecated NEWVERSION
 */
class UsedAsFeaturedImage extends Search\Comparison\Media\UsedAsFeaturedImage {

	public function __construct( Column $column ) {
		parent::__construct();
	}

}