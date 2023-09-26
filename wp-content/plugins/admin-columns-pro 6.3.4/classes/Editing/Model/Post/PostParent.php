<?php

namespace ACP\Editing\Model\Post;

use AC\Column;
use ACP\Editing\Service;

/**
 * @deprecated 5.6
 */
class PostParent extends Service\Post\PostParent {

	public function __construct( Column $column ) {
		parent::__construct( $column->get_post_type() );
	}

}