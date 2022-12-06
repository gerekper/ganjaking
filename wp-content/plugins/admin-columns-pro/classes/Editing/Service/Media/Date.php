<?php

namespace ACP\Editing\Service\Media;

use ACP\Editing\Service\Post;

class Date extends Post\Date {

	protected function is_unsupported_status( $status ) {
		return 'draft' === $status;
	}

}