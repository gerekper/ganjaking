<?php

namespace ACP\Column\User;

use AC;
use ACP\Sorting;

/**
 * @since 2.0
 */
class CommentCount extends AC\Column\User\CommentCount implements Sorting\Sortable {

	public function sorting() {
		return new Sorting\Model\User\CommentCount();
	}

}