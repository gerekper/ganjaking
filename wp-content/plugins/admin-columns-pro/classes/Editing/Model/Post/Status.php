<?php

namespace ACP\Editing\Model\Post;

use ACP\Editing\ApplyFilter;
use ACP\Editing\Service\Post\PostStatus;

/**
 * @deprecated 5.6
 */
class Status extends PostStatus {

	public function __construct( $column ) {
		parent::__construct( $column->get_post_type(), new ApplyFilter\PostStatus( $column ) );
	}
}