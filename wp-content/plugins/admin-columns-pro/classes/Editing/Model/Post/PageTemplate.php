<?php

namespace ACP\Editing\Model\Post;

use AC\Column;
use ACP\Editing\Service;

/**
 * @deprecated 5.6
 */
class PageTemplate extends Service\Post\PageTemplate {

	public function __construct( Column $column ) {
		parent::__construct( $column->get_post_type() );
	}

}